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
        /* ── Verification Modal ── */
        #verifyModal {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,.65);
            z-index: 10000;
            align-items: center; justify-content: center;
            padding: 1rem;
        }
        #verifyModal.open { display: flex !important; animation: vModalIn .25s ease; }
        @keyframes vModalIn { from { opacity: 0; } to { opacity: 1; } }

        .vm-box {
            background: #fff; border-radius: 16px;
            max-width: 420px; width: 100%;
            box-shadow: 0 24px 70px rgba(0,0,0,.3);
            overflow: hidden;
            animation: vBoxIn .3s ease;
        }
        @keyframes vBoxIn {
            from { transform: translateY(24px) scale(.97); opacity: 0; }
            to   { transform: translateY(0) scale(1); opacity: 1; }
        }
        .vm-header {
            background: var(--primary-color);
            padding: 1.6rem 1.8rem 1.3rem;
            position: relative;
        }
        .vm-header h3 { color: #fff; margin: 0; font-size: 1.2rem; font-weight: 800; }
        .vm-header p  { color: rgba(255,255,255,.8); font-size: .85rem; margin: .4rem 0 0; }
        .vm-header .vm-icon {
            display: flex; align-items: center; justify-content: center;
            width: 52px; height: 52px; background: rgba(255,255,255,.15);
            border-radius: 50%; margin-bottom: 1rem;
            font-size: 1.5rem; color: #fff;
        }
        .vm-close-btn {
            position: absolute; top: 1rem; right: 1rem;
            background: rgba(255,255,255,.2); border: none;
            border-radius: 50%; width: 30px; height: 30px;
            color: #fff; font-size: 1.1rem; line-height: 30px;
            text-align: center; cursor: pointer; transition: background .2s;
        }
        .vm-close-btn:hover { background: rgba(255,255,255,.35); }
        .vm-body { padding: 1.8rem; }
        .vm-email-display {
            background: var(--light-bg); border: 1.5px solid var(--border-color);
            border-radius: 8px; padding: .7rem 1rem; font-size: .9rem;
            color: var(--text-dark); font-weight: 600;
            display: flex; align-items: center; gap: .5rem;
            margin-bottom: 1.4rem; word-break: break-all;
        }
        .vm-email-display i { color: var(--primary-color); flex-shrink: 0; }
        .vm-code-label {
            font-size: .85rem; font-weight: 700; color: var(--text-dark);
            letter-spacing: .04em; text-transform: uppercase;
            margin-bottom: .6rem; display: block;
        }
        .vm-digit-row { display: flex; gap: .5rem; justify-content: center; margin-bottom: 1.2rem; }
        .vm-digit {
            width: 48px; height: 58px; border: 2px solid var(--border-color);
            border-radius: 10px; font-size: 1.6rem; font-weight: 800;
            text-align: center; line-height: 1; color: var(--text-dark);
            background: #fff; transition: border-color .2s, box-shadow .2s;
            caret-color: var(--primary-color); outline: none;
        }
        .vm-digit:focus { border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(21,101,192,.12); }
        .vm-digit.filled { border-color: var(--primary-color); background: #F0F4FA; }
        .vm-digit.error {
            border-color: var(--danger-color) !important; background: #FFF5F5;
            animation: shake .35s ease;
        }
        @keyframes shake {
            0%,100% { transform: translateX(0); }
            20%     { transform: translateX(-5px); }
            60%     { transform: translateX(5px); }
            80%     { transform: translateX(-3px); }
        }
        .vm-timer { text-align: center; font-size: .82rem; color: var(--text-light); margin-bottom: 1.2rem; }
        .vm-timer strong { color: var(--primary-color); }
        .vm-timer.expired strong { color: var(--danger-color); }
        .vm-submit-btn {
            background: var(--primary-color); color: #fff; border: none;
            width: 100%; padding: .85rem; border-radius: 8px; font-size: 1rem;
            font-weight: 700; font-family: inherit; letter-spacing: .04em;
            cursor: pointer; transition: background .25s, transform .15s, box-shadow .2s;
        }
        .vm-submit-btn:hover:not(:disabled) {
            background: var(--primary-dark); transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(21,101,192,.3);
        }
        .vm-submit-btn:disabled { background: #9ab5d8; cursor: not-allowed; }
        .vm-resend { text-align: center; margin-top: 1rem; font-size: .85rem; color: var(--text-light); }
        .vm-resend button {
            background: none; border: none; padding: 0;
            color: var(--primary-color); font-weight: 700;
            cursor: pointer; text-decoration: underline;
            font-size: .85rem; font-family: inherit;
        }
        .vm-resend button:disabled { color: var(--text-light); cursor: not-allowed; text-decoration: none; }
        .vm-alert { border-radius: 8px; padding: .7rem 1rem; font-size: .85rem; margin-bottom: 1rem; display: none; }
        .vm-alert.show { display: flex; align-items: center; gap: .5rem; }
        .vm-alert.success { background: #D4EDDA; color: #155724; border: 1px solid #C3E6CB; }
        .vm-alert.error   { background: #F8D7DA; color: #721C24; border: 1px solid #F5C6CB; }
        .vm-alert.info    { background: #D1ECF1; color: #0C5460; border: 1px solid #BEE5EB; }
        .vm-progress { display: flex; justify-content: center; gap: 4px; margin-bottom: .8rem; }
        .vm-prog-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--border-color); transition: background .2s; }
        .vm-prog-dot.filled { background: var(--primary-color); }

        /* ── Inline success banner (shown after modal closes) ── */
        #contactSuccessBanner {
            display: none;
            background: #D4EDDA; color: #155724;
            border: 1px solid #C3E6CB;
            border-radius: 10px; padding: 1.2rem 1.4rem;
            margin-bottom: 1.5rem;
            align-items: center; gap: .8rem;
            font-weight: 600; font-size: .97rem;
        }
        #contactSuccessBanner.show { display: flex; animation: bannerIn .4s ease; }
        @keyframes bannerIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
        #contactSuccessBanner i { font-size: 1.3rem; flex-shrink: 0; }
    </style>
</head>
<body>

    <!-- ── Header ── -->
    <header id="mainHeader">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-lg">
                <a class="navbar-brand" href="#home">
                    <img src="uploads/nam-logo.png" alt="NAM Builders" onerror="this.style.display='none'">
                    <span>NAM Builders <span style="color:var(--primary-color);">&amp; Supply Corp.</span></span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-lg-center">
                        <li class="nav-item"><a class="nav-link" href="#home"     data-section="home">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="#about"    data-section="about">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="#services" data-section="services">Services</a></li>
                        <li class="nav-item"><a class="nav-link" href="#clients"  data-section="clients">Clients</a></li>
                        <li class="nav-item"><a class="nav-link" href="#contact"  data-section="contact">Contact</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- ── Hero ── -->
    <section class="hero" id="home">
        <div class="hero-content">
            <span class="hero-eyebrow">
                <i class="fas fa-hard-hat" style="margin-right:.4rem;"></i>
                Trusted Construction Partner
            </span>
            <h1>Built for Business,<br><span class="highlight">Powered by Supply</span></h1>
            <p>Complete construction and industrial solutions for residential, commercial, and industrial projects.</p>
            <div class="hero-buttons">
                <a href="#services" class="btn-primary-main">
                    <i class="fas fa-cogs" style="margin-right:.5rem;"></i>Our Services
                </a>
                <a href="#contact" class="btn-secondary-main">
                    <i class="fas fa-paper-plane" style="margin-right:.5rem;"></i>Get a Quote
                </a>
            </div>
        </div>
        <div class="hero-scroll-hint">
            <small>Scroll</small>
            <span><i class="fas fa-chevron-down"></i></span>
        </div>
    </section>

    <!-- ── Stats Bar ── -->
    <div class="stats-bar" id="stats">
        <div class="container-lg">
            <div class="stats-grid">
                <div class="stat-item reveal">
                    <span class="stat-number"><span class="counter" data-target="150">0</span>+</span>
                    <span class="stat-label">Projects Completed</span>
                </div>
                <div class="stat-item reveal reveal-delay-1">
                    <span class="stat-number"><span class="counter" data-target="50">0</span>+</span>
                    <span class="stat-label">Happy Clients</span>
                </div>
                <div class="stat-item reveal reveal-delay-2">
                    <span class="stat-number"><span class="counter" data-target="15">0</span>+</span>
                    <span class="stat-label">Years Experience</span>
                </div>
                <div class="stat-item reveal reveal-delay-3">
                    <span class="stat-number"><span class="counter" data-target="8">0</span></span>
                    <span class="stat-label">Service Categories</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ── About ── -->
    <section class="light-bg" id="about">
        <div class="container-lg">
            <div class="section-title reveal">
                <span class="section-tag">Who We Are</span>
                <h2>About NAM Builders</h2>
                <p>Over a decade of building trust, one project at a time.</p>
            </div>
            <div class="about-content reveal">
                <p style="text-align:center;font-size:1.08rem;color:var(--text-light);max-width:680px;margin:0 auto;">
                    NAM Builders and Supply Corp is a leading construction and industrial services company providing complete solutions for residential, commercial, and industrial projects. We specialize in general construction, renovation, electrical systems, fire protection, steel fabrication, office fit-outs, and building maintenance.
                </p>
            </div>
            <div class="features-grid">
                <div class="feature-card reveal reveal-delay-1">
                    <div class="feature-icon"><i class="fas fa-medal"></i></div>
                    <h3>Quality Workmanship</h3>
                    <p>Expert craftsmanship and strict quality control in every project we undertake.</p>
                </div>
                <div class="feature-card reveal reveal-delay-2">
                    <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                    <h3>Safety First</h3>
                    <p>Committed to maintaining the highest safety standards on every job site.</p>
                </div>
                <div class="feature-card reveal reveal-delay-3">
                    <div class="feature-icon"><i class="fas fa-handshake"></i></div>
                    <h3>Client Satisfaction</h3>
                    <p>Dedicated to exceeding expectations and building lasting partnerships.</p>
                </div>
                <div class="feature-card reveal reveal-delay-4">
                    <div class="feature-icon"><i class="fas fa-rocket"></i></div>
                    <h3>On-Time Delivery</h3>
                    <p>Reliable and efficient project delivery from planning to completion.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Services ── -->
    <section id="services">
        <div class="container-lg">
            <div class="section-title reveal">
                <span class="section-tag">What We Do</span>
                <h2>Our Services</h2>
                <p>Comprehensive solutions tailored to your needs. Click any service to learn more.</p>
            </div>
            <div class="services-grid">
                <?php if (!empty($services)): ?>
                    <?php foreach ($services as $idx => $sv):
                        $imgs_array = array_map(fn($i) => UPLOADS_URL . $i['image_path'], $sv['images']);
                        $data_imgs  = htmlspecialchars(json_encode($imgs_array), ENT_QUOTES, 'UTF-8');
                        $data_name  = htmlspecialchars($sv['service_name'], ENT_QUOTES, 'UTF-8');
                        $data_desc  = htmlspecialchars($sv['description'],  ENT_QUOTES, 'UTF-8');
                        $first_img  = !empty($sv['images']) ? UPLOADS_URL . $sv['images'][0]['image_path'] : '';
                        $delay      = ($idx % 4);
                    ?>
                    <div class="service-card reveal reveal-delay-<?php echo $delay; ?>"
                         role="button" tabindex="0"
                         data-name="<?php echo $data_name; ?>"
                         data-desc="<?php echo $data_desc; ?>"
                         data-imgs="<?php echo $data_imgs; ?>">
                        <div class="service-image">
                            <?php if ($first_img): ?>
                                <img src="<?php echo $first_img; ?>" alt="<?php echo $data_name; ?>" loading="lazy">
                            <?php else: ?>
                                <div class="svc-img-placeholder"><i class="fas fa-hard-hat"></i></div>
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

    <!-- ── Service Modal ── -->
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

    <!-- ── Clients ── -->
    <section class="clients-section light-bg" id="clients">
        <div class="container-lg">
            <div class="section-title reveal">
                <span class="section-tag">Who We Work With</span>
                <h2>Our Trusted Clients</h2>
                <p>Partnering with industry leaders to deliver excellence.</p>
            </div>
            <div class="clients-carousel reveal">
                <div class="carousel-wrapper" id="carouselWrapper">
                    <?php foreach ($clients as $client): ?>
                        <div class="carousel-item">
                            <?php if (!empty($client['image_path']) && file_exists(UPLOADS_PATH . $client['image_path'])): ?>
                                <img src="<?php echo UPLOADS_URL . $client['image_path']; ?>" alt="<?php echo sanitize($client['client_name']); ?>">
                            <?php else: ?>
                                <div class="carousel-placeholder"><i class="fas fa-building"></i></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <?php foreach ($clients as $client): ?>
                        <div class="carousel-item">
                            <?php if (!empty($client['image_path']) && file_exists(UPLOADS_PATH . $client['image_path'])): ?>
                                <img src="<?php echo UPLOADS_URL . $client['image_path']; ?>" alt="<?php echo sanitize($client['client_name']); ?>">
                            <?php else: ?>
                                <div class="carousel-placeholder"><i class="fas fa-building"></i></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Contact ── -->
    <section id="contact">
        <div class="container-lg">
            <div class="section-title reveal">
                <span class="section-tag">Reach Out</span>
                <h2>Get In Touch</h2>
                <p>Ready to start your project? Contact us today for a free consultation.</p>
            </div>
            <div class="contact-form reveal">

                <!-- ✅ Success banner shown inline after verification — replaces session-based alert -->
                <div id="contactSuccessBanner">
                    <i class="fas fa-check-circle"></i>
                    <span id="contactSuccessMsg"></span>
                </div>

                <form id="contactForm" novalidate>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-user" style="color:var(--primary-color);margin-right:.4rem;"></i>Full Name</label>
                                <input type="text" name="full_name" id="cf_name" class="form-control" placeholder="Juan dela Cruz" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-envelope" style="color:var(--primary-color);margin-right:.4rem;"></i>Email</label>
                                <input type="email" name="email" id="cf_email" class="form-control" placeholder="juan@example.com" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-phone" style="color:var(--primary-color);margin-right:.4rem;"></i>Phone</label>
                                <input type="tel" name="phone" id="cf_phone" class="form-control" placeholder="+63 9XX XXX XXXX">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-cogs" style="color:var(--primary-color);margin-right:.4rem;"></i>Service Needed</label>
                                <select name="service_needed" id="cf_service" class="form-control">
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
                        <label><i class="fas fa-comment-dots" style="color:var(--primary-color);margin-right:.4rem;"></i>Message</label>
                        <textarea name="message" id="cf_message" class="form-control" placeholder="Tell us about your project..." required></textarea>
                    </div>
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-paper-plane" style="margin-right:.5rem;"></i>Send Message
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- ── Verification Modal ── -->
    <div id="verifyModal" role="dialog" aria-modal="true" aria-labelledby="vmTitle">
        <div class="vm-box">
            <div class="vm-header">
                <button class="vm-close-btn" id="vmCloseBtn" title="Cancel">&times;</button>
                <div class="vm-icon"><i class="fas fa-shield-alt"></i></div>
                <h3 id="vmTitle">Verify Your Email</h3>
                <p>We sent a 6-digit code to your email address.</p>
            </div>
            <div class="vm-body">
                <div class="vm-alert" id="vmAlert"></div>
                <div class="vm-email-display">
                    <i class="fas fa-envelope"></i>
                    <span id="vmEmailDisplay">—</span>
                </div>
                <span class="vm-code-label">Enter 6-digit code</span>
                <div class="vm-digit-row" id="vmDigitRow">
                    <input class="vm-digit" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]" autocomplete="one-time-code" id="vd0">
                    <input class="vm-digit" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]" id="vd1">
                    <input class="vm-digit" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]" id="vd2">
                    <input class="vm-digit" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]" id="vd3">
                    <input class="vm-digit" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]" id="vd4">
                    <input class="vm-digit" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]" id="vd5">
                </div>
                <div class="vm-progress" id="vmProgress">
                    <div class="vm-prog-dot" id="vp0"></div>
                    <div class="vm-prog-dot" id="vp1"></div>
                    <div class="vm-prog-dot" id="vp2"></div>
                    <div class="vm-prog-dot" id="vp3"></div>
                    <div class="vm-prog-dot" id="vp4"></div>
                    <div class="vm-prog-dot" id="vp5"></div>
                </div>
                <div class="vm-timer" id="vmTimer">
                    Code expires in <strong id="vmCountdown">10:00</strong>
                </div>
                <button class="vm-submit-btn" id="vmVerifyBtn" disabled>
                    <i class="fas fa-check-circle" style="margin-right:.4rem;"></i>Verify &amp; Send Message
                </button>
                <div class="vm-resend">
                    Didn't receive the code?
                    <button id="vmResendBtn" disabled>Resend Code</button>
                    <span id="vmResendTimer"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Footer ── -->
    <footer>
        <div class="container-lg">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><i class="fas fa-building" style="color:#64B5F6;margin-right:.4rem;"></i>NAM Builders</h3>
                    <p>Complete construction and industrial solutions with a focus on quality, safety, and client satisfaction.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#home"><i class="fas fa-chevron-right" style="font-size:.7rem;margin-right:.4rem;"></i>Home</a></li>
                        <li><a href="#about"><i class="fas fa-chevron-right" style="font-size:.7rem;margin-right:.4rem;"></i>About</a></li>
                        <li><a href="#services"><i class="fas fa-chevron-right" style="font-size:.7rem;margin-right:.4rem;"></i>Services</a></li>
                        <li><a href="#clients"><i class="fas fa-chevron-right" style="font-size:.7rem;margin-right:.4rem;"></i>Clients</a></li>
                        <li><a href="#contact"><i class="fas fa-chevron-right" style="font-size:.7rem;margin-right:.4rem;"></i>Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact Us</h3>
                    <div class="contact-info"><i class="fas fa-map-marker-alt"></i><span>Your Address Here, Philippines</span></div>
                    <div class="contact-info"><i class="fas fa-phone"></i><span>+63 9XX XXX XXXX</span></div>
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

        /* ── 1. Navbar scroll + active link ── */
        var header   = document.getElementById('mainHeader');
        var sections = document.querySelectorAll('section[id]');
        var navLinks = document.querySelectorAll('.navbar-nav .nav-link[data-section]');
        function updateNav() {
            header.classList.toggle('scrolled', window.scrollY > 40);
            var current = '';
            sections.forEach(function (s) { if (window.scrollY >= s.offsetTop - 110) current = s.id; });
            navLinks.forEach(function (l) { l.classList.toggle('active-link', l.getAttribute('data-section') === current); });
        }
        window.addEventListener('scroll', updateNav, { passive: true });
        updateNav();

        /* ── 2. Scroll-reveal ── */
        var revObs = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('visible'); revObs.unobserve(e.target); } });
        }, { threshold: 0.1 });
        document.querySelectorAll('.reveal').forEach(function (el) { revObs.observe(el); });

        /* ── 3. Animated counters ── */
        var counted = false, statsEl = document.getElementById('stats');
        if (statsEl) {
            new IntersectionObserver(function (e) {
                if (e[0].isIntersecting && !counted) {
                    counted = true;
                    document.querySelectorAll('.counter').forEach(function (c) {
                        var target = parseInt(c.getAttribute('data-target')), n = 0, step = Math.ceil(target / 50);
                        var t = setInterval(function () { n += step; if (n >= target) { n = target; clearInterval(t); } c.textContent = n; }, 28);
                    });
                }
            }, { threshold: .4 }).observe(statsEl);
        }

        /* ── 4. Service Modal ── */
        var svcModal   = document.getElementById('svcModal');
        var slidesWrap = document.getElementById('svcmSlides');
        var dotsWrap   = document.getElementById('svcmDots');
        var titleEl    = document.getElementById('svcmTitle');
        var descEl     = document.getElementById('svcmDesc');
        var cur = 0, tot = 0, tmr = null;

        document.querySelectorAll('#services .service-card').forEach(function (card) {
            card.addEventListener('click', function () {
                openSvcModal(card.getAttribute('data-name'), card.getAttribute('data-desc'),
                             JSON.parse(card.getAttribute('data-imgs') || '[]'));
            });
            card.addEventListener('keypress', function (e) { if (e.key === 'Enter') card.click(); });
        });
        function openSvcModal(name, desc, images) {
            titleEl.textContent = name; descEl.textContent = desc;
            slidesWrap.innerHTML = ''; dotsWrap.innerHTML = '';
            cur = 0; tot = images.length;
            if (!tot) { slidesWrap.innerHTML = '<div class="svcm-no-img"><i class="fas fa-hard-hat"></i></div>'; }
            else {
                images.forEach(function (src, i) {
                    var s = document.createElement('div'); s.className = 'svcm-slide' + (i === 0 ? ' on' : '');
                    var img = document.createElement('img'); img.src = src; img.alt = name;
                    s.appendChild(img); slidesWrap.appendChild(s);
                    if (tot > 1) {
                        var d = document.createElement('button'); d.className = 'svcm-dot' + (i === 0 ? ' on' : '');
                        d.setAttribute('aria-label', 'Image ' + (i + 1));
                        (function (idx) { d.addEventListener('click', function () { svcGoTo(idx); }); }(i));
                        dotsWrap.appendChild(d);
                    }
                });
            }
            svcModal.classList.add('open'); document.body.style.overflow = 'hidden';
            clearInterval(tmr);
            if (tot > 1) tmr = setInterval(function () { svcGoTo((cur + 1) % tot); }, 3000);
        }
        function svcGoTo(idx) {
            var ss = slidesWrap.querySelectorAll('.svcm-slide'), ds = dotsWrap.querySelectorAll('.svcm-dot');
            if (!ss.length) return;
            ss[cur].classList.remove('on'); if (ds[cur]) ds[cur].classList.remove('on');
            cur = idx; ss[cur].classList.add('on'); if (ds[cur]) ds[cur].classList.add('on');
        }
        function closeSvcModal() { svcModal.classList.remove('open'); document.body.style.overflow = ''; clearInterval(tmr); }
        document.getElementById('svcmCloseBtn').addEventListener('click', closeSvcModal);
        document.getElementById('svcmQuoteBtn').addEventListener('click', closeSvcModal);
        svcModal.addEventListener('click', function (e) { if (e.target === svcModal) closeSvcModal(); });

        /* ── 5. Verification Modal ── */
        var verifyModal    = document.getElementById('verifyModal');
        var vmEmailDisplay = document.getElementById('vmEmailDisplay');
        var vmAlert        = document.getElementById('vmAlert');
        var vmVerifyBtn    = document.getElementById('vmVerifyBtn');
        var vmResendBtn    = document.getElementById('vmResendBtn');
        var vmResendTimer  = document.getElementById('vmResendTimer');
        var vmCountdown    = document.getElementById('vmCountdown');
        var vmTimerEl      = document.getElementById('vmTimer');
        var digits         = [0,1,2,3,4,5].map(function (i) { return document.getElementById('vd' + i); });
        var progDots       = [0,1,2,3,4,5].map(function (i) { return document.getElementById('vp' + i); });

        var savedFormData       = null;
        var countdownInterval   = null;
        var resendInterval      = null;
        var countdownSeconds    = 0;

        /* digit wiring */
        digits.forEach(function (inp, i) {
            inp.addEventListener('input', function () {
                inp.value = inp.value.replace(/[^0-9]/g, '').slice(-1);
                progDots[i].classList.toggle('filled', inp.value !== '');
                inp.classList.toggle('filled', inp.value !== '');
                inp.classList.remove('error');
                if (inp.value && i < 5) digits[i + 1].focus();
                updateVerifyBtn();
            });
            inp.addEventListener('keydown', function (e) {
                if (e.key === 'Backspace' && !inp.value && i > 0) {
                    digits[i - 1].value = '';
                    progDots[i - 1].classList.remove('filled');
                    digits[i - 1].classList.remove('filled');
                    digits[i - 1].focus();
                    updateVerifyBtn();
                }
            });
            inp.addEventListener('paste', function (e) {
                e.preventDefault();
                var pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').slice(0, 6);
                pasted.split('').forEach(function (ch, j) {
                    if (digits[j]) { digits[j].value = ch; progDots[j].classList.add('filled'); digits[j].classList.add('filled'); }
                });
                var focus = Math.min(pasted.length, 5);
                digits[focus].focus();
                updateVerifyBtn();
            });
        });

        function getCode()       { return digits.map(function (d) { return d.value; }).join(''); }
        function updateVerifyBtn() { vmVerifyBtn.disabled = (getCode().length !== 6 || countdownSeconds <= 0); }
        function clearDigits() {
            digits.forEach(function (d, i) { d.value = ''; d.classList.remove('filled', 'error'); progDots[i].classList.remove('filled'); });
            vmVerifyBtn.disabled = true;
        }
        function shakeDigits() {
            digits.forEach(function (d) { d.classList.add('error'); });
            setTimeout(function () { digits.forEach(function (d) { d.classList.remove('error'); }); }, 500);
        }

        function showVmAlert(msg, type) {
            var icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle';
            vmAlert.className = 'vm-alert show ' + type;
            vmAlert.innerHTML = '<i class="fas fa-' + icon + '"></i> ' + msg;
        }
        function hideVmAlert() { vmAlert.className = 'vm-alert'; vmAlert.innerHTML = ''; }

        function startCountdown(seconds) {
            clearInterval(countdownInterval);
            countdownSeconds = seconds;
            updateVerifyBtn();
            countdownInterval = setInterval(function () {
                countdownSeconds--;
                var m = Math.floor(countdownSeconds / 60), s = countdownSeconds % 60;
                vmCountdown.textContent = m + ':' + (s < 10 ? '0' : '') + s;
                if (countdownSeconds <= 0) {
                    clearInterval(countdownInterval);
                    vmTimerEl.classList.add('expired');
                    vmCountdown.textContent = 'Expired';
                    vmVerifyBtn.disabled = true;
                    showVmAlert('Code expired. Please request a new one.', 'error');
                }
            }, 1000);
        }

        function startResendCooldown() {
            vmResendBtn.disabled = true;
            var secs = 60;
            vmResendTimer.textContent = ' (' + secs + 's)';
            resendInterval = setInterval(function () {
                secs--;
                vmResendTimer.textContent = ' (' + secs + 's)';
                if (secs <= 0) { clearInterval(resendInterval); vmResendBtn.disabled = false; vmResendTimer.textContent = ''; }
            }, 1000);
        }

        function openVerifyModal(email) {
            vmEmailDisplay.textContent = email;
            clearDigits(); hideVmAlert();
            vmTimerEl.classList.remove('expired');
            vmCountdown.textContent = '10:00';
            startCountdown(600);
            startResendCooldown();
            verifyModal.classList.add('open');
            document.body.style.overflow = 'hidden';
            setTimeout(function () { digits[0].focus(); }, 300);
        }

        function closeVerifyModal() {
            verifyModal.classList.remove('open');
            document.body.style.overflow = '';
            clearInterval(countdownInterval);
            clearInterval(resendInterval);
        }

        document.getElementById('vmCloseBtn').addEventListener('click', closeVerifyModal);
        verifyModal.addEventListener('click', function (e) { if (e.target === verifyModal) closeVerifyModal(); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeVerifyModal(); closeSvcModal(); } });

        function sendOTP(email, onSuccess, onError) {
            var fd = new FormData(); fd.append('email', email);
            fetch('backend/send_verification.php', { method: 'POST', body: fd })
                .then(function (r) { return r.json(); })
                .then(function (data) { if (data.success) onSuccess(data); else onError(data.message); })
                .catch(function () { onError('Network error. Please try again.'); });
        }

        /* contact form submit → send OTP */
        var contactForm = document.getElementById('contactForm');
        var submitBtn   = document.getElementById('submitBtn');
        contactForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var name    = document.getElementById('cf_name').value.trim();
            var email   = document.getElementById('cf_email').value.trim();
            var message = document.getElementById('cf_message').value.trim();
            if (!name)    { document.getElementById('cf_name').focus(); return; }
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { document.getElementById('cf_email').focus(); return; }
            if (!message) { document.getElementById('cf_message').focus(); return; }

            savedFormData = new FormData(contactForm);
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right:.5rem;"></i>Sending code…';
            submitBtn.disabled = true;

            sendOTP(email,
                function () {
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane" style="margin-right:.5rem;"></i>Send Message';
                    submitBtn.disabled = false;
                    openVerifyModal(email);
                },
                function (errMsg) {
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane" style="margin-right:.5rem;"></i>Send Message';
                    submitBtn.disabled = false;
                    var el = document.getElementById('otpSendError');
                    if (!el) {
                        el = document.createElement('div'); el.id = 'otpSendError';
                        el.className = 'alert alert-danger'; el.style.marginBottom = '1rem';
                        contactForm.insertBefore(el, contactForm.firstChild);
                    }
                    el.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + errMsg;
                    setTimeout(function () { if (el.parentNode) el.parentNode.removeChild(el); }, 6000);
                }
            );
        });

        /* resend OTP */
        vmResendBtn.addEventListener('click', function () {
            var email = document.getElementById('cf_email').value.trim();
            hideVmAlert(); clearDigits();
            vmTimerEl.classList.remove('expired'); vmCountdown.textContent = '10:00';
            showVmAlert('Sending a new code…', 'info');
            sendOTP(email,
                function () {
                    showVmAlert('New code sent! Check your inbox.', 'success');
                    startCountdown(600); startResendCooldown();
                    setTimeout(hideVmAlert, 4000);
                    digits[0].focus();
                },
                function (msg) { showVmAlert(msg, 'error'); }
            );
        });

        /* ── KEY FIX: verify button — reads JSON, shows alert inline ── */
        vmVerifyBtn.addEventListener('click', function () {
            var code = getCode();
            if (code.length !== 6) return;

            vmVerifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right:.4rem;"></i>Verifying…';
            vmVerifyBtn.disabled = true;

            if (!savedFormData) {
                showVmAlert('Form data lost. Please close and re-submit the form.', 'error');
                vmVerifyBtn.innerHTML = '<i class="fas fa-check-circle" style="margin-right:.4rem;"></i>Verify & Send Message';
                return;
            }

            var fd = new FormData();
            for (var pair of savedFormData.entries()) { fd.append(pair[0], pair[1]); }
            fd.append('otp_code', code);

            // ── No redirect:'manual' — fetch the JSON response directly ──
            fetch('backend/submit_contact.php', { method: 'POST', body: fd })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.success) {
                        // ✅ Close modal, clear form, show inline success banner
                        closeVerifyModal();
                        contactForm.reset();

                        var banner = document.getElementById('contactSuccessBanner');
                        var msgEl  = document.getElementById('contactSuccessMsg');
                        msgEl.textContent = data.message;
                        banner.classList.add('show');

                        // Scroll the banner into view smoothly
                        banner.scrollIntoView({ behavior: 'smooth', block: 'center' });

                        // Auto-hide after 8 seconds
                        setTimeout(function () { banner.classList.remove('show'); }, 8000);
                    } else {
                        // ❌ Wrong code or server error — shake digits, show alert in modal
                        shakeDigits();
                        showVmAlert(data.message, 'error');
                        vmVerifyBtn.innerHTML = '<i class="fas fa-check-circle" style="margin-right:.4rem;"></i>Verify & Send Message';
                        vmVerifyBtn.disabled = false;
                    }
                })
                .catch(function () {
                    shakeDigits();
                    showVmAlert('Something went wrong. Please try again.', 'error');
                    vmVerifyBtn.innerHTML = '<i class="fas fa-check-circle" style="margin-right:.4rem;"></i>Verify & Send Message';
                    vmVerifyBtn.disabled = false;
                });
        });

    }());
    </script>
</body>
</html>