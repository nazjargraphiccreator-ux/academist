<?php 
/* Template Name: Rima 2026 Smart Courses */
nocache_headers();
get_header(); 

// ── EXACT PRICING DATA ─────────────────────────────────────────
$DATA = [
  'ro' => [
    'label' => 'Romanian', 'flag' => 'https://flagcdn.com/w80/ro.png', 'color' => '#DC2626',
    'levels' => [
      'A1-A2' => ['plat' => 700,  'ind' => [[280,252,'1 Month','2 sessions/mo'],[840,756,'3 Months','8 sessions/mo'],[1680,1512,'6 Months','16 sessions/mo']], 'corp' => [[240,220,'1 Month'],[740,670,'3 Months'],[2220,2000,'6 Months']]],
      'B1-B2' => ['plat' => 800,  'ind' => [[300,270,'1 Month','2 sessions/mo'],[900,810,'3 Months','8 sessions/mo'],[1800,1620,'6 Months','16 sessions/mo']], 'corp' => [[270,240,'1 Month'],[790,710,'3 Months'],[2380,2140,'6 Months']]],
      'C1-C2' => ['plat' => 1000, 'ind' => [[320,288,'1 Month','2 sessions/mo'],[960,864,'3 Months','8 sessions/mo'],[1920,1728,'6 Months','16 sessions/mo']], 'corp' => [[280,250,'1 Month'],[840,760,'3 Months'],[2540,2290,'6 Months']]],
    ],
  ],
  'en' => [
    'label' => 'English', 'flag' => 'https://flagcdn.com/w80/gb.png', 'color' => '#1d4ed8',
    'levels' => [
      'A1-A2' => ['plat' => 800,  'ind' => [[320,288,'1 Month','2 sessions/mo'],[960,864,'3 Months','8 sessions/mo'],[1920,1728,'6 Months','16 sessions/mo']], 'corp' => [[280,250,'1 Month'],[840,760,'3 Months'],[2540,2290,'6 Months']]],
      'B1-B2' => ['plat' => 1300, 'ind' => [[360,324,'1 Month','2 sessions/mo'],[1080,972,'3 Months','8 sessions/mo'],[2160,1944,'6 Months','16 sessions/mo']], 'corp' => [[320,290,'1 Month'],[960,860,'3 Months'],[2860,2570,'6 Months']]],
      'C1-C2' => ['plat' => 1700, 'ind' => [[480,432,'1 Month','2 sessions/mo'],[1440,1296,'3 Months','8 sessions/mo'],[2880,2592,'6 Months','16 sessions/mo']], 'corp' => [[420,380,'1 Month'],[1270,1140,'3 Months'],[3810,3430,'6 Months']]],
    ],
  ],
  'ja' => [
    'label' => 'Japanese', 'flag' => 'https://flagcdn.com/w80/jp.png', 'color' => '#be185d',
    'levels' => [
      'A1-A2' => ['plat' => 1150, 'ind' => [[400,360,'1 Month','2 sessions/mo'],[1200,1080,'3 Months','8 sessions/mo'],[2400,2160,'6 Months','16 sessions/mo']], 'corp' => [[360,320,'1 Month'],[1060,950,'3 Months'],[3180,2860,'6 Months']]],
      'B1-B2' => ['plat' => 1600, 'ind' => [[600,540,'1 Month','2 sessions/mo'],[1800,1620,'3 Months','8 sessions/mo'],[3600,3240,'6 Months','16 sessions/mo']], 'corp' => [[520,470,'1 Month'],[1590,1430,'3 Months'],[4770,4290,'6 Months']]],
      'C1-C2' => ['plat' => 2000, 'ind' => [[720,648,'1 Month','2 sessions/mo'],[2160,1944,'3 Months','8 sessions/mo'],[4800,4320,'6 Months','16 sessions/mo']], 'corp' => [[630,570,'1 Month'],[1900,1710,'3 Months'],[6340,5710,'6 Months']]],
    ],
  ],
];
$json_pricing  = json_encode($DATA, JSON_UNESCAPED_UNICODE);
?>

<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');
    
    .eltdf-title-holder { display: none !important; }

    :root {
        --m-primary: var(--rima-secondary, #B41527);      
        --m-grad-blue: linear-gradient(135deg, #0f172a, #1e293b);
        --m-grad-red: linear-gradient(135deg, var(--rima-secondary, #B41527), #8b101e);
        --m-grad-card: linear-gradient(135deg, #0f172a, #1e293b);
        --m-bg: #f8fafc;
        --m-border: #e2e8f0;
        --m-text: #0f172a;
        --m-gray: #64748b;
        
        --l-a: #10B981; /* Green */
        --l-b: #D4AF37; /* Gold */
        --l-c: #B41527; /* Crimson */
    }

    .rima-pricing-wrapper * { box-sizing: border-box; }

    .rima-pricing-wrapper {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: var(--m-bg);
        color: var(--m-text);
        width: 100%;
        position: relative;
        overflow: hidden;
        padding-top: 80px; /* Space for the custom header */
    }

    /* ── HERO ── */
    .rw-hero {
        background: linear-gradient(-45deg, #090e17, #0f172a, #1e293b, #0f172a);
        background-size: 400% 400%;
        animation: gradientHero 15s ease infinite;
        padding: 100px 20px 100px;
        text-align: center;
        color: #ffffff;
        position: relative;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        z-index: 1;
    }
    
    @keyframes gradientHero {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .rw-hero::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: radial-gradient(circle at 50% 50%, rgba(212, 175, 55, 0.15) 0%, transparent 60%);
        z-index: -1;
    }

    .rw-hero-title {
        font-size: clamp(3rem, 6vw, 5rem); font-weight: 900; letter-spacing: -1.5px;
        color: #ffffff; margin-bottom: 24px; line-height: 1.1;
        text-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
    .rw-hero-subtitle {
        font-size: 1.35rem; color: rgba(255,255,255,0.85); font-weight: 400; margin-bottom: 30px;
        max-width: 700px; margin-left: auto; margin-right: auto; line-height: 1.6;
    }
    .rw-hero-description {
        font-size: 1.1rem; color: rgba(255,255,255,0.6); font-weight: 400; margin-bottom: 50px;
        max-width: 800px; margin-left: auto; margin-right: auto; line-height: 1.6;
    }
    
    .rw-hero-features {
        display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;
    }
    .rw-hero-feat {
        display: flex; align-items: center; gap: 10px; font-size: 0.95rem; font-weight: 600; color: #ffffff;
        background: rgba(255,255,255,0.05); padding: 12px 24px; border-radius: 50px; backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.1);
        transition: transform 0.3s, background 0.3s, box-shadow 0.3s;
    }
    .rw-hero-feat:hover { background: rgba(255,255,255,0.1); transform: translateY(-5px); box-shadow: 0 10px 20px rgba(212, 175, 55, 0.2); }
    .rw-hero-feat svg { width: 20px; height: 20px; stroke-width: 2; color: var(--m-primary); }

    /* ── MAIN CONTENT ── */
    .rw-container { 
        position: relative; z-index: 1; max-width: 1300px; margin: 0 auto; padding: 80px 20px;
    }
    .rw-grid-layout {
        display: grid; grid-template-columns: 2fr 1fr; gap: 50px; align-items: start;
    }
    @media(max-width: 1024px) {
        .rw-grid-layout { grid-template-columns: 1fr; }
    }

    /* Left Side: Steps */
    .rw-selections { display: flex; flex-direction: column; gap: 60px; }

    .rw-step-heading { 
        font-size: 1.25rem; font-weight: 800; margin-bottom: 20px; color: var(--m-text); 
        display: flex; align-items: center; gap: 15px;
    }
    .rw-step-heading span.step-num { 
        background: var(--m-grad-blue); color: white; width: 40px; height: 40px; border-radius: 50%; 
        display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem; box-shadow: 0 4px 15px rgba(15, 23, 42, 0.25);
    }
    .rw-step-heading small { display: block; font-size: 0.9rem; font-weight: 500; color: var(--m-gray); margin-top: 4px; }

    /* ── CARDS ── */
    .rw-lang-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
    @media(max-width: 768px) { .rw-lang-grid { grid-template-columns: repeat(2, 1fr); } }
    
    .rw-card {
        background: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 25px 15px;
        cursor: pointer; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); text-align: center; 
        box-shadow: 0 10px 25px rgba(0,0,0,0.03); position: relative; overflow: hidden;
    }
    .rw-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); border-color: #cbd5e1; }
    .rw-card.active { 
        background: var(--m-grad-card); border-color: transparent; color: white; 
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.25); transform: translateY(-5px);
    }

    /* specific for lang */
    .rw-lang-card { display: flex; flex-direction: column; align-items: center; }
    .rw-lang-card.active .rw-lang-name { color: #ffffff; }
    .rw-lang-card.active .rw-lang-sub { color: rgba(255,255,255,0.8); }
    
    .rw-lang-flag { margin-bottom: 12px; width: 45px; height: 35px; object-fit: cover; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: 0.3s; }
    .rw-card:hover .rw-lang-flag { transform: scale(1.1); }
    .rw-lang-name { font-weight: 800; font-size: 1.1rem; color: var(--m-text); transition: 0.3s; }
    .rw-lang-sub { font-size: 0.8rem; color: var(--m-gray); font-weight: 600; margin-top: 2px; transition: 0.3s;}
    
    .rw-check { position: absolute; top: 12px; right: 12px; width: 24px; height: 24px; background: #ffffff; color: var(--m-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; opacity: 0; transform: scale(0.5); transition: 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
    .rw-card.active .rw-check { opacity: 1; transform: scale(1); }
    .rw-check svg { width: 14px; height: 14px; stroke-width: 3; }

    /* Add Language Card */
    .rw-lang-add { background: transparent; border: 2px dashed #cbd5e1; justify-content: center; box-shadow: none;}
    .rw-lang-add:hover { background: rgba(180, 21, 39, 0.05); border-color: var(--m-primary); box-shadow: none; transform: none;}
    .rw-lang-add .plus { font-size: 2rem; color: var(--m-gray); margin-bottom: 5px; transition: 0.3s; }
    .rw-lang-add:hover .plus { color: var(--m-primary); transform: rotate(90deg); }

    /* ── STEP 2: LEVELS ── */
    .rw-lvl-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
    @media(max-width: 768px) { .rw-lvl-grid { grid-template-columns: repeat(1, 1fr); } }
    
    .rw-lvl-card { text-align: left; border-left: 5px solid transparent; padding: 25px 20px;}
    .rw-lvl-card.active {
        border-color: var(--m-primary); border-left-color: var(--m-primary) !important;
        background: #ffffff; color: var(--m-text);
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.15);
    }
    
    .rw-lvl-card[data-val="A1-A2"] { border-left-color: var(--l-a); }
    .rw-lvl-card[data-val="B1-B2"] { border-left-color: var(--l-b); }
    .rw-lvl-card[data-val="C1-C2"] { border-left-color: var(--l-c); }

    .rw-lvl-header { display: flex; align-items: center; gap: 10px; margin-bottom: 15px; }
    .rw-lvl-bars { display: flex; align-items: flex-end; gap: 3px; height: 20px; }
    .rw-lvl-bars div { width: 5px; background: #cbd5e1; border-radius: 2px; transition: 0.3s;}
    
    .rw-lvl-card[data-val="A1-A2"]:hover .rw-lvl-bars div:nth-child(1), .rw-lvl-card[data-val="A1-A2"]:hover .rw-lvl-bars div:nth-child(2),
    .rw-lvl-card.active[data-val="A1-A2"] .rw-lvl-bars div:nth-child(1), .rw-lvl-card.active[data-val="A1-A2"] .rw-lvl-bars div:nth-child(2) { background: var(--l-a); height: 16px; }
    
    .rw-lvl-card.active[data-val="B1-B2"] .rw-lvl-bars div:nth-child(1), .rw-lvl-card.active[data-val="B1-B2"] .rw-lvl-bars div:nth-child(2), .rw-lvl-card.active[data-val="B1-B2"] .rw-lvl-bars div:nth-child(3) { background: var(--l-b); height: 20px; }
    
    .rw-lvl-card.active[data-val="C1-C2"] .rw-lvl-bars div { background: var(--l-c); height: 20px; }

    .rw-lvl-code { font-size: 1.5rem; font-weight: 900; color: var(--m-text); line-height: 1; }
    .rw-lvl-title { font-size: 0.95rem; font-weight: 800; color: var(--m-text); margin-bottom: 8px; }
    .rw-lvl-desc { font-size: 0.85rem; color: var(--m-gray); line-height: 1.5; font-weight: 500; }
    
    .rw-lvl-card .rw-check { background: var(--m-primary); color: white; }

    /* ── STEP 3: FORMAT ── */
    .rw-fmt-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
    @media(max-width: 768px) { .rw-fmt-grid { grid-template-columns: repeat(2, 1fr); } }
    
    .rw-fmt-card { padding: 30px 20px; }
    .rw-fmt-card.active { border-color: var(--m-primary); background: #ffffff; color: var(--m-text); box-shadow: 0 20px 45px rgba(180, 21, 39, 0.15); }
    
    .rw-fmt-icon { margin-bottom: 15px; color: var(--m-primary); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .rw-card:hover .rw-fmt-icon { transform: scale(1.2); }
    .rw-fmt-icon svg { width: 34px; height: 34px; stroke-width: 2; }
    .rw-fmt-title { font-size: 1.05rem; font-weight: 800; color: var(--m-text); margin-bottom: 8px; }
    .rw-fmt-desc { font-size: 0.85rem; color: var(--m-gray); line-height: 1.5; font-weight: 500; }
    
    .rw-fmt-card .rw-check { background: var(--m-primary); color: white; }

    /* Sub-options for Duration & Corporate */
    .rw-sub-options {
        background: #ffffff; border: 1px solid var(--m-border); border-radius: 20px; padding: 30px; margin-top: 20px; display: none;
        animation: rw-fadein 0.4s cubic-bezier(0.16, 1, 0.3, 1); box-shadow: 0 15px 30px rgba(0,0,0,0.03);
    }
    @keyframes rw-fadein { from { opacity: 0; transform: translateY(-15px); } to { opacity: 1; transform: translateY(0); } }

    .rw-duration-grid { display: flex; gap: 15px; margin-bottom: 20px;}
    .rw-dur-btn { flex:1; padding: 15px; background: #ffffff; border: 2px solid #cbd5e1; border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.3s; color: var(--m-gray); font-size:1rem;}
    .rw-dur-btn:hover { border-color: var(--m-primary); color: var(--m-primary); transform: translateY(-2px);}
    .rw-dur-btn.active { border-color: var(--m-primary); color: var(--m-primary); background: rgba(180, 21, 39, 0.05); box-shadow: 0 8px 20px rgba(180, 21, 39, 0.15);}

    /* ── RIGHT SIDE: SUMMARY ── */
    .rw-summary-panel {
        background: #ffffff; border-radius: 24px; overflow: hidden;
        box-shadow: 0 30px 60px rgba(15, 23, 42, 0.15); position: sticky; top: 100px; border: 1px solid #e2e8f0;
        transition: transform 0.3s ease;
    }
    .rw-summary-panel:hover { transform: translateY(-5px); box-shadow: 0 40px 70px rgba(15, 23, 42, 0.2); }
    
    .rw-sum-header {
        background: var(--m-grad-blue);
        padding: 35px 30px; color: white; position: relative; overflow: hidden;
    }
    .rw-sum-header::after {
        content: ''; position: absolute; top: -50%; right: -20%; width: 200px; height: 200px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%); border-radius: 50%;
    }
    
    .rw-sum-header-content { position: relative; z-index: 1; display: flex; align-items: center; gap: 18px; }
    .rw-sum-icon { width: 50px; height: 50px; background: rgba(255,255,255,0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); }
    .rw-sum-title { font-size: 1.5rem; font-weight: 900; line-height: 1.2; }
    .rw-sum-title small { display: block; font-size: 0.9rem; font-weight: 500; opacity: 0.9; margin-top: 4px;}

    .rw-sum-body { padding: 35px; }
    
    .rw-sum-list { display: flex; flex-direction: column; gap: 20px; border-bottom: 1px solid var(--m-border); padding-bottom: 25px; margin-bottom: 25px; }
    .rw-sum-row { display: flex; align-items: center; justify-content: space-between; font-size: 1rem; font-weight: 600; color: var(--m-gray); }
    .rw-sum-row .label { display: flex; align-items: center; gap: 10px; }
    .rw-sum-row .label svg { width: 20px; height: 20px; color: var(--m-primary); }
    .rw-sum-row .val { color: var(--m-text); font-weight: 800; display: flex; align-items: center; gap: 10px; }
    .rw-sum-row .val img { width: 28px; border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .rw-sum-row .val .lvl-bar { width: 18px; height: 18px; background: var(--l-b); border-radius: 4px; display:inline-block; }

    .rw-total-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; }
    .rw-total-label { display: flex; align-items: center; gap: 10px; font-weight: 800; color: var(--m-text); font-size: 1.1rem; }
    .rw-total-val { text-align: right; }
    .rw-total-price { font-size: 2.2rem; font-weight: 900; color: var(--m-text); line-height: 1; display: flex; align-items: center; gap: 5px;}
    .rw-total-price svg { width: 20px; color: #94a3b8; cursor: help; }
    .rw-total-approx { font-size: 0.9rem; color: #64748b; font-weight: 500; margin-top: 6px; }

    .rw-feats-list { list-style: none; padding: 0; margin-bottom: 35px; }
    .rw-feats-list li { display: flex; align-items: flex-start; gap: 12px; font-size: 0.95rem; font-weight: 600; color: #334155; margin-bottom: 15px; }
    .rw-feats-list li svg { width: 20px; color: var(--m-primary); flex-shrink: 0; margin-top: 2px;}

    .rw-action-btn {
        width: 100%; padding: 24px; border-radius: 18px; font-size: 1.25rem; font-weight: 900;
        background: var(--m-grad-red); color: white; border: none; cursor: pointer; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 15px 35px rgba(180, 21, 39, 0.3); display: flex; justify-content: center; align-items: center; gap: 12px;
        position: relative; overflow: hidden;
    }
    .rw-action-btn::after {
        content: ''; position: absolute; top: 0; left: -100%; width: 50%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transform: skewX(-20deg); transition: 0.5s;
    }
    .rw-action-btn:hover { transform: translateY(-5px); box-shadow: 0 25px 50px rgba(180, 21, 39, 0.4); }
    .rw-action-btn:hover::after { left: 150%; }

    .rw-trust-bar { display: flex; justify-content: space-between; margin-top: 30px; padding-top: 25px; border-top: 1px solid var(--m-border); }
    .rw-trust-item { display: flex; align-items: center; gap: 8px; font-size: 0.8rem; font-weight: 700; color: var(--m-gray); }
    .rw-trust-item svg { width: 16px; color: var(--m-primary); }

    /* Bottom Banner */
    .rw-bottom-banner {
        background: var(--m-grad-blue);
        padding: 40px 50px; color: white; display: flex; justify-content: space-between; align-items: center;
        margin-top: 80px; border-radius: 24px; max-width: 1300px; margin-left: auto; margin-right: auto;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.15); position: relative; overflow: hidden;
    }
    .rw-bb-content { position: relative; z-index: 1; display: flex; align-items: center; gap: 25px; width: 100%; }
    .rw-bb-icon { color: var(--m-primary); }
    .rw-bb-icon svg { width: 56px; height: 56px; stroke-width: 1.5; }
    .rw-bb-text h4 { font-size: 1.5rem; font-weight: 800; margin: 0 0 6px; color: #ffffff; }
    .rw-bb-text p { font-size: 1rem; font-weight: 500; color: rgba(255,255,255,0.8); margin: 0; }
    .rw-bb-btn { background: #ffffff; color: #0f172a; padding: 16px 32px; border-radius: 12px; font-size: 1rem; font-weight: 800; cursor: pointer; transition: 0.3s; border: none; margin-left: auto; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.1);}
    .rw-bb-btn:hover { background: var(--m-primary); color: #fff; transform: translateY(-3px); box-shadow: 0 15px 30px rgba(180, 21, 39, 0.3);}
    .rw-bb-btn svg { width: 20px; height: 20px; }
</style>

<div class="rima-pricing-wrapper">
    
    <!-- Hero Banner -->
    <div class="rw-hero" data-aos="fade-in" data-aos-duration="1000">
        <h1 class="rw-hero-title" data-aos="zoom-in" data-aos-delay="100">Intelligent Course Builder</h1>
        <div class="rw-hero-subtitle" data-aos="fade-up" data-aos-delay="200">Customize your language journey based on your specific needs, CEFR level, and learning style.</div>
        <div class="rw-hero-description" data-aos="fade-up" data-aos-delay="300">
            At Rima Academy, we don't believe in one-size-fits-all. Use our smart course builder to select your desired language, map your proficiency level, and choose a format that fits your busy schedule. Whether you learn best on your own or with a dedicated tutor, we have the perfect curriculum for you.
        </div>
        
        <div class="rw-hero-features" data-aos="fade-up" data-aos-delay="400">
            <div class="rw-hero-feat"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg> Multiple Languages</div>
            <div class="rw-hero-feat"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"></path></svg> Verified CEFR Levels</div>
            <div class="rw-hero-feat"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg> Flexible Learning</div>
            <div class="rw-hero-feat"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> Personalized Experience</div>
        </div>
    </div>

    <div class="rw-container">
        <div class="rw-grid-layout">
            
            <!-- Left Side: Wizard -->
            <div class="rw-selections">
                
                <!-- STEP 1: Language -->
                <div data-aos="fade-up">
                    <div class="rw-step-heading"><span class="step-num">1</span> <div>Choose Language<small>Select the language you want to learn from our premium courses.</small></div></div>
                    <div class="rw-lang-grid">
                        <div class="rw-card rw-lang-card active" data-type="lang" data-val="en">
                            <div class="rw-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                            <img src="https://flagcdn.com/w80/gb.png" class="rw-lang-flag" alt="EN">
                            <div class="rw-lang-name">English</div>
                            <div class="rw-lang-sub">General & Business</div>
                        </div>
                        <div class="rw-card rw-lang-card" data-type="lang" data-val="ro">
                            <div class="rw-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                            <img src="https://flagcdn.com/w80/ro.png" class="rw-lang-flag" alt="RO">
                            <div class="rw-lang-name">Romanian</div>
                            <div class="rw-lang-sub">For Expats</div>
                        </div>
                        <div class="rw-card rw-lang-card" data-type="lang" data-val="ja">
                            <div class="rw-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                            <img src="https://flagcdn.com/w80/jp.png" class="rw-lang-flag" alt="JA">
                            <div class="rw-lang-name">Japanese</div>
                            <div class="rw-lang-sub">JLPT N5-N1</div>
                        </div>
                        <div class="rw-card rw-lang-add" style="cursor:default;">
                            <div class="plus">+</div>
                            <div class="rw-lang-name" style="font-size:1rem;">Add Language</div>
                            <div class="rw-lang-sub">More languages soon</div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Level -->
                <div data-aos="fade-up" data-aos-delay="100">
                    <div class="rw-step-heading"><span class="step-num">2</span> <div>Choose Level<small>Select your current CEFR level to match the curriculum.</small></div></div>
                    <div class="rw-lvl-grid">
                        <!-- A1-A2 -->
                        <div class="rw-card rw-lvl-card" data-type="lvl" data-val="A1-A2">
                            <div class="rw-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                            <div class="rw-lvl-header">
                                <div class="rw-lvl-bars"><div></div><div></div><div></div><div></div></div>
                                <div class="rw-lvl-code">A1-A2</div>
                            </div>
                            <div class="rw-lvl-title">Beginner / Elem.</div>
                            <div class="rw-lvl-desc">Focus on basic communication, everyday situations, and building a strong foundation.</div>
                        </div>
                        <!-- B1-B2 -->
                        <div class="rw-card rw-lvl-card active" data-type="lvl" data-val="B1-B2">
                            <div class="rw-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                            <div class="rw-lvl-header">
                                <div class="rw-lvl-bars"><div></div><div></div><div></div><div></div></div>
                                <div class="rw-lvl-code">B1-B2</div>
                            </div>
                            <div class="rw-lvl-title">Intermediate</div>
                            <div class="rw-lvl-desc">Express yourself with confidence, handle complex topics and workplace scenarios.</div>
                        </div>
                        <!-- C1-C2 -->
                        <div class="rw-card rw-lvl-card" data-type="lvl" data-val="C1-C2">
                            <div class="rw-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                            <div class="rw-lvl-header">
                                <div class="rw-lvl-bars"><div></div><div></div><div></div><div></div></div>
                                <div class="rw-lvl-code">C1-C2</div>
                            </div>
                            <div class="rw-lvl-title">Advanced</div>
                            <div class="rw-lvl-desc">Mastery and near-native fluency for academic and advanced professional contexts.</div>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: Format -->
                <div data-aos="fade-up" data-aos-delay="200">
                    <div class="rw-step-heading"><span class="step-num">3</span> <div>Learning Format<small>Choose how you want to learn based on your schedule and budget.</small></div></div>
                    <div class="rw-fmt-grid">
                        <div class="rw-card rw-fmt-card active" data-type="fmt" data-val="platform">
                            <div class="rw-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                            <div class="rw-fmt-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg></div>
                            <div class="rw-fmt-title">Platform Only</div>
                            <div class="rw-fmt-desc">Self-paced learning on our advanced LMS. Learn at your own speed anytime, anywhere.</div>
                        </div>
                        <div class="rw-card rw-fmt-card" data-type="fmt" data-val="individual">
                            <div class="rw-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                            <div class="rw-fmt-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></div>
                            <div class="rw-fmt-title">Individual</div>
                            <div class="rw-fmt-desc">1-on-1 Zoom sessions with a dedicated expert tutor for maximum personalization.</div>
                        </div>
                        <div class="rw-card rw-fmt-card" data-type="fmt" data-val="group">
                            <div class="rw-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                            <div class="rw-fmt-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg></div>
                            <div class="rw-fmt-title">Group</div>
                            <div class="rw-fmt-desc">Small interactive groups (3-8 students) for collaborative learning and cost efficiency.</div>
                        </div>
                        <div class="rw-card rw-fmt-card" data-type="fmt" data-val="corporate">
                            <div class="rw-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                            <div class="rw-fmt-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><path d="M9 22v-4h6v4"></path><path d="M8 6h.01"></path><path d="M16 6h.01"></path><path d="M12 6h.01"></path><path d="M12 10h.01"></path><path d="M12 14h.01"></path><path d="M16 10h.01"></path><path d="M16 14h.01"></path><path d="M8 10h.01"></path><path d="M8 14h.01"></path></svg></div>
                            <div class="rw-fmt-title">Corporate</div>
                            <div class="rw-fmt-desc">B2B custom plans tailored for teams and employees. Upload your list for an instant quote.</div>
                        </div>
                    </div>

                    <!-- Dynamic Sub-Options Container -->
                    <div id="sub-options" class="rw-sub-options">
                        <!-- Duration (For Individual & Corporate) -->
                        <div id="opt-duration" style="display:none;">
                            <div style="font-size:0.95rem; font-weight:800; color:var(--m-gray); margin-bottom:12px; text-transform:uppercase;">Select Commitment Duration</div>
                            <div class="rw-duration-grid">
                                <button class="rw-dur-btn active" data-dur="1">1 Month</button>
                                <button class="rw-dur-btn" data-dur="3">3 Months (Save 10%)</button>
                                <button class="rw-dur-btn" data-dur="6">6 Months (Save 20%)</button>
                            </div>
                        </div>

                        <!-- B2B Excel Upload (Only for Corporate) -->
                        <div id="opt-corporate-upload" style="display:none; margin-top:20px; padding:25px; background:var(--m-bg); border:2px dashed var(--m-primary); border-radius:12px; text-align:center;">
                            <div style="font-size:1.1rem; font-weight:900; color:var(--m-primary); margin-bottom:10px; text-transform:uppercase;">DATE COMPANIE ȘI ANGAJAȚI</div>
                            <div style="font-size:0.9rem; color:var(--m-gray); margin-bottom:15px;">Sistemul va calcula automat oferta corporativă. Vă rugăm să introduceți CUI-ul firmei și să încărcați lista de angajați.</div>
                            
                            <input type="text" id="rima_b2b_course_cui" placeholder="Introdu CUI (ex: RO123456)" style="width:100%; padding:15px; border-radius:12px; border:2px solid #cbd5e1; margin-bottom:15px; font-size:1rem; text-align:center;" />

                            <input type="file" id="rima_b2b_course_file" accept=".xlsx,.xls,.csv" style="display:none;">
                            <button class="rw-dur-btn" id="rima_b2b_trigger" style="width:100%; border-color:var(--m-primary); color:var(--m-primary); background:#ffffff;">Alege Fișier Excel / CSV (Opțional)</button>
                            <div id="rima_b2b_status" style="font-size:0.95rem; font-weight:700; color:#10B981; margin-top:10px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Summary Sticky -->
            <div data-aos="fade-left" data-aos-delay="300">
                <div class="rw-summary-panel">
                    <div class="rw-sum-header">
                        <div class="rw-sum-header-content">
                            <div class="rw-sum-icon"><svg viewBox="0 0 24 24" width="28" fill="none" stroke="white" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg></div>
                            <div class="rw-sum-title">Your Summary<small>Check your selected options</small></div>
                        </div>
                    </div>
                    
                    <div class="rw-sum-body">
                        <div class="rw-sum-list">
                            <div class="rw-sum-row">
                                <div class="label"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> Language</div>
                                <div class="val" id="sum-lang-name">English <img id="sum-lang-flag" src="https://flagcdn.com/w80/gb.png" alt=""></div>
                            </div>
                            <div class="rw-sum-row">
                                <div class="label"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> CEFR Level</div>
                                <div class="val" id="sum-lvl-name">B1-B2 – Intermediate <div class="lvl-bar"></div></div>
                            </div>
                            <div class="rw-sum-row">
                                <div class="label"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line></svg> Format</div>
                                <div class="val" id="sum-fmt-name">Platform Only</div>
                            </div>
                        </div>

                        <div class="rw-total-row">
                            <div class="rw-total-label"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width:24px; color:var(--m-primary)"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg> Total Investment</div>
                            <div class="rw-total-val">
                                <div class="rw-total-price"><span id="price-val">1300</span> RON <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg></div>
                                <div class="rw-total-approx" id="price-eur">(approx. 260 €)</div>
                            </div>
                        </div>

                        <ul class="rw-feats-list" id="sum-feats">
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> Full access to Rima LMS</li>
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> Interactive exercises</li>
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> Self-paced progress tracking</li>
                        </ul>

                        <button class="rw-action-btn">ENROLL NOW <svg viewBox="0 0 24 24" width="24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></button>

                        <div class="rw-trust-bar">
                            <div class="rw-trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg> Secure SSL payment</div>
                            <div class="rw-trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg> Instant platform access</div>
                            <div class="rw-trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg> 24/7 Priority support</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Banner -->
    <div class="rw-bottom-banner" data-aos="zoom-in" data-aos-delay="200">
        <div class="rw-bb-content">
            <div class="rw-bb-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 18h6"></path><path d="M10 22h4"></path><path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A6 6 0 1 0 7.5 11.5c.76.76 1.23 1.52 1.41 2.5"></path></svg></div>
            <div class="rw-bb-text">
                <h4>Not sure where to start?</h4>
                <p>Take our free placement test and get a personalized recommendation instantly.</p>
            </div>
            <button class="rw-bb-btn">Take Placement Test <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></button>
        </div>
    </div>

</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
// Initialize Animations
AOS.init({
    once: true,
    offset: 50,
    duration: 800,
    easing: 'ease-out-cubic'
});

const pricingData = <?php echo $json_pricing; ?>;
let state = { lang: 'en', lvl: 'B1-B2', fmt: 'platform', dur: 1, empCount: 1 };

function updateUI() {
    // 1. Language
    document.querySelectorAll('.rw-lang-card[data-type="lang"]').forEach(el => {
        el.classList.toggle('active', el.dataset.val === state.lang);
    });
    const lData = pricingData[state.lang];
    document.getElementById('sum-lang-name').innerHTML = lData.label + ' <img src="'+lData.flag+'" alt="" id="sum-lang-flag">';

    // 2. Level
    document.querySelectorAll('.rw-lvl-card').forEach(el => {
        el.classList.toggle('active', el.dataset.val === state.lvl);
    });
    const lvlNameMap = { 'A1-A2': 'Beginner/Elem.', 'B1-B2': 'Intermediate', 'C1-C2': 'Advanced' };
    const lvlColors = { 'A1-A2': 'var(--l-a)', 'B1-B2': 'var(--l-b)', 'C1-C2': 'var(--l-c)' };
    document.getElementById('sum-lvl-name').innerHTML = state.lvl + ' – ' + lvlNameMap[state.lvl] + ' <div class="lvl-bar" style="background:'+lvlColors[state.lvl]+'"></div>';

    // 3. Format
    document.querySelectorAll('.rw-fmt-card').forEach(el => {
        el.classList.toggle('active', el.dataset.val === state.fmt);
    });
    
    // Sub-options
    const sub = document.getElementById('sub-options');
    const optDur = document.getElementById('opt-duration');
    const optCorpUpload = document.getElementById('opt-corporate-upload');
    
    if (state.fmt === 'individual' || state.fmt === 'corporate' || state.fmt === 'group') {
        sub.style.display = 'block';
        optDur.style.display = 'block';
    } else {
        sub.style.display = 'none';
        optDur.style.display = 'none';
    }

    if (state.fmt === 'corporate') {
        optCorpUpload.style.display = 'block';
    } else {
        optCorpUpload.style.display = 'none';
        state.empCount = 1; // reset if not corporate
    }
    
    document.querySelectorAll('.rw-dur-btn').forEach(el => {
        el.classList.toggle('active', parseInt(el.dataset.dur) === state.dur);
    });

    // Update Price
    let price = 0;
    let formatLabel = '';
    const lvlData = lData.levels[state.lvl];
    let feats = [];

    if (state.fmt === 'platform') {
        price = lvlData.plat;
        formatLabel = 'Platform Only';
        feats = ['Full access to Rima LMS', 'Interactive exercises', 'Self-paced progress tracking', 'Instant grading', 'Mobile friendly'];
    } else if (state.fmt === 'individual') {
        let idx = (state.dur === 1) ? 0 : (state.dur === 3 ? 1 : 2);
        price = lvlData.ind[idx][0];
        formatLabel = 'Individual – ' + state.dur + ' Month(s)';
        feats = [lvlData.ind[idx][3], 'Dedicated 1-on-1 expert tutor', 'Live immersive Zoom sessions', 'Tailored lesson plans', 'Full LMS Access included'];
    } else if (state.fmt === 'group') {
        // Group is roughly 70% of Individual
        let idx = (state.dur === 1) ? 0 : (state.dur === 3 ? 1 : 2);
        price = Math.round(lvlData.ind[idx][0] * 0.7);
        formatLabel = 'Group – ' + state.dur + ' Month(s)';
        feats = [lvlData.ind[idx][3], 'Small group (3-8 students)', 'Live immersive Zoom sessions', 'Collaborative learning', 'Full LMS Access included'];
    } else if (state.fmt === 'corporate') {
        let idx = (state.dur === 1) ? 0 : (state.dur === 3 ? 1 : 2);
        price = lvlData.corp[idx][0];
        formatLabel = 'Corporate – ' + state.dur + ' Month(s)';
        feats = ['B2B Custom Corporate Plan', 'Team progress & ROI tracking', 'Dedicated account manager', 'Monthly performance reports', 'Bulk licensing discounts'];
    }

    // Multiply by employees if corporate
    if (state.fmt === 'corporate' && state.empCount > 1) {
        price = price * state.empCount;
        formatLabel += ' (' + state.empCount + ' angajați)';
    }

    document.getElementById('sum-fmt-name').innerText = formatLabel;
    
    // Animate Price change
    const priceEl = document.getElementById('price-val');
    const oldPrice = parseInt(priceEl.innerText) || 0;
    animateValue(priceEl, oldPrice, price, 400);
    
    document.getElementById('price-eur').innerText = '(approx. ' + Math.round(price / 5) + ' €)';

    // Update Features with animation
    const ul = document.getElementById('sum-feats');
    ul.innerHTML = '';
    feats.forEach((f, index) => {
        ul.innerHTML += '<li style="animation: rw-fadein 0.4s ease forwards; animation-delay: '+ (index * 0.1) +'s; opacity: 0;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> ' + f + '</li>';
    });
}

function animateValue(obj, start, end, duration) {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        obj.innerHTML = Math.floor(progress * (end - start) + start);
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
}

// Event Listeners
document.querySelectorAll('.rw-lang-card[data-type="lang"]').forEach(btn => {
    btn.addEventListener('click', () => { state.lang = btn.dataset.val; updateUI(); });
});
document.querySelectorAll('.rw-lvl-card').forEach(btn => {
    btn.addEventListener('click', () => { state.lvl = btn.dataset.val; updateUI(); });
});
document.querySelectorAll('.rw-fmt-card').forEach(btn => {
    btn.addEventListener('click', () => { state.fmt = btn.dataset.val; updateUI(); });
});
document.querySelectorAll('.rw-dur-btn').forEach(btn => {
    if(!btn.id) { // skip the upload button
        btn.addEventListener('click', () => { state.dur = parseInt(btn.dataset.dur); updateUI(); });
    }
});

// Excel Upload Handler
const triggerBtn = document.getElementById('rima_b2b_trigger');
const fileInput = document.getElementById('rima_b2b_course_file');
const statusDiv = document.getElementById('rima_b2b_status');

if (triggerBtn && fileInput) {
    triggerBtn.addEventListener('click', (e) => {
        e.preventDefault();
        fileInput.click();
    });

    fileInput.addEventListener('change', function() {
        if (!this.files.length) return;
        
        let file = this.files[0];
        triggerBtn.innerText = 'Se încarcă...';
        
        let fd = new FormData();
        fd.append('action', 'rima_upload_emp_xlsx');
        fd.append('emp_file', file);
        
        fetch('/wp-admin/admin-ajax.php', {
            method: 'POST',
            body: fd
        })
        .then(res => res.json())
        .then(res => {
            if(res.success && res.data.emp_count) {
                state.empCount = parseInt(res.data.emp_count);
                triggerBtn.innerText = 'Fișier Încărcat ✓';
                statusDiv.innerText = state.empCount + ' angajați detectați.';
                updateUI();
            } else {
                triggerBtn.innerText = 'Alege Fișier Excel / CSV';
                alert(res.data.message || 'Eroare la citirea numărului de persoane.');
            }
        })
        .catch(err => {
            triggerBtn.innerText = 'Alege Fișier Excel / CSV';
            alert('Eroare de conexiune.');
        });
    });
}

// Enroll Button Handler
document.querySelector('.rw-action-btn').addEventListener('click', function(e) {
    e.preventDefault();
    const btn = this;
    const oldHtml = btn.innerHTML;
    btn.innerHTML = 'PROCESSING...';
    btn.style.opacity = '0.7';
    btn.style.pointerEvents = 'none';

    let formData = new FormData();
    formData.append('action', 'rima_add_dynamic_product');
    formData.append('lang', state.lang);
    formData.append('level', state.lvl);
    formData.append('tier', state.fmt + (state.fmt !== 'platform' ? ' - ' + state.dur + ' Months' : ''));
    
    // get CUI if corporate
    if (state.fmt === 'corporate') {
        let cuiVal = document.getElementById('rima_b2b_course_cui').value;
        if (cuiVal) formData.append('cui', cuiVal);
    }
    
    // get price from UI
    let priceStr = document.getElementById('price-val').innerText;
    formData.append('price', priceStr);

    fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if (res.success && res.data && res.data.cart_url) {
            // Trigger floating cart animation (optional, it redirects anyway)
            if(window.jQuery) {
                jQuery(document.body).trigger('added_to_cart', [res.data.fragments, res.data.cart_hash, null]);
            }
            window.location.href = res.data.cart_url;
        } else {
            alert('Error adding to cart. Please try again.');
            btn.innerHTML = oldHtml;
            btn.style.opacity = '1';
            btn.style.pointerEvents = 'auto';
        }
    })
    .catch(err => {
        alert('Connection error. Please try again.');
        btn.innerHTML = oldHtml;
        btn.style.opacity = '1';
        btn.style.pointerEvents = 'auto';
    });
});

// Force remove title stripe (fallback)
document.addEventListener('DOMContentLoaded', function() {
    var titleStripe = document.querySelector('.eltdf-title-holder');
    if (titleStripe) titleStripe.remove();
});

// Init
updateUI();
</script>

<?php
do_action('academist_elated_action_after_main_content');
get_footer();
?>
