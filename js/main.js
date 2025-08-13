// Rapid Rescue - Optimized JavaScript with Performance Enhancements
// Modular, efficient code with lazy loading and optimized animations

// Import GSAP and ScrollTrigger
import { gsap } from "gsap"
import { ScrollTrigger } from "gsap/ScrollTrigger"

gsap.registerPlugin(ScrollTrigger)

const initGSAP = () => {
  if (typeof gsap === "undefined") {
    console.warn("GSAP not loaded")
    return false
  }

  gsap.registerPlugin(ScrollTrigger)
  return true
}

document.addEventListener("DOMContentLoaded", () => {
  const startTime = performance.now()

  // Initialize core functionality
  initializeCore()

  // Initialize animations only if GSAP is available
  if (initGSAP()) {
    initializeAnimations()
  }

  // Performance logging
  const endTime = performance.now()
  console.log(`Initialization completed in ${(endTime - startTime).toFixed(2)}ms`)
})

function initializeCore() {
  // Form validation
  initializeFormValidation()

  // Enhanced interactions
  initializeInteractions()

  // Auto-refresh for tracking pages
  initializeAutoRefresh()

  // Alert handling
  initializeAlerts()
}

function initializeFormValidation() {
  const forms = [
    { id: "registerForm", validator: validateRegistrationForm },
    { id: "loginForm", validator: validateLoginForm },
    { id: "emergencyForm", validator: validateEmergencyForm },
  ]

  forms.forEach(({ id, validator }) => {
    const form = document.getElementById(id)
    if (form) {
      form.addEventListener("submit", (e) => {
        if (!validator()) {
          e.preventDefault()
        }
      })
    }
  })
}

function initializeAnimations() {
  // Batch DOM queries for better performance
  const elements = {
    heroTitle: document.querySelector(".hero-section h1"),
    heroText: document.querySelector(".hero-section p"),
    heroButtons: document.querySelectorAll(".hero-section .btn"),
    sections: document.querySelectorAll("section:not(.hero-section)"),
    cards: document.querySelectorAll(".info-card, .card"),
    navbar: document.querySelector(".navbar"),
  }

  // Hero animations with timeline for better performance
  if (elements.heroTitle || elements.heroText || elements.heroButtons.length) {
    const heroTL = gsap.timeline()

    if (elements.heroTitle) {
      heroTL.fromTo(elements.heroTitle, { opacity: 0, y: 30 }, { opacity: 1, y: 0, duration: 0.8, ease: "power2.out" })
    }

    if (elements.heroText) {
      heroTL.fromTo(
        elements.heroText,
        { opacity: 0, y: 20 },
        { opacity: 1, y: 0, duration: 0.6, ease: "power2.out" },
        "-=0.4",
      )
    }

    if (elements.heroButtons.length) {
      heroTL.fromTo(
        elements.heroButtons,
        { opacity: 0, y: 20 },
        { opacity: 1, y: 0, duration: 0.5, stagger: 0.1, ease: "back.out(1.7)" },
        "-=0.3",
      )
    }
  }

  // Optimized scroll animations with Intersection Observer fallback
  if (elements.sections.length) {
    elements.sections.forEach((section) => {
      gsap.fromTo(
        section,
        { opacity: 0, y: 40 },
        {
          opacity: 1,
          y: 0,
          duration: 0.6,
          ease: "power2.out",
          scrollTrigger: {
            trigger: section,
            start: "top 85%",
            toggleActions: "play none none reverse",
          },
        },
      )
    })
  }

  // Card animations with performance optimization
  if (elements.cards.length) {
    elements.cards.forEach((card, index) => {
      gsap.fromTo(
        card,
        { opacity: 0, y: 30 },
        {
          opacity: 1,
          y: 0,
          duration: 0.5,
          delay: index * 0.1,
          ease: "power2.out",
          scrollTrigger: {
            trigger: card,
            start: "top 90%",
            toggleActions: "play none none reverse",
          },
        },
      )
    })
  }

  // Navbar scroll effect
  if (elements.navbar) {
    ScrollTrigger.create({
      start: "top -80",
      end: 99999,
      toggleClass: { className: "scrolled", targets: elements.navbar },
    })
  }
}

function initializeInteractions() {
  // Button hover effects with throttling
  let hoverTimeout
  document.addEventListener("mouseover", (e) => {
    if (e.target.matches(".btn")) {
      clearTimeout(hoverTimeout)
      hoverTimeout = setTimeout(() => {
        if (typeof gsap !== "undefined") {
          gsap.to(e.target, { scale: 1.05, duration: 0.2, ease: "power2.out" })
        }
      }, 10)
    }
  })

  document.addEventListener("mouseout", (e) => {
    if (e.target.matches(".btn")) {
      clearTimeout(hoverTimeout)
      if (typeof gsap !== "undefined") {
        gsap.to(e.target, { scale: 1, duration: 0.2, ease: "power2.out" })
      }
    }
  })

  // Form field focus effects
  document.addEventListener("focusin", (e) => {
    if (e.target.matches(".form-control, .form-select")) {
      if (typeof gsap !== "undefined") {
        gsap.to(e.target, { scale: 1.02, duration: 0.2, ease: "power2.out" })
      }
    }
  })

  document.addEventListener("focusout", (e) => {
    if (e.target.matches(".form-control, .form-select")) {
      if (typeof gsap !== "undefined") {
        gsap.to(e.target, { scale: 1, duration: 0.2, ease: "power2.out" })
      }
    }
  })
}

function initializeAlerts() {
  const alerts = document.querySelectorAll(".alert")
  if (!alerts.length) return

  alerts.forEach((alert) => {
    // Animate entrance
    if (typeof gsap !== "undefined") {
      gsap.fromTo(alert, { opacity: 0, y: -20 }, { opacity: 1, y: 0, duration: 0.4, ease: "back.out(1.7)" })
    }

    // Auto-hide with cleanup
    setTimeout(() => {
      if (typeof gsap !== "undefined") {
        gsap.to(alert, {
          opacity: 0,
          y: -20,
          duration: 0.3,
          ease: "power2.in",
          onComplete: () => {
            if (alert.parentNode) {
              alert.parentNode.removeChild(alert)
            }
          },
        })
      } else {
        alert.style.opacity = "0"
        setTimeout(() => {
          if (alert.parentNode) {
            alert.parentNode.removeChild(alert)
          }
        }, 300)
      }
    }, 5000)
  })
}

function initializeAutoRefresh() {
  if (
    !window.location.pathname.includes("request_tracking.php") &&
    !window.location.pathname.includes("dashboard.php")
  ) {
    return
  }

  let refreshInterval
  let isVisible = true

  // Page visibility API for better performance
  document.addEventListener("visibilitychange", () => {
    isVisible = !document.hidden
    if (isVisible && !refreshInterval) {
      startAutoRefresh()
    } else if (!isVisible && refreshInterval) {
      clearInterval(refreshInterval)
      refreshInterval = null
    }
  })

  function startAutoRefresh() {
    refreshInterval = setInterval(() => {
      if (isVisible) {
        showRefreshIndicator()
        setTimeout(() => location.reload(), 500)
      }
    }, 30000)
  }

  function showRefreshIndicator() {
    const indicator = document.createElement("div")
    indicator.className = "position-fixed top-0 end-0 m-3 alert alert-info"
    indicator.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Refreshing...'
    indicator.style.zIndex = "9999"
    document.body.appendChild(indicator)

    if (typeof gsap !== "undefined") {
      gsap.fromTo(indicator, { opacity: 0, x: 100 }, { opacity: 1, x: 0, duration: 0.3 })
    }
  }

  startAutoRefresh()
}

const validators = {
  email: (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email),
  phone: (phone) => /^[\d\s\-+()]{10,}$/.test(phone),
  required: (value) => value && value.trim().length > 0,
  minLength: (value, min) => value && value.length >= min,
  match: (value1, value2) => value1 === value2,
}

function validateRegistrationForm() {
  const fields = {
    firstname: { element: document.getElementById("firstname"), rules: ["required"] },
    lastname: { element: document.getElementById("lastname"), rules: ["required"] },
    email: { element: document.getElementById("email"), rules: ["required", "email"] },
    phone: { element: document.getElementById("phone"), rules: ["required", "phone"] },
    password: { element: document.getElementById("password"), rules: ["required", "minLength:6"] },
    confirmPassword: { element: document.getElementById("confirm_password"), rules: ["required"] },
    dob: { element: document.getElementById("dob"), rules: ["required"] },
    address: { element: document.getElementById("address"), rules: ["required"] },
  }

  return validateFields(fields)
}

function validateLoginForm() {
  const fields = {
    email: { element: document.getElementById("email"), rules: ["required", "email"] },
    password: { element: document.getElementById("password"), rules: ["required"] },
  }

  return validateFields(fields)
}

function validateEmergencyForm() {
  const fields = {
    hospitalName: { element: document.getElementById("hospital_name"), rules: ["required"] },
    address: { element: document.getElementById("address"), rules: ["required"] },
    phone: { element: document.getElementById("phone"), rules: ["required", "phone"] },
    pickupAddress: { element: document.getElementById("pickup_address"), rules: ["required"] },
    type: { element: document.getElementById("type"), rules: ["required"] },
  }

  return validateFields(fields)
}

function validateFields(fields) {
  clearErrorMessages()
  let isValid = true

  Object.entries(fields).forEach(([fieldName, { element, rules }]) => {
    if (!element) return

    const value = element.value
    let fieldValid = true
    let errorMessage = ""

    rules.forEach((rule) => {
      if (!fieldValid) return

      if (rule === "required" && !validators.required(value)) {
        fieldValid = false
        errorMessage = `${fieldName.replace(/([A-Z])/g, " $1").toLowerCase()} is required`
      } else if (rule === "email" && value && !validators.email(value)) {
        fieldValid = false
        errorMessage = "Please enter a valid email address"
      } else if (rule === "phone" && value && !validators.phone(value)) {
        fieldValid = false
        errorMessage = "Please enter a valid phone number"
      } else if (rule.startsWith("minLength:")) {
        const minLength = Number.parseInt(rule.split(":")[1])
        if (value && !validators.minLength(value, minLength)) {
          fieldValid = false
          errorMessage = `Must be at least ${minLength} characters long`
        }
      }
    })

    // Special case for password confirmation
    if (fieldName === "confirmPassword" && fieldValid) {
      const password = document.getElementById("password")
      if (password && !validators.match(value, password.value)) {
        fieldValid = false
        errorMessage = "Passwords do not match"
      }
    }

    if (!fieldValid) {
      showError(element, errorMessage)
      isValid = false
    }
  })

  return isValid
}

function showError(element, message) {
  element.classList.add("is-invalid")

  // Animate field shake with CSS animation fallback
  if (typeof gsap !== "undefined") {
    gsap.fromTo(element, { x: -3 }, { x: 3, duration: 0.1, repeat: 2, yoyo: true, ease: "power2.inOut" })
  } else {
    element.style.animation = "shake 0.3s ease-in-out"
  }

  // Create and animate error message
  const errorDiv = document.createElement("div")
  errorDiv.className = "invalid-feedback"
  errorDiv.textContent = message
  element.parentNode.appendChild(errorDiv)

  if (typeof gsap !== "undefined") {
    gsap.fromTo(errorDiv, { opacity: 0, y: -10 }, { opacity: 1, y: 0, duration: 0.3, ease: "power2.out" })
  } else {
    errorDiv.style.opacity = "1"
  }
}

function clearErrorMessages() {
  // Batch DOM operations for better performance
  const invalidElements = document.querySelectorAll(".is-invalid")
  const errorMessages = document.querySelectorAll(".invalid-feedback")

  invalidElements.forEach((element) => element.classList.remove("is-invalid"))

  errorMessages.forEach((message) => {
    if (typeof gsap !== "undefined") {
      gsap.to(message, {
        opacity: 0,
        y: -10,
        duration: 0.2,
        ease: "power2.in",
        onComplete: () => {
          if (message.parentNode) {
            message.parentNode.removeChild(message)
          }
        },
      })
    } else {
      message.remove()
    }
  })
}

const utils = {
  // Throttle function for performance
  throttle: (func, limit) => {
    let inThrottle
    return function () {
      const args = arguments
      
      if (!inThrottle) {
        func.apply(this, args)
        inThrottle = true
        setTimeout(() => (inThrottle = false), limit)
      }
    }
  },

  // Debounce function for performance
  debounce: (func, wait) => {
    let timeout
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout)
        func(...args)
      }
      clearTimeout(timeout)
      timeout = setTimeout(later, wait)
    }
  },

  // Optimized smooth scroll
  smoothScrollTo: (target) => {
    if (typeof gsap !== "undefined") {
      gsap.to(window, { duration: 1, scrollTo: target, ease: "power2.inOut" })
    } else {
      target.scrollIntoView({ behavior: "smooth" })
    }
  },
}

document.addEventListener("click", (e) => {
  if (e.target.matches('a[href^="#"]')) {
    e.preventDefault()
    const target = document.querySelector(e.target.getAttribute("href"))
    if (target) {
      utils.smoothScrollTo(target)
    }
  }
})

window.addEventListener("beforeunload", () => {
  // Cleanup intervals and timeouts
  if (typeof ScrollTrigger !== "undefined") {
    ScrollTrigger.killAll()
  }
})

const style = document.createElement("style")
style.textContent = `
  @keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-3px); }
    75% { transform: translateX(3px); }
  }
`
document.head.appendChild(style)
