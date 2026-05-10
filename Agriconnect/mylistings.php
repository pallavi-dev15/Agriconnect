<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Crop Listings</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; margin: 0; }
        .container { max-width: 900px; margin: 20px auto; padding: 20px; }
        .header { background: #4CAF50; color: white; padding: 20px; border-radius: 5px; }
        .form-box { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .form-box h2 { margin-top: 0; }
        input, textarea { width: 100%; padding: 8px; margin: 5px 0; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box; }
        button { padding: 10px 20px; background: #4CAF50; color: white; border: none; border-radius: 3px; cursor: pointer; }
        button:hover { background: #45a049; }
        .btn-delete { background: #f44336; padding: 5px 10px; font-size: 12px; }
        .btn-delete:hover { background: #da190b; }
        .crops-list { background: white; padding: 20px; border-radius: 5px; }
        .crop-item { background: #f9f9f9; padding: 15px; margin: 10px 0; border-left: 4px solid #4CAF50; border-radius: 3px; }
        .crop-item h3 { margin: 0 0 10px 0; }
        .crop-info { font-size: 13px; color: #666; }
        .nav { background: #2E7D32; padding: 0; }
        .nav a { display: inline-block; color: white; text-decoration: none; padding: 15px 20px; }
        .nav a:hover { background: #1b5e20; }
        .message { padding: 10px; margin: 10px 0; border-radius: 3px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
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

    <?php
    session_start();
    require 'db.php';

    // Check if farmer is logged in
    if (!isset($_SESSION['id']) || $_SESSION['usertype'] !== 'farmer') {
        header("Location: login.php");
        exit();
    }

    $farmer_id = $_SESSION['id'];
    $message = '';

    // Add crop
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
        $crop_name = htmlspecialchars($_POST['crop_name']);
        $price = floatval($_POST['price']);
        $quantity = intval($_POST['quantity']);
        $grade = htmlspecialchars($_POST['grade']);
        $location = htmlspecialchars($_POST['location']);
        $description = htmlspecialchars($_POST['description']);

        if (!empty($crop_name) && $price > 0 && $quantity > 0) {
            $stmt = $conn->prepare("INSERT INTO crops (farmer_id, crop_name, price, quantity, grade, location, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isdiiss", $farmer_id, $crop_name, $price, $quantity, $grade, $location, $description);
            
            if ($stmt->execute()) {
                $message = '<div class="message success">✓ Crop added successfully!</div>';
            } else {
                $message = '<div class="message error">✗ Error adding crop</div>';
            }
            $stmt->close();
        } else {
            $message = '<div class="message error">✗ Fill all required fields</div>';
        }
    }

    // Delete crop
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
        $crop_id = intval($_POST['crop_id']);
        
        // Verify ownership
        $check = $conn->prepare("SELECT farmer_id FROM crops WHERE id = ?");
        $check->bind_param("i", $crop_id);
        $check->execute();
        $result = $check->get_result();
        $row = $result->fetch_assoc();

        if ($row && $row['farmer_id'] === $farmer_id) {
            $delete = $conn->prepare("DELETE FROM crops WHERE id = ? AND farmer_id = ?");
            $delete->bind_param("ii", $crop_id, $farmer_id);
            
            if ($delete->execute()) {
                $message = '<div class="message success">✓ Crop deleted!</div>';
            } else {
                $message = '<div class="message error">✗ Error deleting</div>';
            }
            $delete->close();
        }
        $check->close();
    }

    echo $message;
    ?>

    <!-- Add Crop Form -->
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

    <!-- Your Crops List -->
    <div class="crops-list">
        <h2>Your Crops</h2>
        <?php
        $stmt = $conn->prepare("SELECT id, crop_name, price, quantity, grade, location, description FROM crops WHERE farmer_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $farmer_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo '<p style="color: #999;">No crops yet. Add your first crop above!</p>';
        } else {
            while ($crop = $result->fetch_assoc()) {
                echo '<div class="crop-item">';
                echo '<h3>' . $crop['crop_name'] . '</h3>';
                echo '<div class="crop-info">';
                echo '₹' . $crop['price'] . '/kg | ' . $crop['quantity'] . ' kg | ' . $crop['grade'] . ' | ' . $crop['location'];
                echo '</div>';
                echo '<p>' . $crop['description'] . '</p>';
                echo '<form method="POST" style="display: inline;">';
                echo '<input type="hidden" name="action" value="delete">';
                echo '<input type="hidden" name="crop_id" value="' . $crop['id'] . '">';
                echo '<button type="submit" class="btn-delete" onclick="return confirm(\'Delete this crop?\')">Delete</button>';
                echo '</form>';
                echo '</div>';
            }
        }
        $stmt->close();
        ?>
    </div>
</div>

</body>
</html>
