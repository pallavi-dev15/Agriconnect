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
    <title>Connect with Farmers - Smart AgriConnect</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/dashboard.css">
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

<?php
require 'db.php';

// Load list of farmers
$farmers = [];
$res = $conn->query("SELECT id, fullname, username, location FROM users WHERE usertype = 'farmer'");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $farmers[] = $row;
    }
}
?>

<div class="container" style="max-width:1100px; margin:1.2rem auto; padding:0 1rem;">
    <div class="header" style="background:linear-gradient(135deg,#4CAF50,#2E7D32); color:#fff; padding:28px; border-radius:8px; box-shadow:0 4px 6px rgba(0,0,0,0.08); margin-bottom:18px;">
        <h1 style="margin:0; font-size:28px;">Connect with Farmers</h1>
        <p style="margin:6px 0 0; opacity:0.95;">Send a message to a specific farmer. Your message will be stored and the farmer can view it in their dashboard.</p>
    </div>

    <main style="max-width:900px; margin:0 auto 1.5rem; padding:0 1rem;">
    <?php if (empty($farmers)): ?>
        <div class="empty-state" style="padding:1rem; background:#fff; border-radius:8px; box-shadow:0 6px 14px rgba(0,0,0,0.05);">
            <h3>No farmers available</h3>
            <p>There are currently no farmers registered on the platform.</p>
        </div>
    <?php else: ?>
        <div style="background:#fff; padding:1rem; border-radius:8px; box-shadow:0 6px 14px rgba(0,0,0,0.05);">
            <div id="msgBox" style="display:none; margin-bottom:12px; padding:10px; border-radius:6px;"></div>
            <form id="messageForm">
                <div class="form-group">
                    <label for="farmer_id">Select Farmer</label>
                    <select name="farmer_id" id="farmer_id" required style="width:100%; padding:10px; border-radius:6px; border:1px solid #ddd;">
                        <option value="">-- Select Farmer --</option>
                        <?php foreach ($farmers as $f): ?>
                            <option value="<?php echo (int)$f['id']; ?>"><?php echo htmlspecialchars($f['fullname'] ?: $f['username']) . ' — ' . htmlspecialchars($f['location']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea name="message" id="message" rows="6" required style="width:100%; padding:10px; border-radius:6px; border:1px solid #ddd;" placeholder="Write your message to the selected farmer..."></textarea>
                </div>

                <div class="form-group" style="text-align:right;">
                    <button type="submit" class="btn">Send Message</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <p style="margin-top:14px;"><a href="dashboard.php" class="btn">← Back to Dashboard</a></p>

</main>
</div>

<footer>© 2026 Smart AgriConnect</footer>

<script>
document.getElementById('messageForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    var farmer = document.getElementById('farmer_id').value;
    var message = document.getElementById('message').value.trim();
    var msgBox = document.getElementById('msgBox');
    msgBox.style.display = 'none';

    if (!farmer) {
        msgBox.style.display = 'block';
        msgBox.style.background = '#ffe8e8';
        msgBox.style.border = '1px solid #f5c6cb';
        msgBox.style.color = '#7a1b1b';
        msgBox.textContent = 'Please select a farmer.';
        return;
    }
    if (!message) {
        msgBox.style.display = 'block';
        msgBox.style.background = '#ffe8e8';
        msgBox.style.border = '1px solid #f5c6cb';
        msgBox.style.color = '#7a1b1b';
        msgBox.textContent = 'Please enter a message.';
        return;
    }

    var formData = new FormData();
    formData.append('farmer_id', farmer);
    formData.append('message', message);

    fetch('api_send_message.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    }).then(function(res) {
        return res.json();
    }).then(function(json) {
        msgBox.style.display = 'block';
        if (json.success) {
            msgBox.style.background = '#d4edda';
            msgBox.style.border = '1px solid #c3e6cb';
            msgBox.style.color = '#155724';
            msgBox.textContent = json.message || 'Message sent.';
            document.getElementById('message').value = '';
            document.getElementById('farmer_id').selectedIndex = 0;
        } else {
            msgBox.style.background = '#ffe8e8';
            msgBox.style.border = '1px solid #f5c6cb';
            msgBox.style.color = '#7a1b1b';
            msgBox.textContent = json.message || 'Failed to send message.';
        }
    }).catch(function(err) {
        msgBox.style.display = 'block';
        msgBox.style.background = '#ffe8e8';
        msgBox.style.border = '1px solid #f5c6cb';
        msgBox.style.color = '#7a1b1b';
        msgBox.textContent = 'Network error. Try again later.';
    });
});
</script>
</body>
</html>