<?php 
/* Template Name: Rima 2026 Smart Courses */
nocache_headers();
get_header(); 

// â”€â”€ EXACT PRICING DATA â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
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

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');
    
    .eltdf-title-holder { display: none !important; }

    :root {
        --m-primary: var(--rima-primary);      
        --m-grad-blue: var(--rima-gradient-primary);
        --m-grad-red: var(--rima-gradient-accent);
        --m-grad-card: var(--rima-gradient-dark);
        --m-bg: var(--rima-surface-alt);
        --m-border: var(--rima-border);
        --m-text: var(--rima-text);
        --m-gray: var(--rima-text-muted);
        
        --l-a: #10B981; /* Green */
        --l-b: #3B82F6; /* Blue */
        --l-c: #8B5CF6; /* Purple */
    }

    .rima-pricing-wrapper * { box-sizing: border-box; }

    /* The Academist header is absolute and has white text.
       We need a dark background at the top of the page so the menu is visible. */
    .rima-pricing-wrapper {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: var(--m-bg);
        color: var(--m-text);
        width: 100%;
        position: relative;
        overflow: hidden;
        padding-top: 110px; /* Space for the Academist header */
    }
    
    .rima-pricing-wrapper::before {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 100%; height: 110px;
        background: #0f172a; /* Dark background so the white menu is visible */
        z-index: 0;
    }

    /* â”€â”€ HERO â”€â”€ */
    .rw-hero {
        background: #0f172a; /* Match Academist dark header */
        background-image: radial-gradient(circle at 80% -20%, rgba(18, 48, 142, 0.6) 0%, transparent 50%),
                          radial-gradient(circle at 20% 120%, rgba(230, 34, 67, 0.4) 0%, transparent 50%);
        padding: 80px 20px 80px;
        text-align: center;
        color: #ffffff;
        position: relative;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    .rw-hero-title {
        font-size: clamp(2.8rem, 5vw, 4.5rem); font-weight: 900; letter-spacing: -1.5px;
        color: #ffffff; margin-bottom: 16px; line-height: 1.1;
        text-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
    .rw-hero-subtitle {
        font-size: 1.25rem; color: rgba(255,255,255,0.75); font-weight: 400; margin-bottom: 50px;
        max-width: 650px; margin-left: auto; margin-right: auto;
    }
    
    .rw-hero-features {
        display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;
    }
    .rw-hero-feat {
        display: flex; align-items: center; gap: 10px; font-size: 0.95rem; font-weight: 600; color: #ffffff;
        background: rgba(255,255,255,0.05); padding: 10px 20px; border-radius: 50px; backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.1);
        transition: transform 0.3s, background 0.3s;
    }
    .rw-hero-feat:hover { background: rgba(255,255,255,0.1); transform: translateY(-2px); }
    .rw-hero-feat svg { width: 20px; height: 20px; stroke-width: 2; color: #fbbf24; }

    /* â”€â”€ MAIN CONTENT â”€â”€ */
    .rw-container { 
        position: relative; z-index: 1; max-width: 1300px; margin: 0 auto; padding: 60px 20px;
    }
    .rw-grid-layout {
        display: grid; grid-template-columns: 2fr 1fr; gap: 40px; align-items: start;
    }
    @media(max-width: 1024px) {
        .rw-grid-layout { grid-template-columns: 1fr; }
    }

    /* Left Side: Steps */
    .rw-selections { display: flex; flex-direction: column; gap: 50px; }

    .rw-step-heading { 
        font-size: 1.1rem; font-weight: 800; margin-bottom: 15px; color: var(--m-primary); 
        display: flex; align-items: center; gap: 15px;
    }
    .rw-step-heading span.step-num { 
        background: var(--m-grad-blue); color: white; width: 34px; height: 34px; border-radius: 50%; 
        display: inline-flex; align-items: center; justify-content: center; font-size: 1rem; box-shadow: 0 4px 10px rgba(18,48,142,0.25);
    }
    .rw-step-heading small { display: block; font-size: 0.8rem; font-weight: 500; color: var(--m-gray); margin-top: 2px; }

    /* â”€â”€ STEP 1: LANGUAGES â”€â”€ */
    .rw-lang-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; }
    @media(max-width: 768px) { .rw-lang-grid { grid-template-columns: repeat(2, 1fr); } }
    .rw-lang-card {
        background: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 25px 15px;
        cursor: pointer; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); text-align: center; 
        box-shadow: 0 10px 25px rgba(0,0,0,0.03); position: relative; overflow: hidden; display: flex; flex-direction: column; align-items: center;
    }
    .rw-lang-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); border-color: #cbd5e1; }
    .rw-lang-card.active { 
        background: var(--m-grad-card); border-color: transparent; color: white; 
        box-shadow: 0 20px 45px rgba(18,48,142,0.35); 
    }
    .rw-lang-card.active .rw-lang-name { color: #ffffff; }
    .rw-lang-card.active .rw-lang-sub { color: rgba(255,255,255,0.8); }
    
    .rw-lang-flag { margin-bottom: 12px; width: 40px; height: 30px; object-fit: cover; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .rw-lang-name { font-weight: 800; font-size: 1.05rem; color: var(--m-text); transition: 0.3s; }
    .rw-lang-sub { font-size: 0.75rem; color: var(--m-gray); font-weight: 600; margin-top: 2px; transition: 0.3s;}
    
    .rw-check { position: absolute; top: 12px; right: 12px; width: 22px; height: 22px; background: #ffffff; color: var(--m-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; opacity: 0; transform: scale(0.5); transition: 0.3s; }
    .rw-lang-card.active .rw-check { opacity: 1; transform: scale(1); }
    .rw-check svg { width: 14px; height: 14px; stroke-width: 3; }

    /* Add Language Card */
    .rw-lang-add { background: transparent; border: 2px dashed #cbd5e1; justify-content: center; }
    .rw-lang-add:hover { background: rgba(255,255,255,0.5); border-color: var(--m-primary); }
    .rw-lang-add .plus { font-size: 1.5rem; color: var(--m-gray); margin-bottom: 5px; }

    /* â”€â”€ STEP 2: LEVELS (3 CARDS - A1-A2, B1-B2, C1-C2) â”€â”€ */
    .rw-lvl-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
    @media(max-width: 768px) { .rw-lvl-grid { grid-template-columns: repeat(1, 1fr); gap: 15px; } }
    
    .rw-lvl-card {
        background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px 15px;
        cursor: pointer; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); text-align: left; box-shadow: 0 10px 25px rgba(0,0,0,0.03);
        position: relative; overflow: hidden; display: flex; flex-direction: column; justify-content: flex-start;
        border-left: 5px solid transparent;
    }
    .rw-lvl-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }
    .rw-lvl-card.active {
        border-color: var(--m-primary); border-left-color: var(--m-primary) !important;
        box-shadow: 0 20px 45px rgba(18,48,142,0.15); background: #F8FAFF;
    }
    
    .rw-lvl-card[data-val="A1-A2"] { border-left-color: var(--l-a); }
    .rw-lvl-card[data-val="B1-B2"] { border-left-color: var(--l-b); }
    .rw-lvl-card[data-val="C1-C2"] { border-left-color: var(--l-c); }

    .rw-lvl-header { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; }
    .rw-lvl-bars { display: flex; align-items: flex-end; gap: 2px; height: 16px; }
    .rw-lvl-bars div { width: 4px; background: #cbd5e1; border-radius: 2px; }
    
    .rw-lvl-card[data-val="A1-A2"] .rw-lvl-bars div:nth-child(1), .rw-lvl-card[data-val="A1-A2"] .rw-lvl-bars div:nth-child(2) { background: var(--l-a); height: 12px; }
    .rw-lvl-card[data-val="B1-B2"] .rw-lvl-bars div:nth-child(1), .rw-lvl-card[data-val="B1-B2"] .rw-lvl-bars div:nth-child(2), .rw-lvl-card[data-val="B1-B2"] .rw-lvl-bars div:nth-child(3) { background: var(--l-b); height: 16px; }
    .rw-lvl-card[data-val="C1-C2"] .rw-lvl-bars div { background: var(--l-c); height: 16px; }

    .rw-lvl-code { font-size: 1.3rem; font-weight: 900; color: var(--m-text); line-height: 1; }
    .rw-lvl-title { font-size: 0.85rem; font-weight: 800; color: var(--m-text); margin-bottom: 6px; }
    .rw-lvl-desc { font-size: 0.75rem; color: var(--m-gray); line-height: 1.4; font-weight: 500; }
    
    .rw-lvl-card .rw-check { background: var(--m-primary); color: white; top: 10px; right: 10px; width: 18px; height: 18px; }
    .rw-lvl-card .rw-check svg { width: 10px; height: 10px; }

    /* â”€â”€ STEP 3: FORMAT â”€â”€ */
    .rw-fmt-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; }
    @media(max-width: 768px) { .rw-fmt-grid { grid-template-columns: repeat(2, 1fr); } }
    .rw-fmt-card {
        background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px 15px;
        cursor: pointer; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); box-shadow: 0 10px 25px rgba(0,0,0,0.03); position: relative;
    }
    .rw-fmt-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }
    .rw-fmt-card.active { border-color: var(--m-primary); background: #F8FAFF; box-shadow: 0 20px 45px rgba(18,48,142,0.15); }
    
    .rw-fmt-icon { margin-bottom: 12px; color: var(--m-primary); }
    .rw-fmt-icon svg { width: 28px; height: 28px; stroke-width: 2; }
    .rw-fmt-title { font-size: 0.95rem; font-weight: 800; color: var(--m-text); margin-bottom: 6px; }
    .rw-fmt-desc { font-size: 0.75rem; color: var(--m-gray); line-height: 1.4; font-weight: 500; }
    
    .rw-fmt-card .rw-check { background: var(--m-primary); color: white; top: 12px; right: 12px; }

    /* Sub-options for Duration & Corporate */
    .rw-sub-options {
        background: #ffffff; border: 1px solid var(--m-border); border-radius: 16px; padding: 25px; margin-top: 15px; display: none;
        animation: rw-fadein 0.3s ease; box-shadow: 0 10px 20px rgba(0,0,0,0.03);
    }
    @keyframes rw-fadein { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

    .rw-duration-grid { display: flex; gap: 10px; margin-bottom: 20px;}
    .rw-dur-btn { flex:1; padding: 12px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.2s; color: var(--m-gray);}
    .rw-dur-btn:hover { border-color: var(--m-primary); color: var(--m-primary); }
    .rw-dur-btn.active { border-color: var(--m-primary); color: var(--m-primary); background: #F8FAFF; box-shadow: 0 4px 10px rgba(18,48,142,0.1);}

    /* â”€â”€ RIGHT SIDE: SUMMARY â”€â”€ */
    .rw-summary-panel {
        background: #ffffff; border-radius: 24px; overflow: hidden;
        box-shadow: 0 30px 60px rgba(18,48,142,0.12); position: sticky; top: 40px; border: 1px solid #e2e8f0;
    }
    .rw-sum-header {
        background: var(--m-grad-red);
        padding: 30px; color: white; position: relative; overflow: hidden;
    }
    .rw-sum-header::before {
        content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(18,48,142,0.8), transparent);
    }
    .rw-sum-header-content { position: relative; z-index: 1; display: flex; align-items: center; gap: 15px; }
    .rw-sum-icon { width: 44px; height: 44px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(5px); }
    .rw-sum-title { font-size: 1.3rem; font-weight: 800; line-height: 1.2; }
    .rw-sum-title small { display: block; font-size: 0.8rem; font-weight: 500; opacity: 0.9; margin-top: 4px;}

    .rw-sum-body { padding: 30px; }
    
    .rw-sum-list { display: flex; flex-direction: column; gap: 20px; border-bottom: 1px solid var(--m-border); padding-bottom: 25px; margin-bottom: 25px; }
    .rw-sum-row { display: flex; align-items: center; justify-content: space-between; font-size: 0.9rem; font-weight: 600; color: var(--m-gray); }
    .rw-sum-row .label { display: flex; align-items: center; gap: 10px; }
    .rw-sum-row .label svg { width: 18px; height: 18px; color: var(--m-primary); }
    .rw-sum-row .val { color: var(--m-text); font-weight: 800; display: flex; align-items: center; gap: 8px; }
    .rw-sum-row .val img { width: 24px; border-radius: 3px; }
    .rw-sum-row .val .lvl-bar { width: 16px; height: 16px; background: var(--l-b); border-radius: 3px; display:inline-block; }

    .rw-total-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; }
    .rw-total-label { display: flex; align-items: center; gap: 10px; font-weight: 800; color: var(--m-text); font-size: 1rem; }
    .rw-total-val { text-align: right; }
    .rw-total-price { font-size: 1.8rem; font-weight: 900; color: var(--m-text); line-height: 1; display: flex; align-items: center; gap: 5px;}
    .rw-total-price svg { width: 18px; color: #94a3b8; cursor: help; }
    .rw-total-approx { font-size: 0.8rem; color: #94a3b8; font-weight: 500; margin-top: 4px; }

    .rw-feats-list { list-style: none; padding: 0; margin-bottom: 30px; }
    .rw-feats-list li { display: flex; align-items: center; gap: 10px; font-size: 0.9rem; font-weight: 600; color: #334155; margin-bottom: 12px; }
    .rw-feats-list li svg { width: 18px; color: var(--m-primary); flex-shrink: 0; }

    .rw-action-btn {
        width: 100%; padding: 22px; border-radius: 16px; font-size: 1.15rem; font-weight: 800;
        background: linear-gradient(135deg, #E62243, #D61B38); color: white; border: none; cursor: pointer; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 10px 25px rgba(230,34,67,0.3); display: flex; justify-content: center; align-items: center; gap: 10px;
    }
    .rw-action-btn:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(230,34,67,0.4); }

    .rw-trust-bar { display: flex; justify-content: space-between; margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--m-border); }
    .rw-trust-item { display: flex; align-items: center; gap: 6px; font-size: 0.75rem; font-weight: 700; color: var(--m-gray); }
    .rw-trust-item svg { width: 14px; color: var(--m-primary); }

    /* Bottom Banner */
    .rw-bottom-banner {
        background: linear-gradient(135deg, #1e1b4b, #312e81, #1e3a8a); /* Deep rich gradient */
        background-image: radial-gradient(circle at 100% 0%, rgba(230, 34, 67, 0.25) 0%, transparent 50%), linear-gradient(135deg, #1e1b4b, #312e81, #1e3a8a);
        padding: 30px 40px; color: white; display: flex; justify-content: space-between; align-items: center;
        margin-top: 60px; border-radius: 20px; max-width: 1300px; margin-left: auto; margin-right: auto;
        box-shadow: 0 15px 35px rgba(30, 27, 75, 0.2);
    }
    .rw-bb-content { display: flex; align-items: center; gap: 20px; width: 100%; }
    .rw-bb-icon { color: #fbbf24; }
    .rw-bb-icon svg { width: 48px; height: 48px; stroke-width: 1.5; }
    .rw-bb-text h4 { font-size: 1.2rem; font-weight: 700; margin: 0 0 4px; color: #ffffff; }
    .rw-bb-text p { font-size: 0.85rem; font-weight: 400; color: #94a3b8; margin: 0; }
    .rw-bb-btn { background: transparent; border: 1px solid #ffffff; color: white; padding: 12px 24px; border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: 0.3s; margin-left: auto; display: flex; align-items: center; gap: 8px;}
    .rw-bb-btn:hover { background: rgba(255,255,255,0.1); }
    .rw-bb-btn svg { width: 16px; height: 16px; }
</style>

<div class="rima-pricing-wrapper">
    
    <!-- Hero Banner -->
    <div class="rw-hero">
        <h1 class="rw-hero-title">Intelligent Course Builder</h1>
        <div class="rw-hero-subtitle">Customize your language journey based on your specific needs.</div>
        
        <div class="rw-hero-features">
            <div class="rw-hero-feat"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg> Multiple Languages</div>
            <div class="rw-hero-feat"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"></path></svg> CEFR Levels</div>
            <div class="rw-hero-feat"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg> Flexible Learning</div>
            <div class="rw-hero-feat"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> Personalized Experience</div>
        </div>
    </div>

    <div class="rw-container">
        <div class="rw-grid-layout">
            
            <!-- Left Side: Wizard -->
            <div class="rw-selections">
                
                <!-- STEP 1: Language -->
                <div>
                    <div class="rw-step-heading"><span class="step-num">1</span> <div>Choose Language<small>Select the language you want to learn.</small></div></div>
                    <div class="rw-lang-grid">
                        <div class="rw-lang-card active" data-type="lang" data-val="en">
                            <div class="rw-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                            <img src="https://flagcdn.com/w80/gb.png" class="rw-lang-flag" alt="EN">
                            <div class="rw-lang-name">English</div>
                            <div class="rw-lang-sub">English</div>
                        </div>
                        <div class="rw-lang-card" data-type="lang" data-val="ro">
                            <div class="rw-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                            <img src="https://flagcdn.com/w80/ro.png" class="rw-lang-flag" alt="RO">
                            <div class="rw-lang-name">Romanian</div>
                            <div class="rw-lang-sub">Română</div>
                        </div>
                        <div class="rw-lang-card" data-type="lang" data-val="ja">
                            <div class="rw-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                            <img src="https://flagcdn.com/w80/jp.png" class="rw-lang-flag" alt="JA">
                            <div class="rw-lang-name">Japanese</div>
                            <div class="rw-lang-sub">æ—¥æœ¬èªž</div>
                        </div>
                        <div class="rw-lang-card rw-lang-add" style="cursor:default;">
                            <div class="plus">+</div>
                            <div class="rw-lang-name" style="font-size:0.9rem;">Add Language</div>
                            <div class="rw-lang-sub">More languages soon</div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Level -->
                <div>
                    <div class="rw-step-heading"><span class="step-num">2</span> <div>Choose Level<small>Select your current CEFR level.</small></div></div>
                    <div class="rw-lvl-grid">
                        <!-- A1-A2 -->
                        <div class="rw-lvl-card" data-type="lvl" data-val="A1-A2">
                            <div class="rw-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                            <div class="rw-lvl-header">
                                <div class="rw-lvl-bars"><div></div><div></div><div></div><div></div></div>
                                <div class="rw-lvl-code">A1-A2</div>
                            </div>
                            <div class="rw-lvl-title">Beginner / Elem.</div>
                            <div class="rw-lvl-desc">Basic communication and everyday situations.</div>
                        </div>
                        <!-- B1-B2 -->
                        <div class="rw-lvl-card active" data-type="lvl" data-val="B1-B2">
                            <div class="rw-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                            <div class="rw-lvl-header">
                                <div class="rw-lvl-bars"><div></div><div></div><div></div><div></div></div>
                                <div class="rw-lvl-code">B1-B2</div>
                            </div>
                            <div class="rw-lvl-title">Intermediate</div>
                            <div class="rw-lvl-desc">Express yourself with confidence.</div>
                        </div>
                        <!-- C1-C2 -->
                        <div class="rw-lvl-card" data-type="lvl" data-val="C1-C2">
                            <div class="rw-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                            <div class="rw-lvl-header">
                                <div class="rw-lvl-bars"><div></div><div></div><div></div><div></div></div>
                                <div class="rw-lvl-code">C1-C2</div>
                            </div>
                            <div class="rw-lvl-title">Advanced</div>
                            <div class="rw-lvl-desc">Mastery and near-native fluency.</div>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: Format -->
                <div>
                    <div class="rw-step-heading"><span class="step-num">3</span> <div>Learning Format<small>Choose how you want to learn.</small></div></div>
                    <div class="rw-fmt-grid">
                        <div class="rw-fmt-card active" data-type="fmt" data-val="platform">
                            <div class="rw-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                            <div class="rw-fmt-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg></div>
                            <div class="rw-fmt-title">Platform Only</div>
                            <div class="rw-fmt-desc">Self-paced learning on our advanced LMS.</div>
                        </div>
                        <div class="rw-fmt-card" data-type="fmt" data-val="individual">
                            <div class="rw-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                            <div class="rw-fmt-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></div>
                            <div class="rw-fmt-title">Individual</div>
                            <div class="rw-fmt-desc">1-on-1 Zoom sessions with a dedicated tutor.</div>
                        </div>
                        <div class="rw-fmt-card" data-type="fmt" data-val="group">
                            <div class="rw-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                            <div class="rw-fmt-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg></div>
                            <div class="rw-fmt-title">Group</div>
                            <div class="rw-fmt-desc">Small groups (3-8 students).</div>
                        </div>
                        <div class="rw-fmt-card" data-type="fmt" data-val="corporate">
                            <div class="rw-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                            <div class="rw-fmt-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><path d="M9 22v-4h6v4"></path><path d="M8 6h.01"></path><path d="M16 6h.01"></path><path d="M12 6h.01"></path><path d="M12 10h.01"></path><path d="M12 14h.01"></path><path d="M16 10h.01"></path><path d="M16 14h.01"></path><path d="M8 10h.01"></path><path d="M8 14h.01"></path></svg></div>
                            <div class="rw-fmt-title">Corporate</div>
                            <div class="rw-fmt-desc">B2B custom plans for teams and employees.</div>
                        </div>
                    </div>

                    <!-- Dynamic Sub-Options Container -->
                    <div id="sub-options" class="rw-sub-options">
                        <!-- Duration (For Individual & Corporate) -->
                        <div id="opt-duration" style="display:none;">
                            <div style="font-size:0.85rem; font-weight:800; color:var(--m-gray); margin-bottom:10px; text-transform:uppercase;">Select Duration</div>
                            <div class="rw-duration-grid">
                                <button class="rw-dur-btn active" data-dur="1">1 Month</button>
                                <button class="rw-dur-btn" data-dur="3">3 Months</button>
                                <button class="rw-dur-btn" data-dur="6">6 Months</button>
                            </div>
                        </div>

                        <!-- B2B Excel Upload (Only for Corporate) -->
                        <div id="opt-corporate-upload" style="display:none; margin-top:15px; padding:15px; background:#F8FAFF; border:1px dashed #3B82F6; border-radius:10px;">
                            <div style="font-size:0.85rem; font-weight:800; color:var(--m-primary); margin-bottom:8px; text-transform:uppercase;">ÎNCARCĂ LISTA ANGAJAȚI (EXCEL/CSV)</div>
                            <div style="font-size:0.75rem; color:var(--m-gray); margin-bottom:12px;">Prețul final va fi calculat automat pe baza numărului de persoane din fișier.</div>
                            <input type="file" id="rima_b2b_course_file" accept=".xlsx,.xls,.csv" style="display:none;">
                            <button class="rw-dur-btn" id="rima_b2b_trigger" style="width:100%; border-color:#3B82F6; color:#3B82F6;">Alege Fișier Excel / CSV</button>
                            <div id="rima_b2b_status" style="font-size:0.8rem; font-weight:600; color:#10B981; margin-top:8px; text-align:center;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Summary Sticky -->
            <div>
                <div class="rw-summary-panel">
                    <div class="rw-sum-header">
                        <div class="rw-sum-header-content">
                            <div class="rw-sum-icon"><svg viewBox="0 0 24 24" width="24" fill="none" stroke="white" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg></div>
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
                                <div class="val" id="sum-lvl-name">B1-B2 â€“ Intermediate <div class="lvl-bar"></div></div>
                            </div>
                            <div class="rw-sum-row">
                                <div class="label"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line></svg> Format</div>
                                <div class="val" id="sum-fmt-name">Platform Only</div>
                            </div>
                        </div>

                        <div class="rw-total-row">
                            <div class="rw-total-label"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width:20px; color:var(--m-primary)"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg> Total Investment</div>
                            <div class="rw-total-val">
                                <div class="rw-total-price"><span id="price-val">1300</span> RON <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg></div>
                                <div class="rw-total-approx" id="price-eur">(approx. 260 â‚¬)</div>
                            </div>
                        </div>

                        <ul class="rw-feats-list" id="sum-feats">
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> Full access to Rima LMS</li>
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> Interactive exercises</li>
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> Self-paced progress tracking</li>
                        </ul>

                        <button class="rw-action-btn">ENROLL NOW <svg viewBox="0 0 24 24" width="20" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></button>

                        <div class="rw-trust-bar">
                            <div class="rw-trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg> Secure payment</div>
                            <div class="rw-trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg> Instant access</div>
                            <div class="rw-trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg> 24/7 support</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Banner -->
    <div class="rw-bottom-banner">
        <div class="rw-bb-content">
            <div class="rw-bb-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 18h6"></path><path d="M10 22h4"></path><path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A6 6 0 1 0 7.5 11.5c.76.76 1.23 1.52 1.41 2.5"></path></svg></div>
            <div class="rw-bb-text">
                <h4>Recommended for you</h4>
                <p>Why this package?</p>
            </div>
            <button class="rw-bb-btn">View details &rarr;</button>
        </div>
    </div>

</div>

<script>
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
    document.getElementById('sum-lvl-name').innerHTML = state.lvl + ' â€“ ' + lvlNameMap[state.lvl] + ' <div class="lvl-bar" style="background:'+lvlColors[state.lvl]+'"></div>';

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
        feats = ['Full access to Rima LMS', 'Interactive exercises', 'Self-paced progress tracking'];
    } else if (state.fmt === 'individual') {
        let idx = (state.dur === 1) ? 0 : (state.dur === 3 ? 1 : 2);
        price = lvlData.ind[idx][0];
        formatLabel = 'Individual â€“ ' + state.dur + ' Month(s)';
        feats = [lvlData.ind[idx][3], 'Dedicated 1-on-1 tutor', 'Live Zoom sessions'];
    } else if (state.fmt === 'group') {
        // Group is roughly 70% of Individual
        let idx = (state.dur === 1) ? 0 : (state.dur === 3 ? 1 : 2);
        price = Math.round(lvlData.ind[idx][0] * 0.7);
        formatLabel = 'Group â€“ ' + state.dur + ' Month(s)';
        feats = [lvlData.ind[idx][3], 'Small group (3-8 students)', 'Live Zoom sessions'];
    } else if (state.fmt === 'corporate') {
        let idx = (state.dur === 1) ? 0 : (state.dur === 3 ? 1 : 2);
        price = lvlData.corp[idx][0];
        formatLabel = 'Corporate â€“ ' + state.dur + ' Month(s)';
        feats = ['B2B Custom Plan', 'Team progress tracking', 'Dedicated account manager'];
    }

    // Multiply by employees if corporate
    if (state.fmt === 'corporate' && state.empCount > 1) {
        price = price * state.empCount;
        formatLabel += ' (' + state.empCount + ' angajați)';
    }

    document.getElementById('sum-fmt-name').innerText = formatLabel;
    document.getElementById('price-val').innerText = price;
    document.getElementById('price-eur').innerText = '(approx. ' + Math.round(price / 5) + ' â‚¬)';

    // Update Features
    const ul = document.getElementById('sum-feats');
    ul.innerHTML = '';
    feats.forEach(f => {
        ul.innerHTML += '<li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> ' + f + '</li>';
    });
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
                triggerBtn.innerText = 'Fișier Încărcat âœ“';
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


