<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get all active services and clients
$services = getAllServices($conn, true);
$clients = getAllClients($conn, true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAM Builders and Supply Corp - Building Excellence, Delivering Quality</title>
    <meta name="description" content="Complete construction and industrial solutions for residential, commercial, and industrial projects.">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header / Navigation -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-lg">
                <a class="navbar-brand" href="#home">
                    <i class="fas fa-building" style="color: var(--primary-color); font-size: 1.8rem;"></i>
                    <span>NAM Builders</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#home">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#about">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#services">Services</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact">Contact</a>
                        </li>
                    </ul>
                    <a href="admin/login.php" class="btn btn-admin ms-3">Admin Panel</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content">
            <h1>Building Excellence, <span class="highlight">Delivering Quality</span></h1>
            <p>Complete construction and industrial solutions for residential, commercial, and industrial projects. From planning to installation, we deliver reliable and professional services.</p>
            <div class="hero-buttons">
                <a href="#services" class="btn-primary-main">Our Services</a>
                <a href="#contact" class="btn-secondary-main">Contact Us</a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="light-bg" id="about">
        <div class="container-lg">
            <div class="section-title">
                <h2>About NAM Builders</h2>
            </div>
            <div class="about-content">
                <p style="text-align: center; font-size: 1.1rem; color: var(--text-light);">
                    NAM Builders and Supply Corp is a leading construction and industrial services company providing complete solutions for residential, commercial, and industrial projects. We specialize in general construction, renovation, electrical systems, fire protection, steel fabrication, office fit-outs, and building maintenance. In addition to contracting services, we also supply construction materials, electrical components, PPE, and office supplies. With a strong focus on quality workmanship, safety, and customer satisfaction, NAM Builders delivers reliable, efficient, and professional solutions.
                </p>
            </div>

            <!-- Features Grid -->
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3>Quality Workmanship</h3>
                    <p>Expert craftsmanship in every project we undertake.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Safety First</h3>
                    <p>Committed to maintaining the highest safety standards.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Customer Satisfaction</h3>
                    <p>Dedicated to exceeding client expectations.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h3>Professional Service</h3>
                    <p>Reliable and efficient solutions from start to finish.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services">
        <div class="container-lg">
            <div class="section-title">
                <h2>Our Services</h2>
                <p>Comprehensive construction and industrial solutions tailored to your needs</p>
            </div>

            <div class="services-grid">
                <?php if (!empty($services)): ?>
                    <?php foreach ($services as $service): ?>
                        <div class="service-card">
                            <div class="service-image">
                                <?php if (!empty($service['image_path']) && file_exists(UPLOADS_PATH . $service['image_path'])): ?>
                                    <img src="<?php echo UPLOADS_URL . $service['image_path']; ?>" alt="<?php echo sanitize($service['service_name']); ?>">
                                <?php else: ?>
                                    <div class="service-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="service-content">
                                <h3><?php echo sanitize($service['service_name']); ?></h3>
                                <p><?php echo sanitize($service['description']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; grid-column: 1/-1;">No services available at the moment.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Clients Carousel Section -->
    <section class="clients-section light-bg" id="clients">
        <div class="container-lg">
            <div class="section-title">
                <h2>Our Trusted Clients</h2>
                <p>Partnering with industry leaders to deliver excellence</p>
            </div>

            <div class="clients-carousel">
                <div class="carousel-wrapper" id="carouselWrapper">
                    <?php 
                    // Display clients twice for seamless looping
                    foreach ($clients as $client): 
                    ?>
                        <div class="carousel-item">
                            <?php if (!empty($client['image_path']) && file_exists(UPLOADS_PATH . $client['image_path'])): ?>
                                <img src="<?php echo UPLOADS_URL . $client['image_path']; ?>" alt="<?php echo sanitize($client['client_name']); ?>">
                            <?php else: ?>
                                <div class="carousel-placeholder">
                                    <i class="fas fa-image"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; 
                    
                    // Duplicate for seamless loop
                    foreach ($clients as $client): 
                    ?>
                        <div class="carousel-item">
                            <?php if (!empty($client['image_path']) && file_exists(UPLOADS_PATH . $client['image_path'])): ?>
                                <img src="<?php echo UPLOADS_URL . $client['image_path']; ?>" alt="<?php echo sanitize($client['client_name']); ?>">
                            <?php else: ?>
                                <div class="carousel-placeholder">
                                    <i class="fas fa-image"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact">
        <div class="container-lg">
            <div class="section-title">
                <h2>Get In Touch</h2>
                <p>Ready to start your project? Contact us today for a consultation</p>
            </div>

            <div class="contact-form">
                <?php displayAlert(); ?>
                <form method="POST" action="backend/submit_contact.php">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fullName">Full Name</label>
                                <input type="text" id="fullName" name="full_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="tel" id="phone" name="phone" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="service">Service Needed</label>
                                <select id="service" name="service_needed" class="form-control">
                                    <option value="">Select a service</option>
                                    <?php foreach ($services as $service): ?>
                                        <option value="<?php echo sanitize($service['service_name']); ?>">
                                            <?php echo sanitize($service['service_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" class="form-control" required></textarea>
                    </div>

                    <button type="submit" class="btn-submit">Send Message</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container-lg">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>
                        <i class="fas fa-building"></i> NAM Builders
                    </h3>
                    <p>Complete construction and industrial solutions with a focus on quality, safety, and customer satisfaction.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#clients">Clients</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact Us</h3>
                    <div class="contact-info">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Your Address Here City, Country</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-phone"></i>
                        <span>+1 XXX XXX XXXX</span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-envelope"></i>
                        <span>info@nambuilders.com</span>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 NAM Builders and Supply Corp. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/carousel.js"></script>
</body>
</html>
