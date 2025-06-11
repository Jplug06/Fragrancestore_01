// Admin login functionality

document.addEventListener("DOMContentLoaded", () => {
  // Check if already logged in
  if (sessionStorage.getItem("adminLoggedIn") === "true") {
    window.location.href = "admin-dashboard.html"
    return
  }

  const loginForm = document.getElementById("adminLoginForm")
  const messageDiv = document.getElementById("loginMessage")

  // Helper function to validate required fields
  function validateRequired(value) {
    return value && value.trim() !== ""
  }

  // Helper function to display messages
  function showMessage(element, message, type) {
    if (!element) return
    element.textContent = message
    element.className = `form-message ${type}`
    element.style.display = "block"
  }

  if (loginForm) {
    loginForm.addEventListener("submit", handleLogin)
  }

  async function handleLogin(e) {
    e.preventDefault()

    const formData = new FormData(e.target)
    const loginData = Object.fromEntries(formData)

    // Validate form
    if (!validateRequired(loginData.username)) {
      showMessage(messageDiv, "Please enter your username.", "error")
      return
    }

    if (!validateRequired(loginData.password)) {
      showMessage(messageDiv, "Please enter your password.", "error")
      return
    }

    // For demo purposes - hardcoded admin credentials
    if (loginData.username === "admin" && loginData.password === "admin123") {
      sessionStorage.setItem("adminLoggedIn", "true")
      sessionStorage.setItem("adminUsername", loginData.username)
      showMessage(messageDiv, "Login successful! Redirecting...", "success")

      setTimeout(() => {
        window.location.href = "admin-dashboard.html"
      }, 1500)
      return
    }

    try {
      const response = await fetch("php/admin_login.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(loginData),
      })

      const result = await response.json()

      if (result.success) {
        sessionStorage.setItem("adminLoggedIn", "true")
        sessionStorage.setItem("adminUsername", loginData.username)
        showMessage(messageDiv, "Login successful! Redirecting...", "success")

        setTimeout(() => {
          window.location.href = "admin-dashboard.html"
        }, 1500)
      } else {
        showMessage(messageDiv, "Invalid username or password.", "error")
      }
    } catch (error) {
      console.error("Error during login:", error)
      showMessage(messageDiv, "Login failed. Please try again.", "error")
    }
  }
})
