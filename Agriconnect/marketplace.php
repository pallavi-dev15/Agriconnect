<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace - Smart AgriConnect</title>
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
            max-width: 1200px;
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

        .controls {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 200px;
            display: flex;
            gap: 10px;
        }

        input[type="text"] {
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            flex: 1;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }

        .btn-primary:hover {
            background-color: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(76, 175, 80, 0.3);
        }

        .btn-secondary {
            background-color: #2196F3;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #0b7dda;
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
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
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

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group textarea:focus {
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

        .crops-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .crop-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .crop-card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
        }

        .crop-image {
            width: 100%;
            height: 200px;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 12px;
            overflow: hidden;
        }

        .crop-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .crop-info {
            padding: 15px;
        }

        .crop-name {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }

        .crop-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 12px;
            font-size: 13px;
        }

        .crop-details p {
            margin: 0;
        }

        .crop-details strong {
            color: #2E7D32;
        }

        .crop-description {
            font-size: 13px;
            color: #666;
            margin-bottom: 12px;
            line-height: 1.4;
            max-height: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .crop-actions {
            display: flex;
            gap: 10px;
        }

        .crop-actions .btn {
            flex: 1;
            margin: 0;
            padding: 8px 12px;
            font-size: 12px;
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

        .no-results {
            text-align: center;
            padding: 40px;
            color: #999;
            font-size: 16px;
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

        .price-display {
            font-size: 16px;
            font-weight: bold;
            color: #2E7D32;
            margin-top: 8px;
        }
    </style>
</head>
<body>

<nav>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="marketplace.php" style="background-color: #1b5e20;">Marketplace</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<div class="container">
    <div class="header">
        <h1>Marketplace</h1>
        <p>Browse available crops and place orders</p>
    </div>

    <div id="message" class="message" style="display: none;"></div>

    <div class="controls">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search crops...">
            <button class="btn btn-secondary" onclick="searchCrops()">Search</button>
            <button class="btn btn-secondary" onclick="clearSearch()">Clear</button>
        </div>
    </div>

    <div id="cropsContainer" class="crops-grid">
        <div class="loading">
            <div class="spinner"></div>
            <p>Loading crops...</p>
        </div>
    </div>
</div>

<!-- Order Modal -->
<div id="orderModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Place Order</h2>
            <button class="close-btn" onclick="closeOrderModal()">&times;</button>
        </div>
        <form id="orderForm" onsubmit="submitOrder(event)">
            <div class="form-group">
                <label>Crop Name</label>
                <input type="text" id="orderCropName" disabled>
            </div>

            <div class="form-group">
                <label>Price per Unit</label>
                <input type="text" id="orderCropPrice" disabled>
            </div>

            <div class="form-group">
                <label>Available Quantity (kg)</label>
                <input type="text" id="orderAvailableQty" disabled>
            </div>

            <div class="form-group">
                <label for="orderQuantity">Order Quantity (kg) *</label>
                <input type="number" id="orderQuantity" required>
            </div>

            <div class="form-group" id="totalPriceGroup" style="display: none;">
                <label>Total Price</label>
                <div class="price-display" id="totalPrice">₹0</div>
            </div>

            <div class="form-group">
                <label for="deliveryAddress">Delivery Address *</label>
                <textarea id="deliveryAddress" rows="3" placeholder="Enter your delivery address" required></textarea>
            </div>

            <div class="form-group">
                <label for="orderNotes">Special Notes</label>
                <textarea id="orderNotes" rows="2" placeholder="Any special instructions for the farmer..."></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeOrderModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Place Order</button>
            </div>
        </form>
    </div>
</div>

<script>
var currentOrderData = {};

// XMLHttpRequest wrapper function
function makeRequest(method, url, callback, data = null) {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    var xmlDoc = xhr.responseXML;
                    callback(null, xmlDoc);
                } catch (e) {
                    callback(e, null);
                }
            } else {
                callback(new Error("HTTP " + xhr.status), null);
            }
        }
    };

    xhr.onerror = function() {
        callback(new Error("Network error"), null);
    };

    xhr.open(method, url, true);
    
    if (method === 'POST') {
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send(data);
    } else {
        xhr.send();
    }
}

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

// Load crops
function loadCrops() {
    makeRequest('GET', 'api_get_crops.php', function(err, xmlDoc) {
        var container = document.getElementById('cropsContainer');
        
        if (err) {
            container.innerHTML = '<div class="no-results">Error loading crops</div>';
            return;
        }

        var crops = xmlDoc.getElementsByTagName('crop');
        
        if (crops.length === 0) {
            container.innerHTML = '<div class="empty-state"><h3>No crops available</h3><p>Check back soon for new listings</p></div>';
            return;
        }

        container.innerHTML = '';
        
        for (var i = 0; i < crops.length; i++) {
            var crop = crops[i];
            var cropElement = createCropCard(crop);
            container.appendChild(cropElement);
        }
    });
}

// Create crop card element
function createCropCard(cropXML) {
    var id = cropXML.getElementsByTagName('id')[0].textContent;
    var name = cropXML.getElementsByTagName('crop_name')[0].textContent;
    var price = cropXML.getElementsByTagName('price')[0].textContent;
    var quantity = cropXML.getElementsByTagName('quantity')[0].textContent;
    var grade = cropXML.getElementsByTagName('grade')[0].textContent;
    var location = cropXML.getElementsByTagName('location')[0].textContent;
    var description = cropXML.getElementsByTagName('description')[0].textContent;
    var imageUrl = cropXML.getElementsByTagName('image_url')[0].textContent;

    var card = document.createElement('div');
    card.className = 'crop-card';
    
    var imageHTML = imageUrl ? '<img src="' + imageUrl + '" alt="' + name + '">' : '<span>No Image</span>';
    
    card.innerHTML = `
        <div class="crop-image">${imageHTML}</div>
        <div class="crop-info">
            <div class="crop-name">${name}</div>
            <div class="crop-details">
                <p><strong>₹${price}</strong>/kg</p>
                <p><strong>${quantity}</strong> kg</p>
                <p><strong>${grade}</strong></p>
                <p><strong>${location}</strong></p>
            </div>
            <div class="crop-description">${description}</div>
            <div class="crop-actions">
                <button class="btn btn-primary" onclick="openOrderModal(${id}, '${name}', ${price}, ${quantity})">Order Now</button>
            </div>
        </div>
    `;

    return card;
}

// Search crops
function searchCrops() {
    var query = document.getElementById('searchInput').value.trim();
    
    if (!query) {
        showMessage('Please enter a search term', 'info');
        return;
    }

    var container = document.getElementById('cropsContainer');
    container.innerHTML = '<div class="loading"><div class="spinner"></div><p>Searching...</p></div>';

    makeRequest('GET', 'api_search_crops.php?query=' + encodeURIComponent(query), function(err, xmlDoc) {
        if (err) {
            container.innerHTML = '<div class="no-results">Error searching crops</div>';
            return;
        }

        var crops = xmlDoc.getElementsByTagName('crop');
        
        if (crops.length === 0) {
            container.innerHTML = '<div class="no-results">No crops found matching "' + query + '"</div>';
            return;
        }

        container.innerHTML = '';
        
        for (var i = 0; i < crops.length; i++) {
            var crop = crops[i];
            var cropElement = createCropCard(crop);
            container.appendChild(cropElement);
        }
    });
}

// Clear search and reload
function clearSearch() {
    document.getElementById('searchInput').value = '';
    loadCrops();
}

// Modal functions
function openOrderModal(cropId, cropName, price, quantity) {
    document.getElementById('orderCropName').value = cropName;
    document.getElementById('orderCropPrice').value = '₹' + price + '/kg';
    document.getElementById('orderAvailableQty').value = quantity + ' kg';
    document.getElementById('orderQuantity').value = '';
    document.getElementById('deliveryAddress').value = '';
    document.getElementById('orderNotes').value = '';
    
    currentOrderData = {
        cropId: cropId,
        price: price,
        maxQuantity: quantity
    };
    
    document.getElementById('orderModal').classList.add('active');
}

function closeOrderModal() {
    document.getElementById('orderModal').classList.remove('active');
}

// Calculate total price
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('orderQuantity').addEventListener('input', function() {
        var qty = parseFloat(this.value);
        if (qty > 0 && currentOrderData.price) {
            var total = qty * currentOrderData.price;
            document.getElementById('totalPrice').textContent = '₹' + total.toFixed(2);
            document.getElementById('totalPriceGroup').style.display = 'block';
        } else {
            document.getElementById('totalPriceGroup').style.display = 'none';
        }
    });

    loadCrops();
});

// Submit order
function submitOrder(event) {
    event.preventDefault();

    var quantity = document.getElementById('orderQuantity').value;
    var deliveryAddress = document.getElementById('deliveryAddress').value;
    var notes = document.getElementById('orderNotes').value;

    if (quantity > currentOrderData.maxQuantity) {
        showMessage('Quantity exceeds available stock', 'error');
        return;
    }

    var params = 'crop_id=' + encodeURIComponent(currentOrderData.cropId) +
                 '&quantity=' + encodeURIComponent(quantity) +
                 '&delivery_address=' + encodeURIComponent(deliveryAddress) +
                 '&notes=' + encodeURIComponent(notes);

    makeRequest('POST', 'api_place_order.php', function(err, xmlDoc) {
        if (err) {
            showMessage('Error placing order', 'error');
            return;
        }

        var success = xmlDoc.getElementsByTagName('success')[0].textContent;
        var message = xmlDoc.getElementsByTagName('message')[0].textContent;

        if (success === 'true') {
            showMessage('Order placed successfully! Order ID: ' + xmlDoc.getElementsByTagName('order_id')[0].textContent, 'success');
            closeOrderModal();
            loadCrops();
        } else {
            showMessage(message, 'error');
        }
    }, params);
}

// Close modal when clicking outside
window.onclick = function(event) {
    var modal = document.getElementById('orderModal');
    if (event.target === modal) {
        closeOrderModal();
    }
};
</script>

</body>
</html>
