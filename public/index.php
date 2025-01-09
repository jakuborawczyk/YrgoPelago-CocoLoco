<!DOCTYPE html>
<html>
<head>
  <title>Coco-Loco Resort</title>
    <link rel="stylesheet" href="../src/styles.css">
</head>
<body>
  <nav class="navbar">
    <ul>
      <li><a href="#home">Home</a></li>
      <li><a href="#rooms">Rooms</a></li>
      <li><a href="#features">Features</a></li>
      <li><a href="#testimonials">Testimonials</a></li>
    </ul>
  </nav>

  <div id="home" class="hero" style="animation: fadeIn 1s ease-out;">
    <div class="hero-content">
    <h1 style="animation: fadeInDown 1s ease-out;">Coco-Loco Resort</h1>
    <p style="animation: fadeInUp 1s ease-out;">Your Paradise Getaway Awaits</p>
      <a href="#rooms" class="btn" style="animation: fadeInUp 1s ease-out;" >Explore Rooms</a>
    </div>
    <div class="scroll-indicator">↓</div>
  </div>

  <section id="rooms" class="rooms">
    <h2 class="section-title scroll-reveal">Our Accommodations</h2>
    <div class="room-grid">
      <div class="room-card scroll-reveal">
        <div class="room-image" style="background-image: url('../public/assets/luxury.webp')"></div>
        <div class="room-content">
          <h3>Luxury Suite</h3>
          <p>Ocean-front villa with private pool and premium amenities</p>
          <span class="price">From $500/night</span>
        </div>
      </div>

      <div class="room-card scroll-reveal">
        <div class="room-image" style="background-image: url('../public/assets/standard.webp')"></div>
        <div class="room-content">
          <h3>Standard Room</h3>
          <p>Comfortable room with garden view and modern furnishings</p>
          <span class="price">From $250/night</span>
        </div>
      </div>

      <div class="room-card scroll-reveal">
        <div class="room-image" style="background-image: url('../public/assets/budget.webp')"></div>
        <div class="room-content">
          <h3>Budget Room</h3>
          <p>Cozy room with essential amenities and resort access</p>
          <span class="price">From $150/night</span>
        </div>
      </div>
    </div>
  </section>

  <section id="features" class="features">
    <h2 class="section-title scroll-reveal">Resort Features</h2>
    <div class="feature-grid">
      <div class="feature-item scroll-reveal">
        <div class="feature-icon">🌴</div>
        <h3>Breakfast</h3>
        <p>Daily breakfast served on the beach</p>
      </div>
      <div class="feature-item scroll-reveal">
        <div class="feature-icon">🍹</div>
        <h3>Mini Bar</h3>
        <p>Tropical cocktails and snacks</p>
      </div>
      <div class="feature-item scroll-reveal">
        <div class="feature-icon">🌊</div>
        <h3>Private Pool</h3>
        <p>Indoor pool with sun loungers</p>
      </div>
    </div>
    <div class="buttonContainer">
    <a href="book.php" class="btn book-btn" style="animation: fadeInUp 1s ease-out;" >Book Now</a>
    </div>
  </section>

  <section id="testimonials" class="testimonials">
    <h2 class="section-title scroll-reveal">Guest Reviews</h2>
    <div class="testimonial-grid">
      <div class="testimonial-card scroll-reveal">
        <p class="testimonial-text">"A slice of paradise! The private beach and spa services were incredible."</p>
        <p class="testimonial-author">- Dad.</p>
      </div>
      <div class="testimonial-card scroll-reveal">
        <p class="testimonial-text">"Perfect getaway with amazing views and stellar service."</p>
        <p class="testimonial-author">- Mom.</p>
      </div>
    </div>
  </section>

  <script src="../src/indexScript.js"></script>
</body>
</html>