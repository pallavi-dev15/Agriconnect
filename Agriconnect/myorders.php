<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Smart AgriConnect</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            color: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            animation: slideIn 0.3s ease;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .message.info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .orders-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .order-item {
            border-bottom: 1px solid #eee;
            padding: 20px;
            transition: background-color 0.3s;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-item:hover {
            background-color: #f9f9f9;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .order-id {
            font-weight: bold;
            color: #2E7D32;
            font-size: 16px;
        }

        .order-status {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-accepted {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-shipped {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-delivered {
            background-color: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .order-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 3px;
        }

        .detail-value {
            color: #333;
        }

        .order-description {
            background-color: #f9f9f9;
            padding: 12px;
            border-left: 3px solid #4CAF50;
            margin: 12px 0;
            font-size: 13px;
            line-height: 1.5;
        }

        .order-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .btn-secondary {
            background-color: #2196F3;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #0b7dda;
        }

        .btn-success {
            background-color: #4CAF50;
            color: white;
        }

        .btn-success:hover {
            background-color: #45a049;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state h3 {
            color: #666;
            margin-bottom: 10px;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #4CAF50;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        nav {
            background-color: #2E7D32;
            padding: 0;
            margin: 0;
        }

        nav ul {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }

        nav li {
            margin: 0;
        }

        nav a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 15px 20px;
            transition: background-color 0.3s;
        }

        nav a:hover {
            background-color: #1b5e20;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: #fefefe;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 90%;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
        }

        .modal-header h2 {
            margin: 0;
            color: #333;
        }

        .close-btn {
            font-size: 28px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
            border: none;
            background: none;
        }

        .close-btn:hover {
            color: #000;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
        }

        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-group select:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }

        .modal-footer {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>

<nav>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="myorders.php" style="background-color: #1b5e20;">My Orders</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<div class="container">
    <div class="header">
        <h1>My Orders</h1>
        <p id="headerSubtitle">Track your crop orders</p>
    </div>

    <div id="message" class="message" style="display: none;"></div>

    <div id="ordersContainer" class="orders-container">
        <div class="loading">
            <div class="spinner"></div>
            <p>Loading orders...</p>
        </div>
    </div>
</div>

<!-- Update Status Modal (for farmers only) -->
<div id="statusModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Update Order Status</h2>
            <button class="close-btn" onclick="closeStatusModal()">&times;</button>
        </div>
        <form id="statusForm" onsubmit="submitStatusUpdate(event)">
            <div class="form-group">
                <label for="orderIdDisplay">Order ID</label>
                <input type="text" id="orderIdDisplay" disabled>
            </div>

            <div class="form-group">
                <label for="statusSelect">New Status *</label>
                <select id="statusSelect" required>
                    <option value="">-- Select Status --</option>
                    <option value="pending">Pending</option>
                    <option value="accepted">Accepted</option>
                    <option value="shipped">Shipped</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeStatusModal()">Cancel</button>
                <button type="submit" class="btn btn-success">Update Status</button>
            </div>
        </form>
    </div>
</div>

<script>
var currentOrderId = null;
var userType = null;

// Show message
function showMessage(text, type = 'success') {
    var msgDiv = document.getElementById('message');
    msgDiv.textContent = text;
    msgDiv.className = 'message ' + type;
    msgDiv.style.display = 'block';
    
    setTimeout(function() {
        msgDiv.style.display = 'none';
    }, 5000);
}

// Load orders
function loadOrders() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'api_get_orders.php', true);
    xhr.onload = function() {
        var container = document.getElementById('ordersContainer');
        
        if (xhr.status === 200) {
            var xmlDoc = xhr.responseXML;
            if (!xmlDoc) {
                container.innerHTML = '<div class="empty-state"><h3>Error loading orders</h3></div>';
                return;
            }

            var successNode = xmlDoc.getElementsByTagName('success')[0];
            var orders = xmlDoc.getElementsByTagName('order');

            if (!successNode || successNode.textContent !== 'true' || orders.length === 0) {
                container.innerHTML = '<div class="empty-state"><h3>No orders yet</h3><p>Start by browsing crops and placing an order</p></div>';
                return;
            }
            
            container.innerHTML = '';
            for (var i = 0; i < orders.length; i++) {
                var orderElement = createOrderElement(orders[i]);
                container.appendChild(orderElement);
            }
        } else {
            container.innerHTML = '<div class="empty-state"><h3>Error loading orders</h3></div>';
        }
    };
    xhr.onerror = function() {
        document.getElementById('ordersContainer').innerHTML = '<div class="empty-state"><h3>Error loading orders</h3></div>';
    };
    xhr.send();
}

// Create order element from XML node
function createOrderElement(orderXML) {
    var id = orderXML.getElementsByTagName('id')[0].textContent;
    var cropName = orderXML.getElementsByTagName('crop_name')[0].textContent;
    var quantity = orderXML.getElementsByTagName('quantity')[0].textContent;
    var totalPrice = orderXML.getElementsByTagName('total_price')[0].textContent;
    var status = orderXML.getElementsByTagName('order_status')[0].textContent;
    var orderDate = orderXML.getElementsByTagName('order_date')[0].textContent;
    var deliveryAddress = orderXML.getElementsByTagName('delivery_address')[0].textContent;
    var buyerNameNode = orderXML.getElementsByTagName('buyer_name')[0];
    var farmerNameNode = orderXML.getElementsByTagName('farmer_name')[0];
    var notesNode = orderXML.getElementsByTagName('notes')[0];

    var div = document.createElement('div');
    div.className = 'order-item';

    var headerHTML = '<div class="order-header">' +
                     '<div class="order-id">Order #' + id + '</div>' +
                     '<div class="order-status status-' + status + '">' + status + '</div>' +
                     '</div>';

    var detailsHTML = '<div class="order-details">' +
                      '<div class="detail-item">' +
                      '<div class="detail-label">Crop</div>' +
                      '<div class="detail-value">' + cropName + '</div>' +
                      '</div>' +
                      '<div class="detail-item">' +
                      '<div class="detail-label">Quantity</div>' +
                      '<div class="detail-value">' + quantity + ' kg</div>' +
                      '</div>' +
                      '<div class="detail-item">' +
                      '<div class="detail-label">Total Price</div>' +
                      '<div class="detail-value">₹' + totalPrice + '</div>' +
                      '</div>' +
                      '<div class="detail-item">' +
                      '<div class="detail-label">Order Date</div>' +
                      '<div class="detail-value">' + new Date(orderDate).toLocaleDateString() + '</div>' +
                      '</div>';

    if (buyerNameNode) {
        detailsHTML += '<div class="detail-item">' +
                       '<div class="detail-label">Customer</div>' +
                       '<div class="detail-value">' + buyerNameNode.textContent + '</div>' +
                       '</div>';
    }

    if (farmerNameNode) {
        detailsHTML += '<div class="detail-item">' +
                       '<div class="detail-label">Farmer</div>' +
                       '<div class="detail-value">' + farmerNameNode.textContent + '</div>' +
                       '</div>';
    }

    detailsHTML += '</div>';

    var addressHTML = '<div class="order-description">' +
                      '<strong>Delivery Address:</strong> ' + deliveryAddress + '</div>';

    var notesHTML = '';
    if (notesNode && notesNode.textContent) {
        notesHTML = '<div class="order-description">' +
                    '<strong>Special Notes:</strong> ' + notesNode.textContent + '</div>';
    }

    var actionsHTML = '<div class="order-actions">';
    
    // If buyer_name exists, this is the farmer view — allow status updates
    if (buyerNameNode) {
        actionsHTML += '<button class="btn btn-secondary" onclick="openStatusModal(' + id + ')">Update Status</button>';
    }
    
    actionsHTML += '</div>';

    div.innerHTML = headerHTML + detailsHTML + addressHTML + notesHTML + actionsHTML;

    return div;
}

// Status modal functions
function openStatusModal(orderId) {
    currentOrderId = orderId;
    document.getElementById('orderIdDisplay').value = 'Order #' + orderId;
    document.getElementById('statusSelect').value = '';
    document.getElementById('statusModal').classList.add('active');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.remove('active');
}

// Submit status update
function submitStatusUpdate(event) {
    event.preventDefault();

    var newStatus = document.getElementById('statusSelect').value;

    if (!newStatus) {
        showMessage('Please select a status', 'error');
        return;
    }

    var formData = new FormData();
    formData.append('order_id', currentOrderId);
    formData.append('status', newStatus);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'api_update_order_status.php', true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var xmlDoc = xhr.responseXML;
            if (!xmlDoc) {
                showMessage('Error updating status', 'error');
                return;
            }

            var successNode = xmlDoc.getElementsByTagName('success')[0];
            var messageNode = xmlDoc.getElementsByTagName('message')[0];
            var success = successNode && successNode.textContent === 'true';
            var message = messageNode ? messageNode.textContent : 'Error updating status';

            if (success) {
                showMessage(message, 'success');
                closeStatusModal();
                loadOrders();
            } else {
                showMessage(message, 'error');
            }
        } else {
            showMessage('Error updating status', 'error');
        }
    };
    xhr.onerror = function() {
        showMessage('Network error. Try again.', 'error');
    };
    xhr.send(formData);
}

// Close modal when clicking outside
window.onclick = function(event) {
    var modal = document.getElementById('statusModal');
    if (event.target === modal) {
        closeStatusModal();
    }
};

// Load orders on page load
window.addEventListener('DOMContentLoaded', function() {
    loadOrders();
});
</script>

</body>
</html>
