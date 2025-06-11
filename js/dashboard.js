// Admin dashboard functionality

let currentOrderId = null
let currentMessageId = null

document.addEventListener("DOMContentLoaded", () => {
  // Check if logged in
  if (sessionStorage.getItem("adminLoggedIn") !== "true") {
    window.location.href = "admin.html"
    return
  }

  setupDashboard()
  loadDashboardData()
  setupModals()
  setupFilters()

  // Logout functionality
  document.getElementById("logoutBtn").addEventListener("click", (e) => {
    e.preventDefault()
    sessionStorage.removeItem("adminLoggedIn")
    sessionStorage.removeItem("adminUsername")
    window.location.href = "admin.html"
  })
})

function setupDashboard() {
  // Tab switching
  document.querySelectorAll(".tab-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      const tabName = this.dataset.tab

      // Update tab buttons
      document.querySelectorAll(".tab-btn").forEach((b) => b.classList.remove("active"))
      this.classList.add("active")

      // Update tab content
      document.querySelectorAll(".tab-pane").forEach((pane) => pane.classList.remove("active"))
      document.getElementById(tabName + "Tab").classList.add("active")

      // Load data for the active tab
      if (tabName === "orders") {
        loadOrders()
      } else if (tabName === "messages") {
        loadMessages()
      }
    })
  })
}

async function loadDashboardData() {
  try {
    const response = await fetch("php/get_dashboard_stats.php")
    const data = await response.json()

    if (data.success) {
      document.getElementById("totalOrders").textContent = data.stats.total_orders
      document.getElementById("pendingOrders").textContent = data.stats.pending_orders
      document.getElementById("totalMessages").textContent = data.stats.total_messages
      document.getElementById("unreadMessages").textContent = data.stats.unread_messages
    }
  } catch (error) {
    console.error("Error loading dashboard stats:", error)
  }

  // Load initial data
  loadOrders()
}

async function loadOrders(status = "all") {
  try {
    const response = await fetch(`php/get_orders.php?status=${status}`)
    const data = await response.json()

    if (data.success) {
      displayOrders(data.orders)
    } else {
      console.error("Failed to load orders:", data.message)
    }
  } catch (error) {
    console.error("Error loading orders:", error)
  }
}

async function loadMessages(status = "all") {
  try {
    const response = await fetch(`php/get_messages.php?status=${status}`)
    const data = await response.json()

    if (data.success) {
      displayMessages(data.messages)
    } else {
      console.error("Failed to load messages:", data.message)
    }
  } catch (error) {
    console.error("Error loading messages:", error)
  }
}

function formatPrice(price) {
  return `GH₵${Number.parseFloat(price).toFixed(0)}`
}

function formatDate(dateString) {
  const date = new Date(dateString)
  const day = String(date.getDate()).padStart(2, "0")
  const month = String(date.getMonth() + 1).padStart(2, "0") // Month is 0-indexed
  const year = date.getFullYear()
  return `${month}/${day}/${year}`
}

function displayOrders(orders) {
  const tbody = document.querySelector("#ordersTable tbody")

  if (orders.length === 0) {
    tbody.innerHTML = '<tr><td colspan="8" class="text-center">No orders found</td></tr>'
    return
  }

  tbody.innerHTML = orders
    .map(
      (order) => `
        <tr>
            <td>#${order.id}</td>
            <td>${order.customer_name}</td>
            <td>${order.customer_phone}</td>
            <td>${order.product_name}</td>
            <td>${formatPrice(order.product_price)}</td>
            <td><span class="status-badge status-${order.order_status}">${order.order_status}</span></td>
            <td>${formatDate(order.order_date)}</td>
            <td>
                <button class="action-btn btn-view" onclick="viewOrder(${order.id})">View</button>
                <button class="action-btn btn-delete" onclick="deleteOrder(${order.id})">Delete</button>
            </td>
        </tr>
    `,
    )
    .join("")
}

function displayMessages(messages) {
  const tbody = document.querySelector("#messagesTable tbody")

  if (messages.length === 0) {
    tbody.innerHTML = '<tr><td colspan="7" class="text-center">No messages found</td></tr>'
    return
  }

  tbody.innerHTML = messages
    .map(
      (message) => `
        <tr>
            <td>#${message.id}</td>
            <td>${message.name}</td>
            <td>${message.email}</td>
            <td>${message.subject}</td>
            <td><span class="status-badge status-${message.status}">${message.status}</span></td>
            <td>${formatDate(message.created_at)}</td>
            <td>
                <button class="action-btn btn-view" onclick="viewMessage(${message.id})">View</button>
                <button class="action-btn btn-delete" onclick="deleteMessage(${message.id})">Delete</button>
            </td>
        </tr>
    `,
    )
    .join("")
}

function setupModals() {
  // Order modal
  const orderModal = document.getElementById("orderModal")
  const messageModal = document.getElementById("messageModal")

  // Close buttons
  document.querySelectorAll(".close").forEach((closeBtn) => {
    closeBtn.addEventListener("click", function () {
      this.closest(".modal").style.display = "none"
    })
  })

  // Click outside to close
  window.addEventListener("click", (e) => {
    if (e.target.classList.contains("modal")) {
      e.target.style.display = "none"
    }
  })

  // Update order status
  document.getElementById("updateOrderStatus").addEventListener("click", updateOrderStatus)

  // Update message status
  document.getElementById("updateMessageStatus").addEventListener("click", updateMessageStatus)
}

function setupFilters() {
  // Order status filter
  document.getElementById("orderStatusFilter").addEventListener("change", function () {
    loadOrders(this.value)
  })

  // Message status filter
  document.getElementById("messageStatusFilter").addEventListener("change", function () {
    loadMessages(this.value)
  })
}

async function viewOrder(orderId) {
  try {
    const response = await fetch(`php/get_order_details.php?id=${orderId}`)
    const data = await response.json()

    if (data.success) {
      const order = data.order
      currentOrderId = orderId

      document.getElementById("orderDetails").innerHTML = `
                <div class="order-info">
                    <h3>Order #${order.id}</h3>
                    <p><strong>Customer:</strong> ${order.customer_name}</p>
                    <p><strong>Phone:</strong> ${order.customer_phone}</p>
                    <p><strong>Product:</strong> ${order.product_name}</p>
                    <p><strong>Price:</strong> ${formatPrice(order.product_price)}</p>
                    <p><strong>Order Date:</strong> ${formatDate(order.order_date)}</p>
                    <p><strong>Current Status:</strong> <span class="status-badge status-${order.order_status}">${order.order_status}</span></p>
                    ${order.notes ? `<p><strong>Notes:</strong> ${order.notes}</p>` : ""}
                </div>
            `

      document.getElementById("orderStatusUpdate").value = order.order_status
      document.getElementById("orderModal").style.display = "block"
    }
  } catch (error) {
    console.error("Error loading order details:", error)
    alert("Failed to load order details.")
  }
}

async function viewMessage(messageId) {
  try {
    const response = await fetch(`php/get_message_details.php?id=${messageId}`)
    const data = await response.json()

    if (data.success) {
      const message = data.message
      currentMessageId = messageId

      document.getElementById("messageDetails").innerHTML = `
                <div class="message-info">
                    <h3>Message #${message.id}</h3>
                    <p><strong>From:</strong> ${message.name}</p>
                    <p><strong>Email:</strong> ${message.email}</p>
                    ${message.phone ? `<p><strong>Phone:</strong> ${message.phone}</p>` : ""}
                    <p><strong>Subject:</strong> ${message.subject}</p>
                    <p><strong>Date:</strong> ${formatDate(message.created_at)}</p>
                    <p><strong>Current Status:</strong> <span class="status-badge status-${message.status}">${message.status}</span></p>
                    <div class="message-content">
                        <strong>Message:</strong>
                        <div style="background: #f8f9fa; padding: 1rem; border-radius: 5px; margin-top: 0.5rem;">
                            ${message.message.replace(/\n/g, "<br>")}
                        </div>
                    </div>
                </div>
            `

      document.getElementById("messageStatusUpdate").value = message.status
      document.getElementById("messageModal").style.display = "block"

      // Mark as read if it was unread
      if (message.status === "unread") {
        updateMessageStatusSilent(messageId, "read")
      }
    }
  } catch (error) {
    console.error("Error loading message details:", error)
    alert("Failed to load message details.")
  }
}

async function updateOrderStatus() {
  if (!currentOrderId) return

  const newStatus = document.getElementById("orderStatusUpdate").value

  try {
    const response = await fetch("php/update_order_status.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        order_id: currentOrderId,
        status: newStatus,
      }),
    })

    const result = await response.json()

    if (result.success) {
      alert("Order status updated successfully!")
      document.getElementById("orderModal").style.display = "none"
      loadOrders()
      loadDashboardData()
    } else {
      alert("Failed to update order status: " + result.message)
    }
  } catch (error) {
    console.error("Error updating order status:", error)
    alert("Failed to update order status.")
  }
}

async function updateMessageStatus() {
  if (!currentMessageId) return

  const newStatus = document.getElementById("messageStatusUpdate").value

  try {
    const response = await fetch("php/update_message_status.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        message_id: currentMessageId,
        status: newStatus,
      }),
    })

    const result = await response.json()

    if (result.success) {
      alert("Message status updated successfully!")
      document.getElementById("messageModal").style.display = "none"
      loadMessages()
      loadDashboardData()
    } else {
      alert("Failed to update message status: " + result.message)
    }
  } catch (error) {
    console.error("Error updating message status:", error)
    alert("Failed to update message status.")
  }
}

async function updateMessageStatusSilent(messageId, status) {
  try {
    await fetch("php/update_message_status.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        message_id: messageId,
        status: status,
      }),
    })

    // Refresh data
    loadMessages()
    loadDashboardData()
  } catch (error) {
    console.error("Error updating message status silently:", error)
  }
}

async function deleteOrder(orderId) {
  if (!confirm("Are you sure you want to delete this order? This action cannot be undone.")) {
    return
  }

  try {
    const response = await fetch("php/delete_order.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ order_id: orderId }),
    })

    const result = await response.json()

    if (result.success) {
      alert("Order deleted successfully!")
      loadOrders()
      loadDashboardData()
    } else {
      alert("Failed to delete order: " + result.message)
    }
  } catch (error) {
    console.error("Error deleting order:", error)
    alert("Failed to delete order.")
  }
}

async function deleteMessage(messageId) {
  if (!confirm("Are you sure you want to delete this message? This action cannot be undone.")) {
    return
  }

  try {
    const response = await fetch("php/delete_message.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ message_id: messageId }),
    })

    const result = await response.json()

    if (result.success) {
      alert("Message deleted successfully!")
      loadMessages()
      loadDashboardData()
    } else {
      alert("Failed to delete message: " + result.message)
    }
  } catch (error) {
    console.error("Error deleting message:", error)
    alert("Failed to delete message.")
  }
}
