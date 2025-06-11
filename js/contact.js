// Contact page functionality

document.addEventListener("DOMContentLoaded", () => {
  const contactForm = document.getElementById("contactForm")
  const messageDiv = document.getElementById("contactFormMessage")

  // Helper functions
  function validateRequired(input) {
    return input !== null && input !== undefined && input.trim() !== ""
  }

  function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return emailRegex.test(email)
  }

  function showMessage(messageDiv, message, type) {
    messageDiv.textContent = message
    messageDiv.className = "contact-form-message " + type // Add type as class for styling
  }

  if (contactForm) {
    contactForm.addEventListener("submit", handleContactSubmit)
  }

  async function handleContactSubmit(e) {
    e.preventDefault()

    const formData = new FormData(e.target)
    const contactData = Object.fromEntries(formData)

    // Validate form
    if (!validateRequired(contactData.name)) {
      showMessage(messageDiv, "Please enter your full name.", "error")
      return
    }

    if (!validateRequired(contactData.email)) {
      showMessage(messageDiv, "Please enter your email address.", "error")
      return
    }

    if (!validateEmail(contactData.email)) {
      showMessage(messageDiv, "Please enter a valid email address.", "error")
      return
    }

    if (!validateRequired(contactData.subject)) {
      showMessage(messageDiv, "Please enter a subject.", "error")
      return
    }

    if (!validateRequired(contactData.message)) {
      showMessage(messageDiv, "Please enter your message.", "error")
      return
    }

    try {
      const response = await fetch("php/submit_contact.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(contactData),
      })

      const result = await response.json()

      if (result.success) {
        showMessage(messageDiv, "Thank you for your message! We will get back to you within 24 hours.", "success")
        contactForm.reset()
      } else {
        showMessage(messageDiv, "Failed to send message: " + result.message, "error")
      }
    } catch (error) {
      console.error("Error submitting contact form:", error)
      showMessage(messageDiv, "Failed to send message. Please try again.", "error")
    }
  }
})
