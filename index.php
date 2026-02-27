<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get all active services and clients
$services = getAllServices($conn, true);
$clients  = getAllClients($conn, true);

// For each service, get its images from service_images table
foreach ($services as &$service) {
    $sid = intval($service['id']);
    $img_result = $conn->query("SELECT * FROM service_images WHERE service_id = $sid ORDER BY sort_order ASC");
    $service['images'] = $img_result ? $img_result->fetch_all(MYSQLI_ASSOC) : [];
    // Fallback: if no multi-images, use legacy image_path
    if (empty($service['images']) && !empty($service['image_path'])) {
        $service['images'] = [['image_path' => $service['image_path']]];
    }
}
unset($service);
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

    <style>
        /* ── Service Cards (image + name only) ── */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.5rem;
        }

        .service-card {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            cursor: pointer;
            transition: transform .25s ease, box-shadow .25s ease;
            position: relative;
        }
        .service-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 32px rgba(0,0,0,.12);
        }
        .service-card:hover .service-overlay {
            opacity: 1;
        }

        .service-image {
            width: 100%;
            height: 180px;
            background: var(--light-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        .service-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .service-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255,87,34,.18);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity .25s ease;
        }
        .service-overlay i {
            font-size: 2.2rem;
            color: #fff;
            text-shadow: 0 2px 8px rgba(0,0,0,.4);
        }

        .service-name-bar {
            padding: .85rem 1rem;
            font-weight: 600;
            font-size: 1rem;
            color: var(--text-dark);
            background: #fff;
            text-align: center;
        }

        /* ── Service Detail Modal ── */
        #serviceDetailModal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.65);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        #serviceDetailModal.active { display: flex; }

        .sdm-box {
            background: #fff;
            border-radius: 14px;
            max-width: 820px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
            overflow: hidden;
            display: flex;
            flex-direction: row;
            max-height: 90vh;
        }

        /* LEFT – image slider */
        .sdm-left {
            flex: 0 0 55%;
            background: #1a1a1a;
            position: relative;
            min-height: 380px;
        }
        .sdm-slider {
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        .sdm-slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity .5s ease;
        }
        .sdm-slide.active { opacity: 1; }
        .sdm-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        /* dot indicators */
        .sdm-dots {
            position: absolute;
            bottom: 12px;
            left: 0;
            right: 0;
            display: flex;
            justify-content: center;
            gap: 6px;
            z-index: 10;
        }
        .sdm-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,.5);
            cursor: pointer;
            border: none;
            padding: 0;
            transition: background .25s;
        }
        .sdm-dot.active { background: #fff; }

        /* RIGHT – text content */
        .sdm-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 2rem 1.8rem;
            overflow-y: auto;
        }
        .sdm-close {
            position: absolute;
            top: 14px;
            right: 14px;
            background: rgba(0,0,0,.45);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 34px;
            height: 34px;
            font-size: 1.2rem;
            cursor: pointer;
            z-index: 20;
            line-height: 34px;
            text-align: center;
            transition: background .2s;
        }
        .sdm-close:hover { background: rgba(0,0,0,.7); }

        .sdm-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1rem;
            line-height: 1.3;
        }
        .sdm-divider {
            width: 40px;
            height: 3px;
            background: var(--primary-color);
            border-radius: 2px;
            margin-bottom: 1.2rem;
        }
        .sdm-desc {
            color: var(--text-light);
            line-height: 1.75;
            font-size: .97rem;
        }
        .sdm-cta {
            margin-top: auto;
            padding-top: 1.5rem;
        }
        .sdm-cta a {
            display: inline-block;
            background: var(--primary-color);
            color: #fff;
            padding: .65rem 1.5rem;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            transition: background .2s;
        }
        .sdm-cta a:hover { background: var(--primary-dark); color: #fff; }

        /* placeholder when no image */
        .sdm-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #555;
            font-size: 3rem;
        }

        @media (max-width: 640px) {
            .sdm-box { flex-direction: column; }
            .sdm-left  { flex: none; min-height: 240px; }
            .sdm-right { padding: 1.4rem; }
        }
    </style>
</head>
<body>
    <!-- Header / Navigation -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-lg">
                <a class="navbar-brand" href="#home">
                    <img src="uploads/nam-logo.png" alt="NAM Builders and Supply Corp" style="height: 55px; width: auto;">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                        <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    </ul>
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

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-check-circle"></i></div>
                    <h3>Quality Workmanship</h3>
                    <p>Expert craftsmanship in every project we undertake.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                    <h3>Safety First</h3>
                    <p>Committed to maintaining the highest safety standards.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-users"></i></div>
                    <h3>Customer Satisfaction</h3>
                    <p>Dedicated to exceeding client expectations.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-rocket"></i></div>
                    <h3>Professional Service</h3>
                    <p>Reliable and efficient solutions from start to finish.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ══ Services Section ══ -->
    <section id="services">
        <div class="container-lg">
            <div class="section-title">
                <h2>Our Services</h2>
                <p>Comprehensive construction and industrial solutions tailored to your needs. Click any service to learn more.</p>
            </div>

            <div class="services-grid">
                <?php if (!empty($services)): ?>
                    <?php foreach ($services as $service): 
                        // Build images JSON for JS
                        $imgs_json = json_encode(array_map(function($img) {
                            return UPLOADS_URL . $img['image_path'];
                        }, $service['images']));
                        $first_img_url = !empty($service['images']) ? UPLOADS_URL . $service['images'][0]['image_path'] : '';
                        $name_escaped  = htmlspecialchars($service['service_name'], ENT_QUOTES);
                        $desc_escaped  = htmlspecialchars($service['description'], ENT_QUOTES);
                    ?>
                        <div class="service-card"
                             onclick="openServiceModal(<?php echo $service['id']; ?>, '<?php echo $name_escaped; ?>', '<?php echo $desc_escaped; ?>', <?php echo $imgs_json; ?>)">
                            <div class="service-image">
                                <?php if ($first_img_url): ?>
                                    <img src="<?php echo $first_img_url; ?>" alt="<?php echo $name_escaped; ?>">
                                <?php else: ?>
                                    <div class="service-placeholder"><i class="fas fa-image"></i></div>
                                <?php endif; ?>
                                <div class="service-overlay">
                                    <i class="fas fa-search-plus"></i>
                                </div>
                            </div>
                            <div class="service-name-bar"><?php echo sanitize($service['service_name']); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align:center; grid-column:1/-1; color:var(--text-light);">No services available at the moment.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ══ Service Detail Modal ══ -->
    <div id="serviceDetailModal" onclick="handleModalBackdropClick(event)">
        <div class="sdm-box">
            <!-- LEFT: image slider -->
            <div class="sdm-left">
                <button class="sdm-close" onclick="closeServiceModal()" title="Close">&times;</button>
                <div class="sdm-slider" id="sdmSlider">
                    <!-- slides injected by JS -->
                </div>
                <div class="sdm-dots" id="sdmDots"></div>
            </div>
            <!-- RIGHT: info -->
            <div class="sdm-right">
                <h2 class="sdm-title" id="sdmTitle"></h2>
                <div class="sdm-divider"></div>
                <p class="sdm-desc" id="sdmDesc"></p>
                <div class="sdm-cta">
                    <a href="#contact" onclick="closeServiceModal()">Get a Quote</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Clients Carousel Section -->
    <section class="clients-section light-bg" id="clients">
        <div class="container-lg">
            <div class="section-title">
                <h2>Our Trusted Clients</h2>
                <p>Partnering with industry leaders to deliver excellence</p>
            </div>
            <div class="clients-carousel">
                <div class="carousel-wrapper" id="carouselWrapper">
                    <?php foreach ($clients as $client): ?>
                        <div class="carousel-item">
                            <?php if (!empty($client['image_path']) && file_exists(UPLOADS_PATH . $client['image_path'])): ?>
                                <img src="<?php echo UPLOADS_URL . $client['image_path']; ?>" alt="<?php echo sanitize($client['client_name']); ?>">
                            <?php else: ?>
                                <div class="carousel-placeholder"><i class="fas fa-image"></i></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <?php foreach ($clients as $client): ?>
                        <div class="carousel-item">
                            <?php if (!empty($client['image_path']) && file_exists(UPLOADS_PATH . $client['image_path'])): ?>
                                <img src="<?php echo UPLOADS_URL . $client['image_path']; ?>" alt="<?php echo sanitize($client['client_name']); ?>">
                            <?php else: ?>
                                <div class="carousel-placeholder"><i class="fas fa-image"></i></div>
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
                    <h3><i class="fas fa-building"></i> NAM Builders</h3>
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
                    <div class="contact-info"><i class="fas fa-map-marker-alt"></i><span>Your Address Here City, Country</span></div>
                    <div class="contact-info"><i class="fas fa-phone"></i><span>+1 XXX XXX XXXX</span></div>
                    <div class="contact-info"><i class="fas fa-envelope"></i><span>info@nambuilders.com</span></div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 NAM Builders and Supply Corp. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/carousel.js"></script>
    <script>
    /* ══ Service Detail Modal Logic ══ */
    let slideInterval = null;
    let currentSlide  = 0;
    let totalSlides   = 0;

    function openServiceModal(id, name, desc, images) {
        document.getElementById('sdmTitle').textContent = name;
        document.getElementById('sdmDesc').textContent  = desc;

        const slider = document.getElementById('sdmSlider');
        const dots   = document.getElementById('sdmDots');
        slider.innerHTML = '';
        dots.innerHTML   = '';
        currentSlide = 0;
        totalSlides  = images.length;

        if (images.length === 0) {
            slider.innerHTML = '<div class="sdm-placeholder"><i class="fas fa-image"></i></div>';
        } else {
            images.forEach(function(src, i) {
                const slide = document.createElement('div');
                slide.className = 'sdm-slide' + (i === 0 ? ' active' : '');
                const img = document.createElement('img');
                img.src = src;
                img.alt = name;
                slide.appendChild(img);
                slider.appendChild(slide);

                if (images.length > 1) {
                    const dot = document.createElement('button');
                    dot.className = 'sdm-dot' + (i === 0 ? ' active' : '');
                    dot.setAttribute('aria-label', 'Slide ' + (i+1));
                    dot.addEventListener('click', function() { goToSlide(i); });
                    dots.appendChild(dot);
                }
            });
        }

        document.getElementById('serviceDetailModal').classList.add('active');
        document.body.style.overflow = 'hidden';

        // Auto-advance every 3 seconds if multiple images
        clearInterval(slideInterval);
        if (images.length > 1) {
            slideInterval = setInterval(function() {
                goToSlide((currentSlide + 1) % totalSlides);
            }, 3000);
        }
    }

    function goToSlide(index) {
        const slides = document.querySelectorAll('.sdm-slide');
        const dots   = document.querySelectorAll('.sdm-dot');
        if (!slides.length) return;

        slides[currentSlide].classList.remove('active');
        if (dots[currentSlide]) dots[currentSlide].classList.remove('active');

        currentSlide = index;
        slides[currentSlide].classList.add('active');
        if (dots[currentSlide]) dots[currentSlide].classList.add('active');
    }

    function closeServiceModal() {
        document.getElementById('serviceDetailModal').classList.remove('active');
        document.body.style.overflow = '';
        clearInterval(slideInterval);
    }

    function handleModalBackdropClick(e) {
        if (e.target === document.getElementById('serviceDetailModal')) {
            closeServiceModal();
        }
    }

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeServiceModal();
    });
    </script>
</body>
</html>