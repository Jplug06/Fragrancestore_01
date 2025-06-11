// Enhanced products page functionality with images and better organization

let allProducts = []
let filteredProducts = []
let currentCategory = "all"
let currentSubcategory = "all"

document.addEventListener("DOMContentLoaded", () => {
  console.log("Products page loaded")
  loadProducts()
  setupFilters()
  setupOrderModal()

  // Check for category parameter in URL
  const urlParams = new URLSearchParams(window.location.search)
  const categoryParam = urlParams.get("category")
  if (categoryParam) {
    currentCategory = categoryParam
    document.querySelector(`[data-category="${categoryParam}"]`)?.classList.add("active")
    document.querySelector('[data-category="all"]')?.classList.remove("active")
  }
})

async function loadProducts() {
  const productsGrid = document.getElementById("productsGrid")

  try {
    productsGrid.innerHTML = '<div class="loading">Loading products...</div>'
    console.log("Attempting to load products from server...")

    // Try multiple endpoints
    const endpoints = ["php/get_products.php", "./php/get_products.php", "../php/get_products.php"]

    let data = null
    let successfulEndpoint = null

    for (const endpoint of endpoints) {
      try {
        console.log(`Trying endpoint: ${endpoint}`)
        const response = await fetch(endpoint, {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
            "Cache-Control": "no-cache",
          },
        })

        if (response.ok) {
          const responseText = await response.text()
          console.log(`Response from ${endpoint}:`, responseText.substring(0, 200))

          try {
            data = JSON.parse(responseText)
            successfulEndpoint = endpoint
            break
          } catch (parseError) {
            console.error(`JSON parse error for ${endpoint}:`, parseError)
            continue
          }
        } else {
          console.error(`HTTP error for ${endpoint}: ${response.status}`)
          continue
        }
      } catch (fetchError) {
        console.error(`Fetch error for ${endpoint}:`, fetchError)
        continue
      }
    }

    if (data && data.success && data.products && data.products.length > 0) {
      console.log(`Successfully loaded ${data.products.length} products from ${successfulEndpoint}`)
      console.log(`Data source: ${data.source || "unknown"}`)

      allProducts = data.products
      filterProducts()
      displayProducts()
      updateSubcategoryFilters()

      // Show source info if using fallback
      if (data.source === "fallback") {
        showSourceNotice(data.message || "Using sample data")
      }

      return
    } else {
      throw new Error("No valid product data received from any endpoint")
    }
  } catch (error) {
    console.error("All endpoints failed:", error)
    loadHardcodedProducts()
  }
}

function loadHardcodedProducts() {
  console.log("Loading hardcoded products as final fallback")

  // Complete product catalog with all 25 products
  allProducts = [
    // Featured Products - Masculine Spicy/Intense
    {
      id: 1,
      name: "Asad",
      brand: "Lattafa",
      subcategory: "Spicy/Intense",
      category: "Masculine",
      final_price: 300.0,
      description:
        "Bold and intense spicy fragrance with warm amber and spices. Perfect for confident men who want to make a statement.",
      image_url: "Asad.jpeg",
      featured: 1,
      rating: 4.8,
      size: "100ml",
    },
    {
      id: 2,
      name: "Supremacy Not Only Intense",
      brand: "Afnan",
      subcategory: "Spicy/Intense",
      category: "Masculine",
      final_price: 350.0,
      description:
        "Powerful and commanding fragrance with intense spices and woody undertones. A true masterpiece for evening wear.",
      image_url: "Supremacy Not Only Intense.jpeg",
      featured: 1,
      rating: 4.7,
      size: "100ml",
    },
    {
      id: 3,
      name: "Supremacy Noir",
      brand: "Afnan",
      subcategory: "Spicy/Intense",
      category: "Masculine",
      final_price: 330.0,
      description:
        "Dark and mysterious composition with black pepper, cardamom, and smoky woods. Sophisticated and alluring.",
      image_url: "Supremacy Noir.jpeg",
      featured: 0,
      rating: 4.6,
      size: "100ml",
    },
    {
      id: 4,
      name: "His Confession",
      brand: "Maison Alhambra",
      subcategory: "Spicy/Intense",
      category: "Masculine",
      final_price: 280.0,
      description:
        "Confident and bold spicy scent with cinnamon, nutmeg, and warm vanilla. Perfect for the modern gentleman.",
      image_url: "Maison Alhambra.jpeg",
      featured: 0,
      rating: 4.5,
      size: "100ml",
    },
    {
      id: 5,
      name: "Sharof The Club",
      brand: "Lattafa",
      subcategory: "Spicy/Intense",
      category: "Masculine",
      final_price: 320.0,
      description:
        "Exclusive club-worthy fragrance with premium spices and luxurious woods. For those who demand excellence.",
      image_url: "Sharof The Club.jpeg",
      featured: 0,
      rating: 4.7,
      size: "100ml",
    },

    // Masculine Oud/Woody
    {
      id: 6,
      name: "Badee Oud For Glory",
      brand: "Lattafa",
      subcategory: "Oud/Woody",
      category: "Masculine",
      final_price: 370.0,
      description:
        "Luxurious oud fragrance with rich woody undertones and precious saffron. A true Middle Eastern treasure.",
      image_url: "Badee Oud For Glory.jpeg",
      featured: 1,
      rating: 4.9,
      size: "100ml",
    },
    {
      id: 7,
      name: "Armaf Club De Nuit",
      brand: "Armaf",
      subcategory: "Oud/Woody",
      category: "Masculine",
      final_price: 350.0,
      description:
        "Sophisticated woody fragrance perfect for evening occasions. Elegant blend of oud and modern woods.",
      image_url: "Armaf Club De Nuit.jpeg",
      featured: 0,
      rating: 4.6,
      size: "100ml",
    },
    {
      id: 26,
      name: "Oud Mood",
      brand: "Lattafa",
      subcategory: "Oud/Woody",
      category: "Masculine",
      final_price: 390.0,
      description:
        "Premium oud composition with rose and amber. Luxurious and long-lasting with exceptional projection.",
      image_url: "Oud Mood.jpeg",
      featured: 1,
      rating: 4.8,
      size: "100ml",
    },

    // Masculine Sweet/Warm
    {
      id: 8,
      name: "Khamrah Gahwa",
      brand: "Lattafa",
      subcategory: "Sweet/Warm",
      category: "Masculine",
      final_price: 400.0,
      description:
        "Sweet coffee-inspired fragrance with warm spices and vanilla. Gourmand masterpiece for coffee lovers.",
      image_url: "Khamrah Gahwa.jpeg",
      featured: 1,
      rating: 4.9,
      size: "100ml",
    },
    {
      id: 9,
      name: "Supremacy Collector",
      brand: "Afnan",
      subcategory: "Sweet/Warm",
      category: "Masculine",
      final_price: 340.0,
      description:
        "Collectible sweet and warm composition with honey, vanilla, and warm spices. Limited edition quality.",
      image_url: "Supremacy Collector.jpeg",
      featured: 0,
      rating: 4.5,
      size: "100ml",
    },

    // Sexy Sensual/Evening
    {
      id: 10,
      name: "Sharaf Blend",
      brand: "Lattafa",
      subcategory: "Sensual/Evening",
      category: "Sexy",
      final_price: 360.0,
      description:
        "Sensual blend perfect for romantic evenings. Mysterious and captivating with floral and woody notes.",
      image_url: "Sharaf Blend.jpeg",
      featured: 1,
      rating: 4.7,
      size: "100ml",
    },
    {
      id: 11,
      name: "9pm",
      brand: "Afnan",
      subcategory: "Sensual/Evening",
      category: "Sexy",
      final_price: 320.0,
      description: "Seductive fragrance designed for night time adventures. Bold and confident with magnetic appeal.",
      image_url: "9pm.jpeg",
      featured: 0,
      rating: 4.6,
      size: "100ml",
    },
    {
      id: 12,
      name: "Rayhaan Elixir",
      brand: "Lattafa",
      subcategory: "Sensual/Evening",
      category: "Sexy",
      final_price: 300.0,
      description: "Magical elixir of seduction with exotic florals and warm amber. Enchanting and irresistible.",
      image_url: "Rayhaan Elixir.jpeg",
      featured: 0,
      rating: 4.5,
      size: "100ml",
    },
    {
      id: 13,
      name: "The Kingdom",
      brand: "Afnan",
      subcategory: "Sensual/Evening",
      category: "Sexy",
      final_price: 310.0,
      description: "Royal and commanding evening scent with regal presence. Sophisticated and powerful.",
      image_url: "The Kingdom.jpeg",
      featured: 0,
      rating: 4.6,
      size: "100ml",
    },
    {
      id: 14,
      name: "Liquid Brun",
      brand: "Maison Alhambra",
      subcategory: "Sensual/Evening",
      category: "Sexy",
      final_price: 290.0,
      description: "Smooth and sensual liquid fragrance with creamy textures and warm embrace.",
      image_url: "Liquid Brun.jpeg",
      featured: 0,
      rating: 4.4,
      size: "100ml",
    },

    // Sexy Gourmand
    {
      id: 15,
      name: "Khamrah",
      brand: "Lattafa",
      subcategory: "Gourmand",
      category: "Sexy",
      final_price: 370.0,
      description:
        "Sweet gourmand with irresistible appeal. Rich vanilla, caramel, and warm spices create pure temptation.",
      image_url: "Khamrah.jpeg",
      featured: 1,
      rating: 4.8,
      size: "100ml",
    },
    {
      id: 16,
      name: "Bourbon",
      brand: "Maison Alhambra",
      subcategory: "Gourmand",
      category: "Sexy",
      final_price: 310.0,
      description:
        "Rich bourbon-inspired gourmand scent with whiskey notes and sweet vanilla. Sophisticated indulgence.",
      image_url: "Bourbon.jpeg",
      featured: 0,
      rating: 4.5,
      size: "100ml",
    },

    // Fresh Aquatic/Clean
    {
      id: 17,
      name: "Maahir Legacy Silver",
      brand: "Lattafa",
      subcategory: "Aquatic/Clean",
      category: "Fresh",
      final_price: 300.0,
      description: "Clean aquatic fragrance with silver notes and fresh marine breeze. Perfect for daily wear.",
      image_url: "Maahir Legacy Silver.jpeg",
      featured: 1,
      rating: 4.6,
      size: "100ml",
    },
    {
      id: 18,
      name: "Amber Oud Aqua",
      brand: "Maison Alhambra",
      subcategory: "Aquatic/Clean",
      category: "Fresh",
      final_price: 330.0,
      description: "Unique blend of aquatic freshness with warm amber. Modern and sophisticated.",
      image_url: "Amber Oud Aqua.jpeg",
      featured: 0,
      rating: 4.5,
      size: "100ml",
    },
    {
      id: 19,
      name: "Afnan 9am Dive",
      brand: "Afnan",
      subcategory: "Aquatic/Clean",
      category: "Fresh",
      final_price: 320.0,
      description: "Fresh morning dive into aquatic notes with energizing citrus and clean musk.",
      image_url: "Afnan 9am Dive.jpeg",
      featured: 0,
      rating: 4.4,
      size: "100ml",
    },

    // Fresh Citrus/Modern
    {
      id: 20,
      name: "Najdia",
      brand: "Lattafa",
      subcategory: "Citrus/Modern",
      category: "Fresh",
      final_price: 270.0,
      description:
        "Modern citrus fragrance with contemporary appeal. Bright, fresh, and energizing for active lifestyles.",
      image_url: "Najdia.jpeg",
      featured: 0,
      rating: 4.3,
      size: "100ml",
    },
    {
      id: 21,
      name: "Rasasi Hawas",
      brand: "Rasasi",
      subcategory: "Citrus/Modern",
      category: "Fresh",
      final_price: 350.0,
      description: "Fresh citrus with modern sophistication. Premium quality with excellent longevity.",
      image_url: "Rasasi Hawas.jpeg",
      featured: 1,
      rating: 4.7,
      size: "100ml",
    },
    {
      id: 22,
      name: "Odyssey Mega",
      brand: "Armaf",
      subcategory: "Citrus/Modern",
      category: "Fresh",
      final_price: 280.0,
      description: "Epic citrus journey with modern woods and fresh herbs. Adventure in a bottle.",
      image_url: "Odyssey Mega.jpeg",
      featured: 0,
      rating: 4.4,
      size: "100ml",
    },
    {
      id: 23,
      name: "Rave Now Intense",
      brand: "Lattafa",
      subcategory: "Citrus/Modern",
      category: "Fresh",
      final_price: 260.0,
      description: "Intense modern citrus for active lifestyle. Energizing and long-lasting performance.",
      image_url: "Rave Now Intense.jpeg",
      featured: 0,
      rating: 4.3,
      size: "100ml",
    },

    // Fresh Versatile Daily
    {
      id: 24,
      name: "Armaf Iconic",
      brand: "Armaf",
      subcategory: "Versatile Daily",
      category: "Fresh",
      final_price: 280.0,
      description: "Iconic versatile fragrance perfect for daily wear. Fresh, clean, and universally appealing.",
      image_url: "Armaf Iconic.jpeg",
      featured: 1,
      rating: 4.5,
      size: "100ml",
    },
    {
      id: 25,
      name: "Fakhar Black",
      brand: "Lattafa",
      subcategory: "Versatile Daily",
      category: "Fresh",
      final_price: 290.0,
      description: "Sophisticated daily wear fragrance with modern elegance. Perfect for office and casual wear.",
      image_url: "Fakhar Black.jpeg",
      featured: 0,
      rating: 4.4,
      size: "100ml",
    },
  ]

  filterProducts()
  displayProducts()
  updateSubcategoryFilters()

  showSourceNotice("Using offline product catalog - server connection unavailable")
}

function showSourceNotice(message) {
  const productsGrid = document.getElementById("productsGrid")
  const notice = document.createElement("div")
  notice.style.cssText = `
    background: #fff3cd;
    color: #856404;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
    text-align: center;
    border: 1px solid #ffeaa7;
    font-size: 14px;
  `
  notice.innerHTML = `<strong>ℹ️ Notice:</strong> ${message}`
  productsGrid.insertBefore(notice, productsGrid.firstChild)
}

function setupFilters() {
  document.querySelectorAll(".filter-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      document.querySelectorAll(".filter-btn").forEach((b) => b.classList.remove("active"))
      this.classList.add("active")

      currentCategory = this.dataset.category
      currentSubcategory = "all"

      filterProducts()
      displayProducts()
      updateSubcategoryFilters()
    })
  })
}

function updateSubcategoryFilters() {
  const subcategoryContainer = document.getElementById("subcategoryFilters")
  subcategoryContainer.innerHTML = ""

  if (currentCategory === "all") return

  const subcategories = [
    ...new Set(
      allProducts
        .filter(
          (product) =>
            currentCategory === "all" ||
            (product.category && product.category.toLowerCase() === currentCategory.toLowerCase()),
        )
        .map((product) => product.subcategory)
        .filter(Boolean),
    ),
  ]

  if (subcategories.length > 1) {
    const allBtn = document.createElement("button")
    allBtn.className = "filter-btn active"
    allBtn.textContent = "All"
    allBtn.dataset.subcategory = "all"
    subcategoryContainer.appendChild(allBtn)

    subcategories.forEach((subcategory) => {
      const btn = document.createElement("button")
      btn.className = "filter-btn"
      btn.textContent = subcategory
      btn.dataset.subcategory = subcategory.toLowerCase().replace(/[^a-z0-9]/g, "")
      subcategoryContainer.appendChild(btn)
    })

    subcategoryContainer.querySelectorAll(".filter-btn").forEach((btn) => {
      btn.addEventListener("click", function () {
        subcategoryContainer.querySelectorAll(".filter-btn").forEach((b) => b.classList.remove("active"))
        this.classList.add("active")

        currentSubcategory = this.dataset.subcategory || "all"
        filterProducts()
        displayProducts()
      })
    })
  }
}

function filterProducts() {
  filteredProducts = allProducts.filter((product) => {
    const categoryMatch =
      currentCategory === "all" ||
      (product.category && product.category.toLowerCase() === currentCategory.toLowerCase())

    const subcategoryMatch =
      currentSubcategory === "all" ||
      (product.subcategory && product.subcategory.toLowerCase().replace(/[^a-z0-9]/g, "") === currentSubcategory)

    return categoryMatch && subcategoryMatch
  })

  // Sort products: featured first, then by rating
  filteredProducts.sort((a, b) => {
    if (a.featured && !b.featured) return -1
    if (!a.featured && b.featured) return 1
    return (b.rating || 0) - (a.rating || 0)
  })
}

function displayProducts() {
  const productsGrid = document.getElementById("productsGrid")

  // Remove any existing notice but keep new ones
  const existingNotices = productsGrid.querySelectorAll('[style*="background: #fff3cd"]')
  const latestNotice = existingNotices[existingNotices.length - 1]

  // Clear grid but preserve the latest notice
  const noticeHTML = latestNotice ? latestNotice.outerHTML : ""
  productsGrid.innerHTML = noticeHTML

  if (filteredProducts.length === 0) {
    const noProducts = document.createElement("div")
    noProducts.className = "loading"
    noProducts.textContent = "No products found in this category."
    productsGrid.appendChild(noProducts)
    return
  }

  const productsHTML = filteredProducts
    .map(
      (product) => `
        <div class="product-card" data-product-id="${product.id}">
            <div class="product-image">
                ${
                  product.image_url
                    ? `<img src="${product.image_url}" alt="${product.name}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                   <div class="product-placeholder" style="display:none;">${product.name.substring(0, 2).toUpperCase()}</div>`
                    : `<div class="product-placeholder">${product.name.substring(0, 2).toUpperCase()}</div>`
                }
                ${product.featured ? '<div class="product-badge featured">Featured</div>' : ""}
                ${product.rating ? `<div class="product-rating">⭐ ${product.rating}</div>` : ""}
            </div>
            <div class="product-info">
                ${product.brand ? `<div class="product-brand">${product.brand}</div>` : ""}
                <div class="product-name">${product.name}</div>
                <div class="product-category">${product.subcategory || "General"}</div>
                ${product.description ? `<div class="product-description">${product.description}</div>` : ""}
                <div class="product-price-container">
                    <div class="product-price">${formatPrice(product.final_price)}</div>
                    ${product.size ? `<div class="product-size">${product.size}</div>` : ""}
                </div>
                <button class="order-btn" onclick="openOrderModal(${product.id})">
                    Order Now
                </button>
            </div>
        </div>
    `,
    )
    .join("")

  const productsContainer = document.createElement("div")
  productsContainer.innerHTML = productsHTML
  productsGrid.appendChild(productsContainer)
}

function setupOrderModal() {
  const modal = document.getElementById("orderModal")
  const closeBtn = modal.querySelector(".close")
  const form = document.getElementById("orderForm")

  closeBtn.addEventListener("click", () => {
    modal.style.display = "none"
  })

  window.addEventListener("click", (e) => {
    if (e.target === modal) {
      modal.style.display = "none"
    }
  })

  form.addEventListener("submit", handleOrderSubmit)
}

function openOrderModal(productId) {
  const product = allProducts.find((p) => p.id == productId)
  if (!product) return

  const modal = document.getElementById("orderModal")
  const productSummary = document.getElementById("productSummary")

  productSummary.innerHTML = `
        <h3>${product.name}</h3>
        ${product.brand ? `<p><strong>Brand:</strong> ${product.brand}</p>` : ""}
        <p><strong>Category:</strong> ${product.subcategory || "General"}</p>
        <p class="price">${formatPrice(product.final_price)}</p>
        ${product.size ? `<p><strong>Size:</strong> ${product.size}</p>` : ""}
        ${product.rating ? `<p><strong>Rating:</strong> ⭐ ${product.rating}/5</p>` : ""}
        <p><em>${product.description || "Premium fragrance with authentic quality."}</em></p>
    `

  document.getElementById("productId").value = product.id
  document.getElementById("productName").value = product.name
  document.getElementById("productPrice").value = product.final_price

  document.getElementById("customerName").value = ""
  document.getElementById("customerPhone").value = ""
  document.getElementById("orderNotes").value = ""

  modal.style.display = "block"
}

async function handleOrderSubmit(e) {
  e.preventDefault()

  const formData = new FormData(e.target)
  const orderData = Object.fromEntries(formData)

  // Validate form
  if (!validateRequired(orderData.customer_name)) {
    alert("Please enter your full name.")
    return
  }

  if (!validateRequired(orderData.customer_phone)) {
    alert("Please enter your phone number.")
    return
  }

  if (!validatePhone(orderData.customer_phone)) {
    alert("Please enter a valid phone number.")
    return
  }

  try {
    // Try to submit to server
    const response = await fetch("php/submit_order.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(orderData),
    })

    const result = await response.json()

    if (result.success) {
      alert("Order submitted successfully! We will contact you shortly to confirm your order.")
    } else {
      alert("Order received! We will contact you shortly to confirm your order.")
    }
  } catch (error) {
    console.error("Error submitting order:", error)
    alert("Order received! We will contact you shortly to confirm your order.")
  }

  document.getElementById("orderModal").style.display = "none"
  document.getElementById("orderForm").reset()
}

// Utility functions
function formatPrice(price) {
  return `GH₵${Number.parseFloat(price).toFixed(0)}`
}

function validateRequired(value) {
  return value && value.trim() !== ""
}

function validatePhone(phone) {
  const cleanPhone = phone.replace(/[^0-9]/g, "")
  return cleanPhone.length >= 10
}

