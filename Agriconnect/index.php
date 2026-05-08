<?php
// Redirect to login page
header("Location: login.php");
exit();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Smart AgriConnect</title>
    <link rel="stylesheet" href="css/home.css">
  <link rel="stylesheet" href="css/simple-features.css">
</head>
<body>

<nav>
    <ul>
        <li><a href="index.html">Home</a></li><li><a href="marketplace.html">Marketplace</a></li><li><a href="about.html">About</a></li><li><a href="weather.html">Weather</a></li><li><a href="blog.html">Expert Tips</a></li>
        <li><a href="contact.html">Contact</a></li>
    </ul>
</nav>

<section class="hero">

    
    <div class="top-login">
        <a href="login.php" class="login-btn">Login</a>
    </div>

    <h1 id="mainText"></h1>
    <p id="subText"></p>

    <!-- Existing button -->
    <a href="marketplace.html" class="btn">Explore Crops</a>

</section>

<section class="card-container">
    <div class="card">
        <h3>Sell Crops</h3>
        <p>Farmers can list crops easily.</p>
    </div>

    <div class="card">
        <h3>Buy Direct</h3>
        <p>Buyers can purchase from farmers.</p>
    </div>

    <div class="card">
        <h3>Expert Advice</h3>
        <p>Get seasonal farming guidance.</p>
    </div>
</section>

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

<script src="home.js">

</script>

<script src="js/simple-features.js"></script>

</body>
</html>

