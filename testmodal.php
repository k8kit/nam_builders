<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modal Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: sans-serif; padding: 40px; }

        /* CARDS */
        .test-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            max-width: 700px;
        }
        .test-card {
            background: #eee;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid #ccc;
            position: relative;
            transition: box-shadow .2s;
        }
        .test-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,.2); }
        .test-card:hover .tc-overlay { opacity: 1; }
        .tc-img {
            width: 100%; height: 160px;
            background: #bbb;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; position: relative;
        }
        .tc-img img { width:100%; height:100%; object-fit:cover; }
        .tc-overlay {
            position: absolute; inset: 0;
            background: rgba(0,100,200,.3);
            display: flex; align-items: center; justify-content: center;
            opacity: 0; transition: opacity .2s;
            pointer-events: none;
        }
        .tc-overlay i { font-size: 2rem; color: #fff; }
        .tc-name { padding: 12px; font-weight: 600; text-align: center; background: #fff; }

        /* MODAL */
        #testModal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.7);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        #testModal.open { display: flex !important; }
        .tm-box {
            background: #fff;
            border-radius: 14px;
            max-width: 800px;
            width: 100%;
            display: flex;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,.4);
            max-height: 90vh;
        }
        .tm-left {
            flex: 0 0 55%;
            background: #111;
            position: relative;
            min-height: 380px;
        }
        .tm-slides { position: relative; width: 100%; height: 100%; min-height: 380px; overflow: hidden; }
        .tm-slide { position: absolute; inset: 0; opacity: 0; transition: opacity .5s; }
        .tm-slide.on { opacity: 1; }
        .tm-slide img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .tm-dots {
            position: absolute; bottom: 12px; left: 0; right: 0;
            display: flex; justify-content: center; gap: 6px; z-index: 10;
        }
        .tm-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: rgba(255,255,255,.4); border: none; padding: 0; cursor: pointer;
        }
        .tm-dot.on { background: #fff; }
        .tm-close {
            position: absolute; top: 10px; right: 10px;
            background: rgba(0,0,0,.5); color: #fff; border: none;
            border-radius: 50%; width: 34px; height: 34px;
            font-size: 1.2rem; line-height: 34px; text-align: center;
            cursor: pointer; z-index: 20;
        }
        .tm-right {
            flex: 1; padding: 2rem; display: flex; flex-direction: column; overflow-y: auto;
        }
        .tm-title { font-size: 1.5rem; font-weight: 700; margin: 0 0 .5rem; }
        .tm-bar { width: 40px; height: 4px; background: #1565C0; border-radius: 2px; margin-bottom: 1rem; }
        .tm-desc { color: #555; line-height: 1.7; flex: 1; }
        .tm-cta { padding-top: 1.5rem; }
        .tm-cta a {
            background: #1565C0; color: #fff; padding: .65rem 1.4rem;
            border-radius: 6px; font-weight: 600; text-decoration: none;
        }
    </style>
</head>
<body>

<h2>Service Cards Test</h2>
<p style="color:green; font-weight:600;">Click any card below — a modal should open.</p>

<div class="test-grid">
    <div class="test-card" onclick="openModal('General Construction','Complete construction solutions for residential, commercial, and industrial projects.',['https://picsum.photos/seed/a/600/400','https://picsum.photos/seed/b/600/400','https://picsum.photos/seed/c/600/400'])">
        <div class="tc-img">
            <img src="https://picsum.photos/seed/a/300/200" alt="">
            <div class="tc-overlay"><i class="fas fa-search-plus"></i></div>
        </div>
        <div class="tc-name">General Construction</div>
    </div>

    <div class="test-card" onclick="openModal('Electrical Systems','Expert electrical installation, maintenance, and repair services ensuring safety and efficiency.',['https://picsum.photos/seed/d/600/400'])">
        <div class="tc-img">
            <img src="https://picsum.photos/seed/d/300/200" alt="">
            <div class="tc-overlay"><i class="fas fa-search-plus"></i></div>
        </div>
        <div class="tc-name">Electrical Systems</div>
    </div>

    <div class="test-card" onclick="openModal('Steel Fabrication','Custom steel fabrication services for structural and architectural applications.',['https://picsum.photos/seed/e/600/400','https://picsum.photos/seed/f/600/400'])">
        <div class="tc-img">
            <img src="https://picsum.photos/seed/e/300/200" alt="">
            <div class="tc-overlay"><i class="fas fa-search-plus"></i></div>
        </div>
        <div class="tc-name">Steel Fabrication</div>
    </div>
</div>

<!-- MODAL -->
<div id="testModal" onclick="if(event.target===this)closeModal()">
    <div class="tm-box">
        <div class="tm-left">
            <button class="tm-close" onclick="closeModal()">&times;</button>
            <div class="tm-slides" id="tmSlides"></div>
            <div class="tm-dots"   id="tmDots"></div>
        </div>
        <div class="tm-right">
            <h2 class="tm-title" id="tmTitle"></h2>
            <div class="tm-bar"></div>
            <p  class="tm-desc"  id="tmDesc"></p>
            <div class="tm-cta"><a href="#contact" onclick="closeModal()">Get a Quote</a></div>
        </div>
    </div>
</div>

<script>
var modal  = document.getElementById('testModal');
var slides = document.getElementById('tmSlides');
var dots   = document.getElementById('tmDots');
var titleE = document.getElementById('tmTitle');
var descE  = document.getElementById('tmDesc');
var cur=0, tot=0, tmr=null;

function openModal(name, desc, images) {
    console.log('openModal called:', name); // debug
    titleE.textContent = name;
    descE.textContent  = desc;
    slides.innerHTML = '';
    dots.innerHTML   = '';
    cur = 0; tot = images.length;

    images.forEach(function(src, i) {
        var s = document.createElement('div');
        s.className = 'tm-slide' + (i===0?' on':'');
        var img = document.createElement('img');
        img.src = src;
        s.appendChild(img);
        slides.appendChild(s);

        if (tot > 1) {
            var d = document.createElement('button');
            d.className = 'tm-dot' + (i===0?' on':'');
            (function(idx){ d.onclick = function(){ go(idx); }; }(i));
            dots.appendChild(d);
        }
    });

    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
    clearInterval(tmr);
    if (tot > 1) tmr = setInterval(function(){ go((cur+1)%tot); }, 3000);
}

function go(idx) {
    var ss = slides.querySelectorAll('.tm-slide');
    var ds = dots.querySelectorAll('.tm-dot');
    ss[cur].classList.remove('on');
    if(ds[cur]) ds[cur].classList.remove('on');
    cur = idx;
    ss[cur].classList.add('on');
    if(ds[cur]) ds[cur].classList.add('on');
}

function closeModal() {
    modal.classList.remove('open');
    document.body.style.overflow = '';
    clearInterval(tmr);
}

document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeModal(); });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>