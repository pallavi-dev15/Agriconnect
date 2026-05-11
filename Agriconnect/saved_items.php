<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['usertype'])) {
    header("Location: login.php");
    exit();
}
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
                        <a href="marketplace.php" class="btn">View in Marketplace</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <p style="margin:14px 0;"><a href="dashboard.php" class="btn">← Back to Dashboard</a></p>
    <?php endif; ?>
    </main>
</div>

<footer>© 2026 Smart AgriConnect</footer>
</body>
</html>