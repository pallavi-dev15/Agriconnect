<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id']) || $_SESSION['usertype'] !== 'farmer') {
    header('Location: login.php');
    exit();
}

$farmer_id = $_SESSION['id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $crop_name = trim($_POST['crop_name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    $grade = trim($_POST['grade'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($crop_name !== '' && $price > 0 && $quantity > 0) {
        $stmt = $conn->prepare('INSERT INTO crops (farmer_id, crop_name, price, quantity, grade, location, description) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('isdisss', $farmer_id, $crop_name, $price, $quantity, $grade, $location, $description);
        if ($stmt->execute()) {
            $message = '<div class="message success">Crop added successfully!</div>';
        } else {
            $message = '<div class="message error">Error adding crop.</div>';
        }
        $stmt->close();
    } else {
        $message = '<div class="message error">Fill all required fields.</div>';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $crop_id = intval($_POST['crop_id'] ?? 0);
    $check = $conn->prepare('SELECT farmer_id FROM crops WHERE id = ?');
    $check->bind_param('i', $crop_id);
    $check->execute();
    $result = $check->get_result();
    $row = $result->fetch_assoc();

    if ($row && (int)$row['farmer_id'] === (int)$farmer_id) {
        $delete = $conn->prepare('DELETE FROM crops WHERE id = ? AND farmer_id = ?');
        $delete->bind_param('ii', $crop_id, $farmer_id);
        if ($delete->execute()) {
            $message = '<div class="message success">Crop deleted successfully.</div>';
        } else {
            $message = '<div class="message error">Error deleting crop.</div>';
        }
        $delete->close();
    } else {
        $message = '<div class="message error">Unauthorized crop deletion.</div>';
    }
    $check->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    header('Content-Type: application/json; charset=utf-8');

    $crop_id = intval($_POST['crop_id'] ?? 0);
    $crop_name = trim($_POST['crop_name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    $grade = trim($_POST['grade'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($crop_id <= 0 || $crop_name === '' || $price <= 0 || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit();
    }

    $check = $conn->prepare('SELECT farmer_id FROM crops WHERE id = ?');
    $check->bind_param('i', $crop_id);
    $check->execute();
    $result = $check->get_result();
    $row = $result->fetch_assoc();
    $check->close();

    if (!$row || (int)$row['farmer_id'] !== (int)$farmer_id) {
        echo json_encode(['success' => false, 'message' => 'Crop not found or unauthorized']);
        exit();
    }

    $stmt = $conn->prepare('UPDATE crops SET crop_name = ?, price = ?, quantity = ?, grade = ?, location = ?, description = ? WHERE id = ? AND farmer_id = ?');
    $stmt->bind_param('sdisssii', $crop_name, $price, $quantity, $grade, $location, $description, $crop_id, $farmer_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Crop updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update crop']);
    }
    $stmt->close();
    $conn->close();
    exit();
}

$stmt = $conn->prepare('SELECT id, crop_name, price, quantity, grade, location, description FROM crops WHERE farmer_id = ? ORDER BY created_at DESC');
$stmt->bind_param('i', $farmer_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Crop Listings</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; }
        .container { max-width: 900px; margin: 20px auto; padding: 20px; }
        .header { background: #4CAF50; color: white; padding: 20px; border-radius: 5px; }
        .form-box, .crops-list { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .form-box h2, .crops-list h2 { margin-top: 0; }
        input, textarea { width: 100%; padding: 8px; margin: 5px 0; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box; }
        button { padding: 10px 20px; background: #4CAF50; color: white; border: none; border-radius: 3px; cursor: pointer; }
        button:hover { background: #45a049; }
        .btn-delete { background: #f44336; padding: 6px 10px; font-size: 12px; }
        .btn-delete:hover { background: #da190b; }
        .btn-update { background: #2196F3; padding: 6px 10px; font-size: 12px; margin-right: 6px; }
        .btn-update:hover { background: #0b7dda; }
        .crop-item { background: #f9f9f9; padding: 15px; margin: 10px 0; border-left: 4px solid #4CAF50; border-radius: 3px; }
        .crop-item h3 { margin: 0 0 10px 0; }
        .crop-info { font-size: 13px; color: #666; margin-bottom: 10px; }
        .nav { background: #2E7D32; padding: 0; }
        .nav a { display: inline-block; color: white; text-decoration: none; padding: 15px 20px; }
        .nav a:hover { background: #1b5e20; }
        .message { padding: 10px; margin: 10px 0; border-radius: 3px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .modal { display: none; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); }
        .modal.active { display: flex; align-items: center; justify-content: center; }
        .modal-content { background: white; padding: 25px; border-radius: 5px; width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .modal-header h2 { margin: 0; }
        .close-btn { background: none; border: none; font-size: 28px; cursor: pointer; color: #aaa; padding: 0; }
        .close-btn:hover { color: #000; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        .actions { margin-top: 10px; }
    </style>
</head>
<body>

<div class="nav">
    <a href="dashboard.php">Dashboard</a>
    <a href="mylistings.php" style="background: #1b5e20;">My Listings</a>
    <a href="myorders.php">My Orders</a>
    <a href="logout.php">Logout</a>
</div>

<div class="container">
    <div class="header">
        <h1>My Crop Listings</h1>
        <p>Add and manage your crops</p>
    </div>

    <?php echo $message; ?>

    <div class="form-box">
        <h2>Add New Crop</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <label>Crop Name *</label>
            <input type="text" name="crop_name" required>
            <label>Price (₹/kg) *</label>
            <input type="number" name="price" step="0.01" required>
            <label>Quantity (kg) *</label>
            <input type="number" name="quantity" required>
            <label>Grade</label>
            <input type="text" name="grade" placeholder="e.g., A+">
            <label>Location</label>
            <input type="text" name="location" placeholder="e.g., Pune, Maharashtra">
            <label>Description</label>
            <textarea name="description" rows="3" placeholder="Describe your crop..."></textarea>
            <button type="submit">Add Crop</button>
        </form>
    </div>

    <div class="crops-list">
        <h2>Your Crops</h2>
        <?php if ($result->num_rows === 0): ?>
            <p style="color: #999;">No crops yet. Add your first crop above!</p>
        <?php else: ?>
            <?php while ($crop = $result->fetch_assoc()): ?>
                <div class="crop-item">
                    <h3><?php echo htmlspecialchars($crop['crop_name']); ?></h3>
                    <div class="crop-info">
                        ₹<?php echo htmlspecialchars($crop['price']); ?>/kg | <?php echo htmlspecialchars($crop['quantity']); ?> kg | <?php echo htmlspecialchars($crop['grade']); ?> | <?php echo htmlspecialchars($crop['location']); ?>
                    </div>
                    <p><?php echo htmlspecialchars($crop['description']); ?></p>
                    <div class="actions">
                        <button type="button" class="btn-update update-btn"
                            data-id="<?php echo (int)$crop['id']; ?>"
                            data-name="<?php echo htmlspecialchars($crop['crop_name'], ENT_QUOTES); ?>"
                            data-price="<?php echo htmlspecialchars($crop['price'], ENT_QUOTES); ?>"
                            data-quantity="<?php echo (int)$crop['quantity']; ?>"
                            data-grade="<?php echo htmlspecialchars($crop['grade'], ENT_QUOTES); ?>"
                            data-location="<?php echo htmlspecialchars($crop['location'], ENT_QUOTES); ?>"
                            data-description="<?php echo htmlspecialchars($crop['description'], ENT_QUOTES); ?>">Update</button>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="crop_id" value="<?php echo (int)$crop['id']; ?>">
                            <button type="submit" class="btn-delete" onclick="return confirm('Delete this crop?')">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
        <?php $stmt->close(); ?>
    </div>
</div>

<div id="updateModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Update Crop</h2>
            <button class="close-btn" type="button" onclick="closeUpdateModal()">&times;</button>
        </div>
        <form id="updateForm">
            <input type="hidden" id="update_crop_id">
            <label>Crop Name *</label>
            <input type="text" id="update_crop_name" required>
            <label>Price (₹/kg) *</label>
            <input type="number" id="update_price" step="0.01" required>
            <label>Quantity (kg) *</label>
            <input type="number" id="update_quantity" required>
            <label>Grade</label>
            <input type="text" id="update_grade" placeholder="e.g., A+">
            <label>Location</label>
            <input type="text" id="update_location" placeholder="e.g., Pune, Maharashtra">
            <label>Description</label>
            <textarea id="update_description" rows="3" placeholder="Describe your crop..."></textarea>
            <div style="margin-top: 15px;">
                <button type="submit">Save Changes</button>
                <button type="button" onclick="closeUpdateModal()" style="background: #666; margin-left: 10px;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
var updateButtons = document.querySelectorAll('.update-btn');
updateButtons.forEach(function(button) {
    button.addEventListener('click', function() {
        document.getElementById('update_crop_id').value = this.dataset.id;
        document.getElementById('update_crop_name').value = this.dataset.name;
        document.getElementById('update_price').value = this.dataset.price;
        document.getElementById('update_quantity').value = this.dataset.quantity;
        document.getElementById('update_grade').value = this.dataset.grade;
        document.getElementById('update_location').value = this.dataset.location;
        document.getElementById('update_description').value = this.dataset.description;
        document.getElementById('updateModal').classList.add('active');
    });
});

function closeUpdateModal() {
    document.getElementById('updateModal').classList.remove('active');
}

document.getElementById('updateForm').addEventListener('submit', function(e) {
    e.preventDefault();

    var formData = new FormData();
    formData.append('action', 'update');
    formData.append('crop_id', document.getElementById('update_crop_id').value);
    formData.append('crop_name', document.getElementById('update_crop_name').value);
    formData.append('price', document.getElementById('update_price').value);
    formData.append('quantity', document.getElementById('update_quantity').value);
    formData.append('grade', document.getElementById('update_grade').value);
    formData.append('location', document.getElementById('update_location').value);
    formData.append('description', document.getElementById('update_description').value);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'mylistings.php', true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var xmlDoc = xhr.responseXML;
            var success = xmlDoc.getElementsByTagName('success')[0].textContent;
            if (success === 'true') {
                var msg = xmlDoc.getElementsByTagName('message')[0].textContent;
                alert(msg);
                closeUpdateModal();
                setTimeout(function() {
                    location.reload();
                }, 100);
            } else {
                var msg = xmlDoc.getElementsByTagName('message')[0].textContent;
                alert(msg || 'Error updating crop');
            }
        }
    };
    xhr.onerror = function() {
        alert('Network error. Try again.');
    };
    xhr.send(formData);
});

window.onclick = function(event) {
    var modal = document.getElementById('updateModal');
    if (event.target === modal) {
        closeUpdateModal();
    }
};
</script>

</body>
</html>
