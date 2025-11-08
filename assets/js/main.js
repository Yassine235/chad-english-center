/**
 * CHAD ENGLISH CENTER - JavaScript
 *
 * Fonctionnalités :
 * - Menu mobile toggle
 * - Smooth scroll
 * - Active link highlighting
 */

// Menu mobile toggle
document.addEventListener("DOMContentLoaded", () => {
  const navToggle = document.getElementById("navToggle")
  const navMenu = document.getElementById("navMenu")

  if (navToggle && navMenu) {
    navToggle.addEventListener("click", () => {
      navMenu.classList.toggle("active")

      // Animation du burger
      const spans = navToggle.querySelectorAll("span")
      if (navMenu.classList.contains("active")) {
        spans[0].style.transform = "rotate(45deg) translate(5px, 5px)"
        spans[1].style.opacity = "0"
        spans[2].style.transform = "rotate(-45deg) translate(7px, -6px)"
      } else {
        spans[0].style.transform = "none"
        spans[1].style.opacity = "1"
        spans[2].style.transform = "none"
      }
    })

    // Fermer le menu au clic sur un lien
    const navLinks = navMenu.querySelectorAll(".nav-link")
    navLinks.forEach((link) => {
      link.addEventListener("click", () => {
        if (window.innerWidth <= 768) {
          navMenu.classList.remove("active")
          const spans = navToggle.querySelectorAll("span")
          spans[0].style.transform = "none"
          spans[1].style.opacity = "1"
          spans[2].style.transform = "none"
        }
      })
    })
  }

  // Smooth scroll pour les ancres
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      const href = this.getAttribute("href")
      if (href !== "#" && href !== "") {
        e.preventDefault()
        const target = document.querySelector(href)
        if (target) {
          target.scrollIntoView({
            behavior: "smooth",
            block: "start",
          })
        }
      }
    })
  })
})
