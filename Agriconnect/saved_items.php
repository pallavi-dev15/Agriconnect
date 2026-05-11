<?php
require 'session_protect.php';
ensure_logged_in();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Saved Items - Smart AgriConnect</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <style>
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        nav {
            flex-shrink: 0;
        }
        .container {
            flex: 1;
        }
        footer {
            flex-shrink: 0;
        }
        .message {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
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
        .crop-actions {
            display: flex;
            gap: 8px;
        }
        .crop-actions .btn {
            flex: 1;
            margin: 0;
            padding: 8px 12px;
            font-size: 12px;
        }
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        .btn-primary:hover {
            background-color: #45a049;
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
        <?php if ($_SESSION['usertype'] === 'farmer'): ?>
            <li><a href="mylistings.php">My Listings</a></li>
            <li><a href="myorders.php">My Orders</a></li>
        <?php else: ?>
            <li><a href="marketplace.php">Marketplace</a></li>
            <li><a href="myorders.php">My Orders</a></li>
        <?php endif; ?>
        <li><a href="logout.php" style="background-color: #d9534f; border-radius: 999px; padding: 0.45rem 0.8rem;">Logout</a></li>
    </ul>
</nav>

<div class="container" style="max-width:1100px; margin:1.2rem auto; padding:0 1rem;">
    <div class="header" style="background:linear-gradient(135deg,#4CAF50,#2E7D32); color:#fff; padding:28px; border-radius:8px; box-shadow:0 4px 6px rgba(0,0,0,0.08); margin-bottom:18px;">
        <h1 style="margin:0; font-size:28px;">Saved Items</h1>
        <p style="margin:6px 0 0; opacity:0.95;">Saved items are kept here for quick access.</p>
    </div>
<?php
require 'db.php';

// Attempt to load saved items from DB; fallback to session
$buyer_id = $_SESSION['id'] ?? null;
$saved = [];

if ($buyer_id) {
    // check if table exists
    $res = $conn->query("SHOW TABLES LIKE 'saved_items'");
    if ($res && $res->num_rows > 0) {
        $stmt = $conn->prepare("SELECT s.crop_id, c.crop_name, c.price, c.image_url FROM saved_items s JOIN crops c ON s.crop_id = c.id WHERE s.buyer_id = ?");
        $stmt->bind_param('i', $buyer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $saved[] = $row;
        }
        $stmt->close();
    }
}

// Fallback to session-stored saved items (array of crop ids)
if (empty($saved) && !empty($_SESSION['saved_items']) && is_array($_SESSION['saved_items'])) {
    $ids = array_map('intval', $_SESSION['saved_items']);
    if (!empty($ids)) {
        $in = implode(',', $ids);
        $q = "SELECT id as crop_id, crop_name, price, image_url FROM crops WHERE id IN ($in)";
        $r = $conn->query($q);
        if ($r) {
            while ($row = $r->fetch_assoc()) {
                $saved[] = $row;
            }
        }
    }
}

?>

<main style="padding:0; max-width:1100px; margin:0 auto;">
    <div id="message" style="display: none; margin-bottom: 15px;"></div>
    <?php if (empty($saved)): ?>
        <div class="empty-state" style="padding:1rem; background:#fff; border-radius:8px; box-shadow:0 6px 14px rgba(0,0,0,0.05);">
            <h3>No saved items</h3>
            <p>You haven't saved any products yet. Browse the <a href="marketplace.php">marketplace</a> to add favourites.</p>
            <p style="margin-top:12px;"><a href="dashboard.php" class="btn">← Back to Dashboard</a></p>
        </div>
    <?php else: ?>
        <div class="card-container">
            <?php foreach ($saved as $item): ?>
                <div class="card">
                    <div style="height:160px; overflow:hidden;">
                        <?php if (!empty($item['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" style="width:100%; height:160px; object-fit:cover;" />
                        <?php else: ?>
                            <div style="width:100%; height:160px; background:#f0f0f0; display:flex;align-items:center;justify-content:center;color:#999;">No image</div>
                        <?php endif; ?>
                    </div>
                    <div style="padding:1rem;">
                        <h3 style="margin:0 0 0.5rem; color:#1f4f2a"><?php echo htmlspecialchars($item['crop_name']); ?></h3>
                        <p style="margin:0 0 0.75rem; color:#455d4d;">Price: ₹<?php echo htmlspecialchars($item['price']); ?> / kg</p>
                        <div class="crop-actions">
                            <button class="btn btn-primary" onclick="openOrderModal(<?php echo (int)$item['crop_id']; ?>, '<?php echo htmlspecialchars($item['crop_name']); ?>', <?php echo (float)$item['price']; ?>, 100)">Order</button>
                            <button class="btn btn-secondary" onclick="removeFromSaved(<?php echo (int)$item['crop_id']; ?>, this)" style="background-color:#dc2626;">Remove</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <p style="margin:14px 0;"><a href="dashboard.php" class="btn">← Back to Dashboard</a></p>
    <?php endif; ?>
    </main>
</div>

<footer>© 2026 Smart AgriConnect</footer>

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

function showMessage(text, type = 'success') {
    var msgDiv = document.getElementById('message');
    msgDiv.textContent = text;
    msgDiv.className = 'message ' + type;
    msgDiv.style.display = 'block';
    
    setTimeout(function() {
        msgDiv.style.display = 'none';
    }, 5000);
}

function openOrderModal(cropId, cropName, price, quantity) {
    document.getElementById('orderCropName').value = cropName;
    document.getElementById('orderCropPrice').value = '₹' + price + '/kg';
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
});

function submitOrder(event) {
    event.preventDefault();

    var quantity = document.getElementById('orderQuantity').value;
    var deliveryAddress = document.getElementById('deliveryAddress').value;
    var notes = document.getElementById('orderNotes').value;

    if (quantity > currentOrderData.maxQuantity) {
        showMessage('Quantity exceeds available stock', 'error');
        return;
    }

    var formData = new FormData();
    formData.append('crop_id', currentOrderData.cropId);
    formData.append('quantity', quantity);
    formData.append('delivery_address', deliveryAddress);
    formData.append('notes', notes);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'api_place_order.php', true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var xmlDoc = xhr.responseXML;
            var success = xmlDoc.getElementsByTagName('success')[0].textContent;
            if (success === 'true') {
                var orderId = xmlDoc.getElementsByTagName('order_id')[0].textContent;
                showMessage('Order placed successfully! Order ID: ' + orderId, 'success');
                closeOrderModal();
            } else {
                var msg = xmlDoc.getElementsByTagName('message')[0].textContent;
                showMessage(msg || 'Failed to place order', 'error');
            }
        }
    };
    xhr.onerror = function() {
        showMessage('Network error. Try again.', 'error');
    };
    xhr.send(formData);
}

function removeFromSaved(cropId, btn) {
    var formData = new FormData();
    formData.append('crop_id', cropId);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'api_toggle_saved.php', true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var xmlDoc = xhr.responseXML;
            var success = xmlDoc.getElementsByTagName('success')[0].textContent;
            if (success === 'true') {
                showMessage('Removed from saved items', 'success');
                btn.closest('.card').style.opacity = '0.5';
                setTimeout(function() {
                    btn.closest('.card').remove();
                }, 500);
            } else {
                var msg = xmlDoc.getElementsByTagName('message')[0].textContent;
                showMessage(msg || 'Error removing item', 'error');
            }
        }
    };
    xhr.onerror = function() {
        showMessage('Network error. Try again.', 'error');
    };
    xhr.send(formData);
}

window.onclick = function(event) {
    var modal = document.getElementById('orderModal');
    if (event.target === modal) {
        closeOrderModal();
    }
};
</script>

</body>
</html>