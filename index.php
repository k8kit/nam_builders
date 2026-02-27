<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$services = getAllServices($conn, true);
$clients  = getAllClients($conn, true);

foreach ($services as &$service) {
    $sid = intval($service['id']);
    $img_result = $conn->query("SELECT * FROM service_images WHERE service_id = $sid ORDER BY sort_order ASC");
    $service['images'] = $img_result ? $img_result->fetch_all(MYSQLI_ASSOC) : [];
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
        /* ── Service Cards ── */
        #services .services-grid {
            display: grid !important;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)) !important;
            gap: 1.5rem !important;
        }
        #services .service-card {
            background: #fff !important;
            border-radius: 10px !important;
            overflow: hidden !important;
            border: 1px solid var(--border-color) !important;
            cursor: pointer !important;
            transition: transform .25s, box-shadow .25s !important;
            position: relative !important;
        }
        #services .service-card:hover {
            transform: translateY(-6px) !important;
            box-shadow: 0 12px 32px rgba(0,0,0,.15) !important;
        }
        #services .service-card:hover .svc-overlay { opacity: 1 !important; }
        #services .service-image {
            width: 100% !important; height: 185px !important;
            background: var(--light-bg) !important;
            display: flex !important; align-items: center !important;
            justify-content: center !important; overflow: hidden !important;
            position: relative !important;
        }
        #services .service-image img {
            width: 100% !important; height: 100% !important; object-fit: cover !important;
        }
        .svc-overlay {
            position: absolute !important; inset: 0 !important;
            background: rgba(21,101,192,.25) !important;
            display: flex !important; align-items: center !important;
            justify-content: center !important; opacity: 0 !important;
            transition: opacity .25s !important; pointer-events: none !important; z-index: 2 !important;
        }
        .svc-overlay i { font-size: 2.2rem !important; color: #fff !important; text-shadow: 0 2px 8px rgba(0,0,0,.5) !important; }
        .svc-name-bar {
            padding: .85rem 1rem !important; font-weight: 600 !important;
            font-size: .95rem !important; color: var(--text-dark) !important;
            background: #fff !important; text-align: center !important;
        }
        #services .service-content { display: none !important; }
        .svc-img-placeholder {
            width: 70px; height: 70px; border: 3px solid var(--border-color);
            border-radius: 8px; display: flex; align-items: center;
            justify-content: center; color: var(--border-color); font-size: 2rem;
        }

        /* ── Service Modal ── */
        #svcModal {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.7); z-index: 9999;
            align-items: center; justify-content: center; padding: 1rem;
        }
        #svcModal.open { display: flex !important; }
        .svcm-box {
            background: #fff; border-radius: 14px; max-width: 830px; width: 100%;
            box-shadow: 0 24px 70px rgba(0,0,0,.35); overflow: hidden;
            display: flex; flex-direction: row; max-height: 88vh; position: relative;
        }
        .svcm-left { flex: 0 0 55%; background: #111; position: relative; min-height: 400px; }
        .svcm-slides { position: relative; width: 100%; height: 100%; min-height: 400px; overflow: hidden; }
        .svcm-slide { position: absolute; inset: 0; opacity: 0; transition: opacity .55s; }
        .svcm-slide.on { opacity: 1; }
        .svcm-slide img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .svcm-no-img {
            width: 100%; height: 100%; min-height: 400px;
            display: flex; align-items: center; justify-content: center;
            color: #555; font-size: 4rem;
        }
        .svcm-dots {
            position: absolute; bottom: 14px; left: 0; right: 0;
            display: flex; justify-content: center; gap: 7px; z-index: 10;
        }
        .svcm-dot {
            width: 9px; height: 9px; border-radius: 50%;
            background: rgba(255,255,255,.45); border: none; padding: 0; cursor: pointer;
            transition: background .25s, transform .2s;
        }
        .svcm-dot.on { background: #fff; transform: scale(1.2); }
        .svcm-close {
            position: absolute; top: 12px; right: 12px;
            background: rgba(0,0,0,.5); color: #fff; border: none;
            border-radius: 50%; width: 36px; height: 36px; font-size: 1.25rem;
            line-height: 36px; text-align: center; cursor: pointer; z-index: 20;
        }
        .svcm-close:hover { background: rgba(0,0,0,.75); }
        .svcm-right { flex: 1; display: flex; flex-direction: column; padding: 2.2rem 2rem; overflow-y: auto; }
        .svcm-title { font-size: 1.55rem; font-weight: 700; color: var(--text-dark); margin: 0 0 .9rem; }
        .svcm-bar { width: 42px; height: 4px; background: var(--primary-color); border-radius: 3px; margin-bottom: 1.3rem; }
        .svcm-desc { color: var(--text-light); line-height: 1.8; font-size: .97rem; flex: 1; }
        .svcm-cta { padding-top: 1.8rem; }
        .svcm-cta a {
            display: inline-block; background: var(--primary-color); color: #fff;
            padding: .7rem 1.7rem; border-radius: 6px; font-weight: 600;
            text-decoration: none; transition: background .2s;
        }
        .svcm-cta a:hover { background: var(--primary-dark); color: #fff; }
        @media (max-width: 620px) {
            .svcm-box { flex-direction: column; max-height: 95vh; }
            .svcm-left { flex: none; min-height: 230px; }
            .svcm-slides, .svcm-no-img { min-height: 230px; }
            .svcm-right { padding: 1.4rem; }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-lg">
                <a class="navbar-brand" href="#home">
                    <img src="uploads/nam-logo.png" alt="NAM Builders" style="height:55px;width:auto;"
                         onerror="this.outerHTML='<span style=\'display:flex;align-items:center;gap:.5rem;\'><i class=\'fas fa-building\' style=\'color:var(--primary-color);font-size:1.8rem;\'></i><span>NAM Builders and Supply Corp</span></span>'">
                         <span >NAM Builders and Supply Corp</span>
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

    <!-- Hero -->
    <section class="hero" id="home">
        <div class="hero-content">
            <h1>Building Excellence, <span class="highlight">Delivering Quality</span></h1>
            <p>Complete construction and industrial solutions for residential, commercial, and industrial projects.</p>
            <div class="hero-buttons">
                <a href="#services" class="btn-primary-main">Our Services</a>
                <a href="#contact" class="btn-secondary-main">Contact Us</a>
            </div>
        </div>
    </section>

    <!-- About -->
    <section class="light-bg" id="about">
        <div class="container-lg">
            <div class="section-title"><h2>About NAM Builders</h2></div>
            <div class="about-content">
                <p style="text-align:center;font-size:1.1rem;color:var(--text-light);">
                    NAM Builders and Supply Corp is a leading construction and industrial services company providing complete solutions for residential, commercial, and industrial projects. We specialize in general construction, renovation, electrical systems, fire protection, steel fabrication, office fit-outs, and building maintenance. In addition to contracting services, we also supply construction materials, electrical components, PPE, and office supplies.
                </p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-check-circle"></i></div>
                    <h3>Quality Workmanship</h3><p>Expert craftsmanship in every project we undertake.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                    <h3>Safety First</h3><p>Committed to maintaining the highest safety standards.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-users"></i></div>
                    <h3>Customer Satisfaction</h3><p>Dedicated to exceeding client expectations.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-rocket"></i></div>
                    <h3>Professional Service</h3><p>Reliable and efficient solutions from start to finish.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services -->
    <section id="services">
        <div class="container-lg">
            <div class="section-title">
                <h2>Our Services</h2>
                <p>Comprehensive solutions tailored to your needs. Click any service to learn more.</p>
            </div>
            <div class="services-grid">
                <?php if (!empty($services)): ?>
                    <?php foreach ($services as $sv):
                        /* Safe: store all data in data-* attributes as JSON — no JS string escaping issues */
                        $imgs_array = array_map(fn($i) => UPLOADS_URL . $i['image_path'], $sv['images']);
                        $data_imgs  = htmlspecialchars(json_encode($imgs_array), ENT_QUOTES, 'UTF-8');
                        $data_name  = htmlspecialchars($sv['service_name'], ENT_QUOTES, 'UTF-8');
                        $data_desc  = htmlspecialchars($sv['description'],  ENT_QUOTES, 'UTF-8');
                        $first_img  = !empty($sv['images']) ? UPLOADS_URL . $sv['images'][0]['image_path'] : '';
                    ?>
                    <div class="service-card"
                         role="button"
                         tabindex="0"
                         data-name="<?php echo $data_name; ?>"
                         data-desc="<?php echo $data_desc; ?>"
                         data-imgs="<?php echo $data_imgs; ?>">
                        <div class="service-image">
                            <?php if ($first_img): ?>
                                <img src="<?php echo $first_img; ?>" alt="<?php echo $data_name; ?>">
                            <?php else: ?>
                                <div class="svc-img-placeholder"><i class="fas fa-image"></i></div>
                            <?php endif; ?>
                            <div class="svc-overlay"><i class="fas fa-search-plus"></i></div>
                        </div>
                        <div class="svc-name-bar"><?php echo htmlspecialchars($sv['service_name']); ?></div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="grid-column:1/-1;text-align:center;color:var(--text-light);">No services available.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Service Modal -->
    <div id="svcModal" role="dialog" aria-modal="true" aria-labelledby="svcmTitle">
        <div class="svcm-box">
            <div class="svcm-left">
                <button class="svcm-close" id="svcmCloseBtn">&times;</button>
                <div class="svcm-slides" id="svcmSlides"></div>
                <div class="svcm-dots"   id="svcmDots"></div>
            </div>
            <div class="svcm-right">
                <h2 class="svcm-title" id="svcmTitle"></h2>
                <div class="svcm-bar"></div>
                <p  class="svcm-desc"  id="svcmDesc"></p>
                <div class="svcm-cta">
                    <a href="#contact" id="svcmQuoteBtn">
                        <i class="fas fa-paper-plane" style="margin-right:.4rem;"></i>Get a Quote
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Clients -->
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

    <!-- Contact -->
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
                                <label>Full Name</label>
                                <input type="text" name="full_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="tel" name="phone" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Service Needed</label>
                                <select name="service_needed" class="form-control">
                                    <option value="">Select a service</option>
                                    <?php foreach ($services as $sv): ?>
                                        <option value="<?php echo htmlspecialchars($sv['service_name']); ?>">
                                            <?php echo htmlspecialchars($sv['service_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message" class="form-control" required></textarea>
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
                <p>&copy; <?php echo date('Y'); ?> NAM Builders and Supply Corp. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/carousel.js"></script>
    <script>
    (function () {
        var modal      = document.getElementById('svcModal');
        var slidesWrap = document.getElementById('svcmSlides');
        var dotsWrap   = document.getElementById('svcmDots');
        var titleEl    = document.getElementById('svcmTitle');
        var descEl     = document.getElementById('svcmDesc');
        var cur = 0, tot = 0, tmr = null;

        /* Attach click listeners to every service card via data attributes */
        document.querySelectorAll('#services .service-card').forEach(function (card) {
            card.addEventListener('click', function () {
                var name   = card.getAttribute('data-name');
                var desc   = card.getAttribute('data-desc');
                var images = JSON.parse(card.getAttribute('data-imgs') || '[]');
                openModal(name, desc, images);
            });
            card.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') card.click();
            });
        });

        function openModal(name, desc, images) {
            titleEl.textContent = name;
            descEl.textContent  = desc;
            slidesWrap.innerHTML = '';
            dotsWrap.innerHTML   = '';
            cur = 0; tot = images.length;

            if (!tot) {
                slidesWrap.innerHTML = '<div class="svcm-no-img"><i class="fas fa-image"></i></div>';
            } else {
                images.forEach(function (src, i) {
                    var s   = document.createElement('div');
                    s.className = 'svcm-slide' + (i === 0 ? ' on' : '');
                    var img = document.createElement('img');
                    img.src = src;
                    img.alt = name;
                    s.appendChild(img);
                    slidesWrap.appendChild(s);

                    if (tot > 1) {
                        var d = document.createElement('button');
                        d.className = 'svcm-dot' + (i === 0 ? ' on' : '');
                        d.setAttribute('aria-label', 'Image ' + (i + 1));
                        (function (idx) {
                            d.addEventListener('click', function () { goTo(idx); });
                        }(i));
                        dotsWrap.appendChild(d);
                    }
                });
            }

            modal.classList.add('open');
            document.body.style.overflow = 'hidden';
            clearInterval(tmr);
            if (tot > 1) tmr = setInterval(function () { goTo((cur + 1) % tot); }, 3000);
        }

        function goTo(idx) {
            var ss = slidesWrap.querySelectorAll('.svcm-slide');
            var ds = dotsWrap.querySelectorAll('.svcm-dot');
            if (!ss.length) return;
            ss[cur].classList.remove('on');
            if (ds[cur]) ds[cur].classList.remove('on');
            cur = idx;
            ss[cur].classList.add('on');
            if (ds[cur]) ds[cur].classList.add('on');
        }

        function closeModal() {
            modal.classList.remove('open');
            document.body.style.overflow = '';
            clearInterval(tmr);
        }

        document.getElementById('svcmCloseBtn').addEventListener('click', closeModal);
        document.getElementById('svcmQuoteBtn').addEventListener('click', closeModal);
        modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });
    }());
    </script>
</body>
</html>