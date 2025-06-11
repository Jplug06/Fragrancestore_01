// Main JavaScript file for common functionality

document.addEventListener("DOMContentLoaded", () => {
  // Mobile menu toggle
  const hamburger = document.querySelector(".hamburger")
  const navMenu = document.querySelector(".nav-menu")

  if (hamburger && navMenu) {
    hamburger.addEventListener("click", () => {
      hamburger.classList.toggle("active")
      navMenu.classList.toggle("active")
    })

    // Close mobile menu when clicking on a link
    document.querySelectorAll(".nav-menu a").forEach((link) => {
      link.addEventListener("click", () => {
        hamburger.classList.remove("active")
        navMenu.classList.remove("active")
      })
    })
  }

  // Smooth scrolling for anchor links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault()
      const target = document.querySelector(this.getAttribute("href"))
      if (target) {
        target.scrollIntoView({
          behavior: "smooth",
          block: "start",
        })
      }
    })
  })

  // Add loading animation to forms
  const forms = document.querySelectorAll("form")
  forms.forEach((form) => {
    form.addEventListener("submit", () => {
      const submitBtn = form.querySelector('button[type="submit"]')
      if (submitBtn) {
        const originalText = submitBtn.textContent
        submitBtn.textContent = "Processing..."
        submitBtn.disabled = true

        // Re-enable button after 3 seconds (fallback)
        setTimeout(() => {
          submitBtn.textContent = originalText
          submitBtn.disabled = false
        }, 3000)
      }
    })
  })

  // Add fade-in animation to elements when they come into view
  const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -50px 0px",
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("fade-in")
        observer.unobserve(entry.target)
      }
    })
  }, observerOptions)

  // Observe elements for animation
  document.querySelectorAll(".category-card, .feature, .product-card, .faq-item").forEach((el) => {
    observer.observe(el)
  })
})

// Utility functions
function showMessage(element, message, type = "success") {
  if (!element) return

  element.textContent = message
  element.className = `form-message ${type}`
  element.style.display = "block"

  // Auto-hide after 5 seconds
  setTimeout(() => {
    element.style.display = "none"
  }, 5000)
}

function formatPrice(price) {
  return `GH₵${Number.parseFloat(price).toFixed(0)}`
}

function formatDate(dateString) {
  const date = new Date(dateString)
  return date.toLocaleDateString("en-GB", {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  })
}

// API helper functions
async function makeRequest(url, options = {}) {
  try {
    const response = await fetch(url, {
      headers: {
        "Content-Type": "application/json",
        ...options.headers,
      },
      ...options,
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.message || "Request failed")
    }

    return data
  } catch (error) {
    console.error("Request error:", error)
    throw error
  }
}

// Form validation helpers
function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return re.test(email)
}

function validatePhone(phone) {
  const re = /^[+]?[0-9\s\-$$$$]{10,}$/
  return re.test(phone.replace(/\s/g, ""))
}

function validateRequired(value) {
  return value && value.trim().length > 0
}
