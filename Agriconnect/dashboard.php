<?php
require 'session_protect.php';
ensure_logged_in();

$usertype = $_SESSION['usertype'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Smart AgriConnect</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <style>
        .role-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            margin-left: 15px;
        }
        .farmer-badge {
            background-color: #d4edda;
            color: #155724;
        }
        .buyer-badge {
            background-color: #cce5ff;
            color: #004085;
        }
    </style>
</head>
<body>

<nav>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <?php if ($usertype === 'farmer'): ?>
            <li><a href="mylistings.php">My Listings</a></li>
            <li><a href="myorders.php">My Orders</a></li>
        <?php else: ?>
            <li><a href="marketplace.php">Browse Products</a></li>
            <li><a href="myorders.php">My Orders</a></li>
            <li><a href="connect_farmers.php">Contact</a></li>
        <?php endif; ?>
        <?php if ($usertype === 'farmer'): ?>
           
        <?php endif; ?>
        <li><a href="logout.php" style="background-color: #d9534f; border-radius: 999px; padding: 0.45rem 0.8rem;">Logout</a></li>
    </ul>
</nav>

<?php if ($usertype === 'farmer'): ?>
    <!-- FARMER DASHBOARD -->
    <section class="hero">
        <div class="welcome-box">
            <h1>
                Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!
                <span class="role-badge farmer-badge">Farmer</span>
            </h1>
            <p>Manage Your Farm & Sell Your Crops</p>
        </div>
        <a href="mylistings.php" class="btn">Manage My Crops</a>
    </section>

    <section class="card-container">
        <div class="card" onclick="window.location.href='mylistings.php'" style="cursor: pointer;">
            <h3>📋 My Crop Listings</h3>
            <p>Manage and monitor your active crop listings in the marketplace.</p>
        </div>

        <div class="card" onclick="window.location.href='myorders.php'" style="cursor: pointer;">
            <h3>📦 My Orders</h3>
            <p>Track and manage incoming orders from buyers.</p>
        </div>

        <div class="card">
            <h3>💰 Sales Analytics</h3>
            <p>Track your sales performance and earnings over time.</p>
        </div>

        <div class="card">
            <h3>🌾 Crop Management</h3>
            <p>Add, edit, or remove crops from your inventory.</p>
        </div>

        <div class="card">
            <h3>📞 Customer Support</h3>
            <p>Communicate with buyers and manage their inquiries.</p>
        </div>

        <div class="card">
            <h3>🌤️ Weather Forecast</h3>
            <p>Plan your farming activities with accurate weather data.</p>
        </div>
    </section>

<?php else: ?>
    <!-- BUYER DASHBOARD -->
    <section class="hero">
        <div class="welcome-box">
            <h1>
                Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!
                <span class="role-badge buyer-badge">Buyer</span>
            </h1>
            <p>Find the Best Quality Products from Local Farmers</p>
        </div>
        <a href="marketplace.php" class="btn">Explore Products</a>
    </section>

    <section class="card-container">
        <div class="card" onclick="window.location.href='marketplace.php'" style="cursor: pointer;">
            <h3>🛒 Browse Products</h3>
            <p>Discover fresh produce and agricultural products from local farmers.</p>
        </div>

        <div class="card" onclick="window.location.href='myorders.php'" style="cursor: pointer;">
            <h3>📦 My Orders</h3>
            <p>View order history and track current deliveries.</p>
        </div>

        <div class="card" onclick="window.location.href='saved_items.php'" style="cursor: pointer;">
            <h3>❤️ Saved Items</h3>
            <p>Keep your favorite products for quick access.</p>
        </div>

        <div class="card" onclick="window.location.href='connect_farmers.php'" style="cursor: pointer;">
            <h3>👨‍🌾 Connect with Farmers</h3>
            <p>Direct messaging with farmers for bulk orders and inquiries.</p>
        </div>

        <!-- Reviews card removed per request -->
    </section>

<?php endif; ?>

<div class="slideshow-container">
  <div class="slide fade">
    <img src="pic1.jpeg" width="100%">
  </div>

  <div class="slide fade">
    <img src="pic2.jpeg" width="100%">
  </div>

  <div class="slide fade">
    <img src="pic3.jpeg" width="100%">
  </div>

  <div class="slide fade">
    <img src="pic4.jpeg" width="100%">
  </div>

  <div class="slide fade">
    <img src="pic5.jpeg" width="100%">
  </div>

  <!-- Buttons -->
  <a class="prev">&#10094;</a>
  <a class="next">&#10095;</a>
</div>

<footer>
    © 2026 Smart AgriConnect | All Rights Reserved
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="home.js"></script>
<script src="js/simple-features.js"></script>

</body>
</html>