<?php
/**
 * RIMA Academy — Email Notification System
 * 
 * Handles all transactional emails:
 *  1. Welcome + account activation (user + admin)
 *  2. Order received / pending (user)
 *  3. BACS on-hold — upload payment proof (user) + review alert (admin)
 *  4. BACS approved — payment verified (user)
 *  5. Order processing/completed (user + admin)
 *  6. Order cancelled / failed / refunded (user + admin)
 *  7. Course enrollment confirmation (user + admin)
 *  8. Course completion + certificate ready (user + admin)
 *  9. Course expiry reminder — 7 days (user)
 * 10. Live session / Zoom reminder — 24h before (user)
 * 11. Admin manually enrolls student (user)
 * 12. Quiz passed / failed (user)
 * 13. Password change confirmation (user)
 * 14. Account inactive 30-day reminder (user)
 * 15. Password reset (user)
 * 16. Contact form notification (admin)
 * 
 * @package RimaAcademy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ============================================================
   CONSTANTS & HELPERS
   ============================================================ */

define( 'RIMA_EMAIL_ACCENT',     '#C8102E' );
define( 'RIMA_EMAIL_DARK',       '#0D1B2A' );
define( 'RIMA_EMAIL_MID',        '#1A2E45' );
define( 'RIMA_EMAIL_FROM_NAME',  'Rima Academy' );
define( 'RIMA_EMAIL_FROM_ADDR',  'office@rima-academy.com' );
define( 'RIMA_EMAIL_ADMIN',      get_option( 'admin_email' ) );
define( 'RIMA_SITE_URL',         'https://rima-academy.com' );
define( 'RIMA_LOGO_URL',         'https://rima-academy.com/wp-content/uploads/2026/06/light-logo.png' );

add_filter( 'wp_mail_content_type', function() { return 'text/html'; } );
add_filter( 'wp_mail_from',         function() { return RIMA_EMAIL_FROM_ADDR; } );
add_filter( 'wp_mail_from_name',    function() { return RIMA_EMAIL_FROM_NAME; } );

/* ============================================================
   TEMPLATE ENGINE
   ============================================================ */

function rima_email_wrap( $inner_html, $preview = '' ) {
    $year   = date( 'Y' );
    $logo   = esc_url( RIMA_LOGO_URL );
    $site   = esc_url( RIMA_SITE_URL );
    $accent = RIMA_EMAIL_ACCENT;
    $dark   = RIMA_EMAIL_DARK;
    $mid    = RIMA_EMAIL_MID;

    ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Rima Academy</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
  *{box-sizing:border-box;margin:0;padding:0}
  body{margin:0;padding:0;background:<?php echo $dark; ?>;font-family:'Inter',Arial,sans-serif}
  img{border:0;outline:none;text-decoration:none}
  a{text-decoration:none}
  .wrapper{background:<?php echo $dark; ?>;padding:40px 20px 60px}
  .container{max-width:620px;margin:0 auto}
  .hd{background:linear-gradient(135deg,<?php echo $mid; ?> 0%,<?php echo $dark; ?> 100%);border-radius:20px 20px 0 0;padding:36px 40px;text-align:center;border-bottom:3px solid <?php echo $accent; ?>}
  .hd img{height:52px;width:auto}
  .tagline{color:rgba(255,255,255,.45);font-size:11px;letter-spacing:2px;text-transform:uppercase;margin-top:10px}
  .hero{background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);padding:32px 40px;text-align:center}
  .hero-icon{font-size:48px;line-height:1;margin-bottom:12px}
  .hero h1{color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0}
  .hero p{color:rgba(255,255,255,.85);font-size:14px;margin-top:10px;line-height:1.6}
  .body{background:<?php echo $mid; ?>;padding:36px 40px}
  .body p{color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px}
  .body p.greeting{color:#fff;font-size:17px;font-weight:600}
  .info-card{background:<?php echo $dark; ?>;border-radius:14px;padding:20px;margin:20px 0;border:1px solid rgba(255,255,255,.08)}
  .row{display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06)}
  .row:last-child{border-bottom:none}
  .lbl{color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0}
  .val{color:#fff;font-size:14px;font-weight:500}
  .steps{margin:24px 0}
  .step{display:flex;align-items:flex-start;margin-bottom:18px}
  .step-num{background:<?php echo $accent; ?>;color:#fff;font-size:13px;font-weight:700;border-radius:50%;width:28px;height:28px;line-height:28px;text-align:center;flex-shrink:0;margin-right:14px;margin-top:2px}
  .step-text{color:rgba(255,255,255,.8);font-size:14px;line-height:1.6}
  .step-text strong{color:#fff}
  .cta-wrap{text-align:center;margin:28px 0}
  .cta-btn{display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4)}
  .divider{height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,.1),transparent);margin:22px 0}
  .highlight{background:linear-gradient(135deg,rgba(200,16,46,.15),rgba(200,16,46,.05));border-left:4px solid <?php echo $accent; ?>;border-radius:0 12px 12px 0;padding:16px 18px;margin:18px 0}
  .highlight p{color:rgba(255,255,255,.85);font-size:14px;margin:0;line-height:1.6}
  .highlight p strong{color:#fff}
  .badge{display:inline-block;background:rgba(200,16,46,.2);border:1px solid <?php echo $accent; ?>;color:<?php echo $accent; ?>;font-size:11px;font-weight:700;padding:4px 12px;border-radius:50px;letter-spacing:1px;text-transform:uppercase;margin-bottom:14px}
  .footer{background:<?php echo $dark; ?>;border-radius:0 0 20px 20px;padding:26px 40px;text-align:center;border-top:1px solid rgba(255,255,255,.06)}
  .footer a{color:rgba(255,255,255,.35);font-size:12px;margin:0 8px}
  .footer p{color:rgba(255,255,255,.22);font-size:11px;margin-top:12px;line-height:1.6}
  @media(max-width:480px){.hd,.hero,.body,.footer{padding-left:22px!important;padding-right:22px!important}.hero h1{font-size:20px!important}.cta-btn{padding:13px 26px!important}}
</style>
</head>
<body style="margin:0;padding:0;background:<?php echo $dark; ?>;font-family:'Inter',Arial,sans-serif;">
<div style="display:none;max-height:0;overflow:hidden"><?php echo esc_html( $preview ); ?>&nbsp;&#8204;&nbsp;&#8204;&nbsp;&#8204;</div>
<div class="wrapper" style="background:<?php echo $dark; ?>;padding:40px 20px 60px;">
  <div class="container" style="max-width:620px;margin:0 auto;background:<?php echo $mid; ?>;border-radius:20px;overflow:hidden;">
    <div class="hd" style="background:<?php echo $dark; ?>;padding:36px 40px;text-align:center;border-bottom:3px solid <?php echo $accent; ?>;">
      <a href="<?php echo $site; ?>"><img src="<?php echo $logo; ?>" alt="Rima Academy" style="height:52px;width:auto;border:0;outline:none;"></a>
      <div class="tagline" style="color:rgba(255,255,255,.45);font-size:11px;letter-spacing:2px;text-transform:uppercase;margin-top:10px;">Learn &middot; Speak &middot; Connect &middot; Succeed</div>
    </div>

    <?php echo $inner_html; ?>

    <div class="footer" style="background:<?php echo $dark; ?>;padding:26px 40px;text-align:center;border-top:1px solid rgba(255,255,255,.06);">
      <a href="<?php echo $site; ?>" style="color:rgba(255,255,255,.35);font-size:12px;margin:0 8px;text-decoration:none;">Home</a>
      <a href="<?php echo $site; ?>/our-courses/" style="color:rgba(255,255,255,.35);font-size:12px;margin:0 8px;text-decoration:none;">Courses</a>
      <a href="<?php echo $site; ?>/contact/" style="color:rgba(255,255,255,.35);font-size:12px;margin:0 8px;text-decoration:none;">Contact</a>
      <a href="<?php echo $site; ?>/privacy-policy/" style="color:rgba(255,255,255,.35);font-size:12px;margin:0 8px;text-decoration:none;">Privacy</a>
      <p style="color:rgba(255,255,255,.22);font-size:11px;margin-top:12px;line-height:1.6;">&copy; <?php echo $year; ?> Rima Academy &mdash; office@rima-academy.com</p>
    </div>
  </div>
</div>
</body>
</html>
<?php
    return ob_get_clean();
}

/* ============================================================
   CORE SEND HELPER
   ============================================================ */

function rima_send_email( $to, $subject, $inner, $preview = '' ) {
    if ( empty( $to ) ) return false;
    $html    = rima_email_wrap( $inner, $preview );
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . RIMA_EMAIL_FROM_NAME . ' <' . RIMA_EMAIL_FROM_ADDR . '>',
    );
    return wp_mail( $to, $subject, $html, $headers );
}

/* ============================================================
   1. WELCOME EMAIL — NEW USER REGISTRATION
   ============================================================ */

add_action( 'user_register', 'rima_on_user_register', 10, 1 );

function rima_on_user_register( $user_id ) {
    $user = get_userdata( $user_id );
    if ( ! $user ) return;

    $name        = esc_html( $user->display_name ?: $user->user_login );
    $email       = $user->user_email;
    $login       = esc_html( $user->user_login );
    $login_url   = esc_url( wp_login_url() );
    $courses_url = esc_url( RIMA_SITE_URL . '/our-courses/' );

    // ── User: Welcome email ──────────────────────────────────
    ob_start(); ?>
<div style="background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);padding:32px 40px;text-align:center;">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#127891;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">Welcome to Rima Academy!</h1>
  <p style="color:rgba(255,255,255,.85);font-size:14px;margin-top:10px;line-height:1.6;">Your account is ready &mdash; your language journey starts now.</p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <p style="color:#fff;font-size:17px;font-weight:600;margin-bottom:16px;">Hi <?php echo $name; ?>,</p>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">We're thrilled to have you on board! Rima Academy is your gateway to mastering <strong style="color:#fff;">English</strong>, <strong style="color:#fff;">Japanese</strong>, <strong style="color:#fff;">Romanian</strong> and more &mdash; with native tutors and live interactive sessions.</p>

  <div style="background:<?php echo $dark; ?>;border-radius:14px;padding:20px;margin:20px 0;border:1px solid rgba(255,255,255,.08);">
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Username</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $login; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Email</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo esc_html( $email ); ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Status</span><span style="color:#22c55e;font-size:14px;font-weight:500;">&#10004; Active</span></div>
  </div>

  <div style="margin:24px 0;">
    <div style="display:flex;align-items:flex-start;margin-bottom:18px;"><div style="background:<?php echo $accent; ?>;color:#fff;font-size:13px;font-weight:700;border-radius:50%;width:28px;height:28px;line-height:28px;text-align:center;flex-shrink:0;margin-right:14px;margin-top:2px;">1</div><div style="color:rgba(255,255,255,.8);font-size:14px;line-height:1.6;"><strong style="color:#fff;">Log in</strong> to your account using the button below.</div></div>
    <div style="display:flex;align-items:flex-start;margin-bottom:18px;"><div style="background:<?php echo $accent; ?>;color:#fff;font-size:13px;font-weight:700;border-radius:50%;width:28px;height:28px;line-height:28px;text-align:center;flex-shrink:0;margin-right:14px;margin-top:2px;">2</div><div style="color:rgba(255,255,255,.8);font-size:14px;line-height:1.6;"><strong style="color:#fff;">Browse our courses</strong> and pick the language that excites you.</div></div>

    <div style="display:flex;align-items:flex-start;margin-bottom:18px;"><div style="background:<?php echo $accent; ?>;color:#fff;font-size:13px;font-weight:700;border-radius:50%;width:28px;height:28px;line-height:28px;text-align:center;flex-shrink:0;margin-right:14px;margin-top:2px;">3</div><div style="color:rgba(255,255,255,.8);font-size:14px;line-height:1.6;"><strong style="color:#fff;">Enroll</strong> and join your first live session with a native tutor.</div></div>
  </div>

  <div style="text-align:center;margin:28px 0;">
    <a href="<?php echo $login_url; ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;">Go to My Account</a>
  </div>

  <div style="height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,.1),transparent);margin:22px 0;"></div>
  <div style="background:linear-gradient(135deg,rgba(200,16,46,.15),rgba(200,16,46,.05));border-left:4px solid <?php echo $accent; ?>;border-radius:0 12px 12px 0;padding:16px 18px;margin:18px 0;">
    <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">&#127775; <strong style="color:#fff;">Pro tip:</strong> Check out our flagship English &amp; Japanese courses. Certification paths available for IELTS, JLPT, Cambridge &amp; TOEFL.</p>
  </div>

  <div class="cta-wrap" style="margin-top:10px">
    <a href="<?php echo $courses_url; ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;" style="background:linear-gradient(135deg,#1A2E45,#0D1B2A);box-shadow:none;border:1px solid rgba(255,255,255,.15)">Browse Courses</a>
  </div>
</div>
<?php
    $user_inner = ob_get_clean();
    rima_send_email( $email, 'Welcome to Rima Academy — Your Account is Ready!', $user_inner, "Hi {$name}, your account is active. Start learning today!" );

    // ── Admin: New registration alert ────────────────────────
    $registered = current_time( 'mysql' );
    ob_start(); ?>
<div class="hero" style="background:linear-gradient(135deg,#1A2E45,#0D1B2A);border-bottom:3px solid #C8102E">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#128100;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">New User Registration</h1>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">A new student has just joined Rima Academy.</p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <div class="badge">Admin Alert</div>
  <div style="background:<?php echo $dark; ?>;border-radius:14px;padding:20px;margin:20px 0;border:1px solid rgba(255,255,255,.08);">
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Name</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $name; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Username</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $login; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Email</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo esc_html( $email ); ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Registered</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $registered; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">User ID</span><span style="color:#fff;font-size:14px;font-weight:500;">#<?php echo $user_id; ?></span></div>
  </div>
  <div style="text-align:center;margin:28px 0;">
    <a href="<?php echo esc_url( admin_url( 'users.php' ) ); ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;">View in Dashboard</a>
  </div>
</div>
<?php
    $admin_inner = ob_get_clean();
    rima_send_email( RIMA_EMAIL_ADMIN, "New Registration: {$name} ({$email})", $admin_inner, "New student registered: {$name}" );
}

/* ============================================================
   2. COURSE ENROLLMENT — WOOCOMMERCE ORDER
   ============================================================ */

add_action( 'woocommerce_order_status_changed', 'rima_on_order_status_change', 10, 4 );

function rima_on_order_status_change( $order_id, $old, $new_status, $order ) {
    if ( ! in_array( $new_status, array( 'processing', 'completed' ), true ) ) return;

    $user_id = $order->get_user_id();
    if ( ! $user_id ) return;

    $user      = get_userdata( $user_id );
    $name      = esc_html( $user->display_name ?: $user->user_login );
    $email     = $user->user_email;
    $order_num = $order->get_order_number();
    $total     = $order->get_formatted_order_total();
    $my_acc    = esc_url( RIMA_SITE_URL . '/my-account/' );

    $course_names = array();
    foreach ( $order->get_items() as $item ) {
        $course_names[] = esc_html( $item->get_name() );
    }
    if ( empty( $course_names ) ) return;

    $courses_rows = '';
    foreach ( $course_names as $cn ) {
        $courses_rows .= '<div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:#fff;font-size:14px;font-weight:500;">&#128218; ' . $cn . '</span></div>';
    }
    $courses_str = implode( ', ', $course_names );

    // ── User: Enrollment confirmation ─────────────────────────
    ob_start(); ?>
<div style="background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);padding:32px 40px;text-align:center;">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#9989;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">You're Enrolled!</h1>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Your course access is now active. Start learning immediately.</p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <p style="color:#fff;font-size:17px;font-weight:600;margin-bottom:16px;">Hi <?php echo $name; ?>,</p>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Great news! Your enrollment has been confirmed and you have <strong style="color:#fff;">full access</strong> to your course(s) right now.</p>
  <div style="background:<?php echo $dark; ?>;border-radius:14px;padding:20px;margin:20px 0;border:1px solid rgba(255,255,255,.08);">
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Order #</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $order_num; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Total Paid</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $total; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Status</span><span style="color:#22c55e;font-size:14px;font-weight:500;">&#10004; Confirmed</span></div>
  </div>
  <p style="margin-top:18px;font-weight:600;color:#fff">Enrolled course(s):</p>
  <div class="info-card" style="margin-top:8px"><?php echo $courses_rows; ?></div>
  <div style="text-align:center;margin:28px 0;">
    <a href="<?php echo $my_acc; ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;">Start Learning Now</a>
  </div>
  <div style="background:linear-gradient(135deg,rgba(200,16,46,.15),rgba(200,16,46,.05));border-left:4px solid <?php echo $accent; ?>;border-radius:0 12px 12px 0;padding:16px 18px;margin:18px 0;">
    <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">&#128197; <strong style="color:#fff;">Reminder:</strong> Check your dashboard for upcoming live sessions and schedule your first class with a native tutor.</p>
  </div>
</div>
<?php
    $user_inner = ob_get_clean();
    rima_send_email( $email, "Enrollment Confirmed — {$courses_str}", $user_inner, "You're enrolled in {$courses_str}!" );

    // ── Admin: Sales alert ────────────────────────────────────
    $login = esc_html( $user->user_login );
    ob_start(); ?>
<div class="hero" style="background:linear-gradient(135deg,#0d3a2a,#0D1B2A);border-bottom:3px solid #22c55e">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#128176;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">New Enrollment</h1>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Order #<?php echo $order_num; ?> confirmed &mdash; <?php echo $total; ?></p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <div class="badge">Sales Alert</div>
  <div style="background:<?php echo $dark; ?>;border-radius:14px;padding:20px;margin:20px 0;border:1px solid rgba(255,255,255,.08);">
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Student</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $name; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Email</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo esc_html( $email ); ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Username</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $login; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Order #</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $order_num; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Total</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $total; ?></span></div>
  </div>
  <p style="color:#fff;font-weight:600;margin-top:16px">Courses:</p>
  <div class="info-card" style="margin-top:8px"><?php echo $courses_rows; ?></div>
  <div style="text-align:center;margin:28px 0;">
    <a href="<?php echo esc_url( admin_url( 'post.php?post=' . $order_id . '&action=edit' ) ); ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;">View Order</a>
  </div>
</div>
<?php
    $admin_inner = ob_get_clean();
    rima_send_email( RIMA_EMAIL_ADMIN, "New Enrollment: {$name} — Order #{$order_num} ({$total})", $admin_inner, "New enrollment by {$name}: {$courses_str}" );
}

/* ============================================================
   3. PASSWORD RESET — OVERRIDE WP DEFAULT
   ============================================================ */

add_filter( 'retrieve_password_message', 'rima_custom_password_reset_email', 10, 4 );

function rima_custom_password_reset_email( $message, $key, $user_login, $user_data ) {
    $name      = esc_html( $user_data->display_name ?: $user_login );
    $reset_url = esc_url( network_site_url( 'wp-login.php?action=rp&key=' . $key . '&login=' . rawurlencode( $user_login ), 'login' ) );

    ob_start(); ?>
<div style="background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);padding:32px 40px;text-align:center;">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#128272;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">Password Reset Request</h1>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">We received a request to reset your password.</p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <p style="color:#fff;font-size:17px;font-weight:600;margin-bottom:16px;">Hi <?php echo $name; ?>,</p>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Someone requested a password reset for your Rima Academy account. If this was you, click the button below to set a new password. This link will expire in <strong style="color:#fff;">24 hours</strong>.</p>
  <div style="text-align:center;margin:28px 0;">
    <a href="<?php echo $reset_url; ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;">Reset My Password</a>
  </div>
  <div style="height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,.1),transparent);margin:22px 0;"></div>
  <div style="background:linear-gradient(135deg,rgba(200,16,46,.15),rgba(200,16,46,.05));border-left:4px solid <?php echo $accent; ?>;border-radius:0 12px 12px 0;padding:16px 18px;margin:18px 0;">
    <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">&#9888;&#65039; <strong style="color:#fff;">Didn't request this?</strong> You can safely ignore this email. If you're concerned, contact us at <a href="mailto:office@rima-academy.com" style="color:#C8102E">office@rima-academy.com</a>.</p>
  </div>
  <p style="color:rgba(255,255,255,.35);font-size:12px;margin-top:16px;word-break:break-all">
    Or paste this link: <a href="<?php echo $reset_url; ?>" style="color:#C8102E"><?php echo $reset_url; ?></a>
  </p>
</div>
<?php
    $inner = ob_get_clean();
    return rima_email_wrap( $inner, "Reset your Rima Academy password — link expires in 24h" );
}

add_filter( 'retrieve_password_title', function() {
    return 'Password Reset — Rima Academy';
} );

/* ============================================================
   4. CONTACT FORM 7 — ADMIN NOTIFICATION
   ============================================================ */

add_action( 'wpcf7_mail_sent', 'rima_cf7_admin_notification', 10, 1 );

function rima_cf7_admin_notification( $contact_form ) {
    if ( ! class_exists( 'WPCF7_Submission' ) ) return;
    $submission = WPCF7_Submission::get_instance();
    if ( ! $submission ) return;

    $data    = $submission->get_posted_data();
    $sender  = sanitize_text_field( isset( $data['your-name'] )    ? $data['your-name']    : 'Unknown' );
    $s_email = sanitize_email(      isset( $data['your-email'] )   ? $data['your-email']   : '' );
    $subject = sanitize_text_field( isset( $data['your-subject'] ) ? $data['your-subject'] : 'No subject' );
    $body    = esc_html( isset( $data['your-message'] ) ? wp_strip_all_tags( $data['your-message'] ) : '' );

    ob_start(); ?>
<div class="hero" style="background:linear-gradient(135deg,#1A3A5C,#0D1B2A);border-bottom:3px solid #3B82F6">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#9993;&#65039;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">New Contact Form Message</h1>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Received via rima-academy.com/contact/</p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <div class="badge">Contact Form</div>
  <div style="background:<?php echo $dark; ?>;border-radius:14px;padding:20px;margin:20px 0;border:1px solid rgba(255,255,255,.08);">
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">From</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo esc_html( $sender ); ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Email</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo esc_html( $s_email ); ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Subject</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo esc_html( $subject ); ?></span></div>
  </div>
  <div class="highlight" style="margin-top:18px">
    <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;"><strong style="color:#fff;">Message:</strong><br><br><?php echo nl2br( $body ); ?></p>
  </div>
  <div style="text-align:center;margin:28px 0;">
    <a href="mailto:<?php echo esc_attr( $s_email ); ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;">Reply to <?php echo esc_html( $sender ); ?></a>
  </div>
</div>
<?php
    $inner = ob_get_clean();
    rima_send_email( RIMA_EMAIL_ADMIN, "New Contact: {$subject} — from {$sender}", $inner, "New message from {$sender}: {$subject}" );
}

/* ============================================================
   5. SUPPRESS DEFAULT WP REGISTRATION EMAILS
   ============================================================ */

// Suppress WP default plain-text admin notification (we send a better one)
add_filter( 'wp_new_user_notification_email_admin', function( $email_data ) {
    $email_data['to'] = '';
    return $email_data;
} );

// Suppress WP default plain-text user notification (we send a branded one)
add_filter( 'wp_new_user_notification_email', function( $email_data ) {
    $email_data['to'] = '';
    return $email_data;
} );

/* ============================================================
   6. WOOCOMMERCE ORDER LIFECYCLE EMAILS
   ============================================================ */

/**
 * Helper: build an order items table HTML for emails.
 */
function rima_email_order_items_table( $order ) {
    $rows = '';
    foreach ( $order->get_items() as $item ) {
        $name = esc_html( $item->get_name() );
        $qty  = $item->get_quantity();
        $line = wc_price( $item->get_total() );
        $rows .= '<div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span class="val" style="flex:1">&#128218; ' . $name . '</span><span style="color:rgba(255,255,255,.5);font-size:13px;margin:0 12px">x' . $qty . '</span><span class="val" style="white-space:nowrap">' . $line . '</span></div>';
    }
    return $rows;
}

/**
 * Helper: safely get user name from order (works for guests too).
 */
function rima_email_order_name( $order ) {
    $first = $order->get_billing_first_name();
    $last  = $order->get_billing_last_name();
    $name  = trim( $first . ' ' . $last );
    if ( empty( $name ) ) {
        $user = get_userdata( $order->get_user_id() );
        $name = $user ? $user->display_name : 'Customer';
    }
    return esc_html( $name );
}

/**
 * Hook: woocommerce_order_status_changed fires for ALL status transitions.
 * We handle every relevant transition with a branded email.
 * (The enrollment email in section 2 handles processing/completed — here we add the rest.)
 */
add_action( 'woocommerce_order_status_changed', 'rima_order_lifecycle_emails', 20, 4 );

function rima_order_lifecycle_emails( $order_id, $old_status, $new_status, $order ) {
    $name       = rima_email_order_name( $order );
    $email      = $order->get_billing_email();
    $order_num  = $order->get_order_number();
    $total      = $order->get_formatted_order_total();
    $order_url  = esc_url( $order->get_view_order_url() );
    $admin_url  = esc_url( admin_url( 'post.php?post=' . $order_id . '&action=edit' ) );
    $items_html = rima_email_order_items_table( $order );
    $date       = $order->get_date_created()->date_i18n( 'd M Y, H:i' );
    $method     = esc_html( $order->get_payment_method_title() );

    // ── A. ORDER PLACED / PENDING PAYMENT ────────────────────────
    if ( $new_status === 'pending' ) {
        ob_start(); ?>
<div class="hero" style="background:linear-gradient(135deg,#1A3A5C,#0D1B2A);border-bottom:3px solid #3B82F6">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#128203;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">Order Received!</h1>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">We've received your order and are awaiting payment confirmation.</p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <p style="color:#fff;font-size:17px;font-weight:600;margin-bottom:16px;">Hi <?php echo $name; ?>,</p>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Thank you for your order! We've received it and it's now <strong style="color:#fff;">awaiting payment</strong>. Once payment is confirmed, your course access will be activated immediately.</p>
  <div style="background:<?php echo $dark; ?>;border-radius:14px;padding:20px;margin:20px 0;border:1px solid rgba(255,255,255,.08);">
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Order #</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $order_num; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Date</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $date; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Payment</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $method; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Status</span><span class="val" style="color:#F59E0B">&#8987; Pending Payment</span></div>
  </div>
  <p style="margin-top:16px;font-weight:600;color:#fff">Items ordered:</p>
  <div class="info-card" style="margin-top:8px"><?php echo $items_html; ?></div>
  <div class="info-card" style="margin-top:12px">
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Total</span><span class="val" style="color:#22c55e;font-size:18px;font-weight:700"><?php echo $total; ?></span></div>
  </div>
  <div style="text-align:center;margin:28px 0;">
    <a href="<?php echo $order_url; ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;" style="background:linear-gradient(135deg,#1A3A5C,#0D1B2A);box-shadow:none;border:1px solid rgba(255,255,255,.2)">View My Order</a>
  </div>
  <div style="background:linear-gradient(135deg,rgba(200,16,46,.15),rgba(200,16,46,.05));border-left:4px solid <?php echo $accent; ?>;border-radius:0 12px 12px 0;padding:16px 18px;margin:18px 0;">
    <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">&#128337; <strong style="color:#fff;">Please complete your payment</strong> to activate your course access. If you need help, contact us at <a href="mailto:office@rima-academy.com" style="color:#C8102E">office@rima-academy.com</a>.</p>
  </div>
</div>
<?php
        $inner = ob_get_clean();
        rima_send_email( $email, "Order #{$order_num} Received — Awaiting Payment", $inner, "Order #{$order_num} received — complete payment to activate access." );
    }

    // ── B. ON-HOLD (awaiting bank transfer, manual review) ────────
    if ( $new_status === 'on-hold' ) {
        ob_start(); ?>
<div class="hero" style="background:linear-gradient(135deg,#3D2A0A,#0D1B2A);border-bottom:3px solid #F59E0B">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#128336;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">Order On Hold</h1>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Your order is being reviewed — we'll notify you shortly.</p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <p style="color:#fff;font-size:17px;font-weight:600;margin-bottom:16px;">Hi <?php echo $name; ?>,</p>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Your order is currently <strong style="color:#fff;">on hold</strong>. This usually happens with bank transfer payments or when a manual review is needed. We'll confirm as soon as payment is verified.</p>
  <div style="background:<?php echo $dark; ?>;border-radius:14px;padding:20px;margin:20px 0;border:1px solid rgba(255,255,255,.08);">
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Order #</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $order_num; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Total</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $total; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Status</span><span class="val" style="color:#F59E0B">&#128336; On Hold</span></div>
  </div>
  <div class="info-card" style="margin-top:8px"><?php echo $items_html; ?></div>
  <div style="background:linear-gradient(135deg,rgba(200,16,46,.15),rgba(200,16,46,.05));border-left:4px solid <?php echo $accent; ?>;border-radius:0 12px 12px 0;padding:16px 18px;margin:18px 0;">
    <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">&#128222; If you made a bank transfer, please allow 1-2 business days. Contact us at <a href="mailto:office@rima-academy.com" style="color:#C8102E">office@rima-academy.com</a> with your transfer proof to speed things up.</p>
  </div>
  <div style="text-align:center;margin:28px 0;">
    <a href="<?php echo $order_url; ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;" style="background:linear-gradient(135deg,#3D2A0A,#1A1400);box-shadow:none;border:1px solid #F59E0B">View Order Status</a>
  </div>
</div>
<?php
        $inner = ob_get_clean();
        rima_send_email( $email, "Order #{$order_num} On Hold — Awaiting Verification", $inner, "Your order #{$order_num} is on hold, pending payment verification." );

        // Admin alert for on-hold
        ob_start(); ?>
<div class="hero" style="background:linear-gradient(135deg,#3D2A0A,#0D1B2A);border-bottom:3px solid #F59E0B">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#128336;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">Order On Hold</h1>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Order #<?php echo $order_num; ?> needs manual review.</p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <div class="badge" style="background:rgba(245,158,11,.2);border-color:#F59E0B;color:#F59E0B">Needs Review</div>
  <div style="background:<?php echo $dark; ?>;border-radius:14px;padding:20px;margin:20px 0;border:1px solid rgba(255,255,255,.08);">
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Customer</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $name; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Email</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo esc_html( $email ); ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Order #</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $order_num; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Method</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $method; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Total</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $total; ?></span></div>
  </div>
  <div style="text-align:center;margin:28px 0;">
    <a href="<?php echo $admin_url; ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;">Review Order</a>
  </div>
</div>
<?php
        $admin_inner = ob_get_clean();
        rima_send_email( RIMA_EMAIL_ADMIN, "&#9203; Order #{$order_num} On Hold — Manual Review Required", $admin_inner, "Order #{$order_num} from {$name} is on hold." );
    }

    // ── C. ORDER CANCELLED ─────────────────────────────────────────
    if ( $new_status === 'cancelled' ) {
        ob_start(); ?>
<div class="hero" style="background:linear-gradient(135deg,#2D1A1A,#0D1B2A);border-bottom:3px solid #EF4444">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#10060;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">Order Cancelled</h1>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Your order #<?php echo $order_num; ?> has been cancelled.</p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <p style="color:#fff;font-size:17px;font-weight:600;margin-bottom:16px;">Hi <?php echo $name; ?>,</p>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Your order has been <strong style="color:#fff;">cancelled</strong>. If you did not request this cancellation or believe this is a mistake, please contact us immediately.</p>
  <div style="background:<?php echo $dark; ?>;border-radius:14px;padding:20px;margin:20px 0;border:1px solid rgba(255,255,255,.08);">
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Order #</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $order_num; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Total</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $total; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Status</span><span class="val" style="color:#EF4444">&#10060; Cancelled</span></div>
  </div>
  <div style="text-align:center;margin:28px 0;">
    <a href="<?php echo esc_url( RIMA_SITE_URL . '/our-courses/' ); ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;">Browse Courses Again</a>
  </div>
  <div style="background:linear-gradient(135deg,rgba(200,16,46,.15),rgba(200,16,46,.05));border-left:4px solid <?php echo $accent; ?>;border-radius:0 12px 12px 0;padding:16px 18px;margin:18px 0;">
    <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">&#128222; Questions? Contact us at <a href="mailto:office@rima-academy.com" style="color:#C8102E">office@rima-academy.com</a> or WhatsApp <a href="https://wa.me/40736852666" style="color:#25D366">+40 736 852 666</a>.</p>
  </div>
</div>
<?php
        $inner = ob_get_clean();
        rima_send_email( $email, "Order #{$order_num} Cancelled", $inner, "Your order #{$order_num} has been cancelled." );

        // Admin cancelled alert
        ob_start(); ?>
<div class="hero" style="background:linear-gradient(135deg,#2D1A1A,#0D1B2A);border-bottom:3px solid #EF4444">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#10060;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">Order Cancelled</h1>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Order #<?php echo $order_num; ?> was cancelled.</p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <div class="badge" style="background:rgba(239,68,68,.2);border-color:#EF4444;color:#EF4444">Cancelled</div>
  <div style="background:<?php echo $dark; ?>;border-radius:14px;padding:20px;margin:20px 0;border:1px solid rgba(255,255,255,.08);">
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Customer</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $name; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Email</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo esc_html( $email ); ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Order #</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $order_num; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Total</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $total; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Prev Status</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo esc_html( $old_status ); ?></span></div>
  </div>
  <div style="text-align:center;margin:28px 0;">
    <a href="<?php echo $admin_url; ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;">View in Dashboard</a>
  </div>
</div>
<?php
        $admin_inner = ob_get_clean();
        rima_send_email( RIMA_EMAIL_ADMIN, "&#10060; Order #{$order_num} Cancelled — {$name}", $admin_inner, "Order #{$order_num} from {$name} was cancelled." );
    }

    // ── D. ORDER FAILED ────────────────────────────────────────────
    if ( $new_status === 'failed' ) {
        ob_start(); ?>
<div class="hero" style="background:linear-gradient(135deg,#2D1A1A,#0D1B2A);border-bottom:3px solid #EF4444">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#9888;&#65039;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">Payment Failed</h1>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">We couldn't process payment for order #<?php echo $order_num; ?>.</p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <p style="color:#fff;font-size:17px;font-weight:600;margin-bottom:16px;">Hi <?php echo $name; ?>,</p>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Unfortunately, the <strong style="color:#fff;">payment for your order failed</strong>. Your course access has not been activated. Please try again with a different payment method or contact your bank.</p>
  <div style="background:<?php echo $dark; ?>;border-radius:14px;padding:20px;margin:20px 0;border:1px solid rgba(255,255,255,.08);">
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Order #</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $order_num; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Total</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $total; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Status</span><span class="val" style="color:#EF4444">&#9888; Payment Failed</span></div>
  </div>
  <div style="text-align:center;margin:28px 0;">
    <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;">Try Again</a>
  </div>
  <div style="background:linear-gradient(135deg,rgba(200,16,46,.15),rgba(200,16,46,.05));border-left:4px solid <?php echo $accent; ?>;border-radius:0 12px 12px 0;padding:16px 18px;margin:18px 0;">
    <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">&#128222; Need help? Contact us at <a href="mailto:office@rima-academy.com" style="color:#C8102E">office@rima-academy.com</a> or WhatsApp <a href="https://wa.me/40736852666" style="color:#25D366">+40 736 852 666</a>.</p>
  </div>
</div>
<?php
        $inner = ob_get_clean();
        rima_send_email( $email, "Payment Failed — Order #{$order_num}", $inner, "Payment failed for order #{$order_num}. Please try again." );

        // Admin failed alert
        ob_start(); ?>
<div class="hero" style="background:linear-gradient(135deg,#2D1A1A,#0D1B2A);border-bottom:3px solid #EF4444">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#9888;&#65039;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">Payment Failed</h1>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Order #<?php echo $order_num; ?> payment could not be processed.</p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <div class="badge" style="background:rgba(239,68,68,.2);border-color:#EF4444;color:#EF4444">Failed Payment</div>
  <div style="background:<?php echo $dark; ?>;border-radius:14px;padding:20px;margin:20px 0;border:1px solid rgba(255,255,255,.08);">
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Customer</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $name; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Email</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo esc_html( $email ); ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Order #</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $order_num; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Total</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $total; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Method</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $method; ?></span></div>
  </div>
  <div style="text-align:center;margin:28px 0;">
    <a href="<?php echo $admin_url; ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;">View Order</a>
  </div>
</div>
<?php
        $admin_inner = ob_get_clean();
        rima_send_email( RIMA_EMAIL_ADMIN, "&#9888; Payment Failed — Order #{$order_num} ({$name})", $admin_inner, "Payment failed for order #{$order_num} from {$name}." );
    }

    // ── E. ORDER REFUNDED ──────────────────────────────────────────
    if ( $new_status === 'refunded' ) {
        ob_start(); ?>
<div class="hero" style="background:linear-gradient(135deg,#0d3a2a,#0D1B2A);border-bottom:3px solid #22c55e">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#128176;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">Refund Processed</h1>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Your refund for order #<?php echo $order_num; ?> has been issued.</p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <p style="color:#fff;font-size:17px;font-weight:600;margin-bottom:16px;">Hi <?php echo $name; ?>,</p>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Your refund has been <strong style="color:#fff;">successfully processed</strong>. Depending on your bank or payment provider, funds may take 3-5 business days to appear.</p>
  <div style="background:<?php echo $dark; ?>;border-radius:14px;padding:20px;margin:20px 0;border:1px solid rgba(255,255,255,.08);">
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Order #</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $order_num; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Refund Amount</span><span class="val" style="color:#22c55e;font-weight:700"><?php echo $total; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Method</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $method; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Status</span><span class="val" style="color:#22c55e">&#10003; Refunded</span></div>
  </div>
  <div style="background:linear-gradient(135deg,rgba(200,16,46,.15),rgba(200,16,46,.05));border-left:4px solid <?php echo $accent; ?>;border-radius:0 12px 12px 0;padding:16px 18px;margin:18px 0;">
    <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">&#128336; <strong style="color:#fff;">Refund timeline:</strong> Card payments: 3-5 business days · Bank transfers: up to 7 business days. Contact us if you don't receive it: <a href="mailto:office@rima-academy.com" style="color:#C8102E">office@rima-academy.com</a>.</p>
  </div>
</div>
<?php
        $inner = ob_get_clean();
        rima_send_email( $email, "Refund Confirmed — Order #{$order_num}", $inner, "Your refund for order #{$order_num} has been processed." );

        // Admin refund alert
        ob_start(); ?>
<div class="hero" style="background:linear-gradient(135deg,#0d3a2a,#0D1B2A);border-bottom:3px solid #22c55e">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#128176;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">Refund Issued</h1>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Order #<?php echo $order_num; ?> has been refunded.</p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <div class="badge" style="background:rgba(34,197,94,.2);border-color:#22c55e;color:#22c55e">Refunded</div>
  <div style="background:<?php echo $dark; ?>;border-radius:14px;padding:20px;margin:20px 0;border:1px solid rgba(255,255,255,.08);">
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Customer</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $name; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Email</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo esc_html( $email ); ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Order #</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $order_num; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Amount</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $total; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Date</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $date; ?></span></div>
  </div>
  <div style="text-align:center;margin:28px 0;">
    <a href="<?php echo $admin_url; ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;">View in Dashboard</a>
  </div>
</div>
<?php
        $admin_inner = ob_get_clean();
        rima_send_email( RIMA_EMAIL_ADMIN, "&#128176; Refund Issued — Order #{$order_num} ({$name})", $admin_inner, "Refund issued for order #{$order_num} to {$name}." );
    }

    // ── F. ORDER COMPLETED ─────────────────────────────────────────
    // (enrollment handling also fires for 'completed' via section 2,
    //  so here we send the receipt/completion summary)
    if ( $new_status === 'completed' && $old_status !== 'completed' ) {
        ob_start(); ?>
<div style="background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);padding:32px 40px;text-align:center;">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#127873;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">Order Complete!</h1>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Your order #<?php echo $order_num; ?> has been fulfilled. Enjoy your courses!</p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <p style="color:#fff;font-size:17px;font-weight:600;margin-bottom:16px;">Hi <?php echo $name; ?>,</p>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Your order is now <strong style="color:#fff;">complete</strong>! All items have been delivered and your course access is fully active. Here's your order summary:</p>
  <div style="background:<?php echo $dark; ?>;border-radius:14px;padding:20px;margin:20px 0;border:1px solid rgba(255,255,255,.08);">
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Order #</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $order_num; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Date</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $date; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Payment</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $method; ?></span></div>
  </div>
  <p style="margin-top:16px;font-weight:600;color:#fff">Items:</p>
  <div class="info-card" style="margin-top:8px"><?php echo $items_html; ?></div>
  <div class="info-card" style="margin-top:12px">
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Total Paid</span><span class="val" style="color:#22c55e;font-size:18px;font-weight:700"><?php echo $total; ?></span></div>
  </div>
  <div style="text-align:center;margin:28px 0;">
    <a href="<?php echo esc_url( RIMA_SITE_URL . '/my-account/' ); ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;">Go to My Courses</a>
  </div>
  <div style="background:linear-gradient(135deg,rgba(200,16,46,.15),rgba(200,16,46,.05));border-left:4px solid <?php echo $accent; ?>;border-radius:0 12px 12px 0;padding:16px 18px;margin:18px 0;">
    <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">&#11088; <strong style="color:#fff;">Enjoying Rima Academy?</strong> Leave us a review or recommend us to a friend &mdash; your support helps us grow!</p>
  </div>
</div>
<?php
        $inner = ob_get_clean();
        rima_send_email( $email, "Order #{$order_num} Complete — Thank You!", $inner, "Order #{$order_num} is complete. Enjoy your courses!" );
    }
}

/* ============================================================
   7. SUPPRESS WOOCOMMERCE DEFAULT EMAILS
   (replace them with our branded ones above)
   ============================================================ */

add_action( 'woocommerce_email', 'rima_disable_default_wc_emails' );

function rima_disable_default_wc_emails() {
    // Array of default email IDs to disable (note: these are the IDs, not the class names)
    $email_ids_to_disable = array(
        'new_order',
        'cancelled_order',
        'failed_order',
        'customer_on_hold_order',
        'customer_processing_order',
        'customer_completed_order',
        'customer_refunded_order',
        'customer_invoice',
        'customer_note',
        'customer_reset_password',
        'customer_new_account',
    );

    foreach ( $email_ids_to_disable as $email_id ) {
        add_filter( 'woocommerce_email_enabled_' . $email_id, '__return_false' );
    }
}

/* ============================================================
   8. ADMIN TEST ENDPOINT
   Visit: /wp-admin/?rima_test_email=TYPE
   Types: welcome | reset | enrollment | pending | cancelled | failed | refunded | completed | onhold
   ============================================================ */

add_action( 'admin_init', function() {
    if ( ! current_user_can( 'manage_options' ) ) return;
    $type = isset( $_GET['rima_test_email'] ) ? sanitize_text_field( $_GET['rima_test_email'] ) : '';
    if ( ! $type ) return;

    // Helper: get latest order for testing
    $get_order = function() {
        $orders = wc_get_orders( array( 'limit' => 1, 'orderby' => 'date', 'order' => 'DESC' ) );
        return ! empty( $orders ) ? $orders[0] : null;
    };

    switch ( $type ) {
        case 'welcome':
            rima_on_user_register( get_current_user_id() );
            wp_die( '<p style="font-family:sans-serif;padding:20px">&#9989; Test "welcome" emails sent! Check admin + your inbox.</p>' );

        case 'reset':
            $user = wp_get_current_user();
            $key  = get_password_reset_key( $user );
            if ( is_wp_error( $key ) ) wp_die( 'Could not generate reset key.' );
            $html = rima_custom_password_reset_email( '', $key, $user->user_login, $user );
            wp_mail( $user->user_email, 'Password Reset — Rima Academy', $html, array( 'Content-Type: text/html; charset=UTF-8' ) );
            wp_die( '<p style="font-family:sans-serif;padding:20px">&#9989; Test "reset" email sent to ' . esc_html( $user->user_email ) . '</p>' );

        case 'enrollment':
        case 'completed':
            $o = $get_order();
            if ( $o ) { rima_on_order_status_change( $o->get_id(), 'processing', 'completed', $o ); rima_order_lifecycle_emails( $o->get_id(), 'processing', 'completed', $o ); wp_die( '<p style="font-family:sans-serif;padding:20px">&#9989; Test "completed" emails sent!</p>' ); }
            wp_die( 'No orders found.' );

        case 'pending':
            $o = $get_order();
            if ( $o ) { rima_order_lifecycle_emails( $o->get_id(), 'cart', 'pending', $o ); wp_die( '<p style="font-family:sans-serif;padding:20px">&#9989; Test "pending" email sent!</p>' ); }
            wp_die( 'No orders found.' );

        case 'cancelled':
            $o = $get_order();
            if ( $o ) { rima_order_lifecycle_emails( $o->get_id(), 'processing', 'cancelled', $o ); wp_die( '<p style="font-family:sans-serif;padding:20px">&#9989; Test "cancelled" emails sent!</p>' ); }
            wp_die( 'No orders found.' );

        case 'failed':
            $o = $get_order();
            if ( $o ) { rima_order_lifecycle_emails( $o->get_id(), 'pending', 'failed', $o ); wp_die( '<p style="font-family:sans-serif;padding:20px">&#9989; Test "failed" emails sent!</p>' ); }
            wp_die( 'No orders found.' );

        case 'refunded':
            $o = $get_order();
            if ( $o ) { rima_order_lifecycle_emails( $o->get_id(), 'completed', 'refunded', $o ); wp_die( '<p style="font-family:sans-serif;padding:20px">&#9989; Test "refunded" emails sent!</p>' ); }
            wp_die( 'No orders found.' );

        case 'onhold':
            $o = $get_order();
            if ( $o ) { rima_order_lifecycle_emails( $o->get_id(), 'pending', 'on-hold', $o ); wp_die( '<p style="font-family:sans-serif;padding:20px">&#9989; Test "on-hold" emails sent!</p>' ); }
            wp_die( 'No orders found.' );

        default:
            wp_die( '<p style="font-family:sans-serif;padding:20px">Unknown type. Use:<br>welcome | reset | enrollment | completed | pending | cancelled | failed | refunded | onhold | bacs | coursecomplete | expiry | livesession | manualenroll | quizpass | quizfail | pwdchanged | inactive</p>' );
    }
} );


/* ============================================================
   9. BACS — SPECIFIC: UPLOAD PAYMENT PROOF INSTRUCTION
   Fires when order goes ON-HOLD with BACS payment method.
   ============================================================ */

add_action( 'woocommerce_order_status_changed', 'rima_bacs_payment_proof_email', 15, 4 );

function rima_bacs_payment_proof_email( $order_id, $old_status, $new_status, $order ) {
    // Only for BACS (bank transfer) going on-hold
    if ( $new_status !== 'on-hold' ) return;
    if ( $order->get_payment_method() !== 'bacs' ) return;

    $name       = rima_email_order_name( $order );
    $email      = $order->get_billing_email();
    $order_num  = $order->get_order_number();
    $total      = $order->get_formatted_order_total();
    $my_acc_url = esc_url( RIMA_SITE_URL . '/my-account/' );
    $order_url  = esc_url( $order->get_view_order_url() );

    // Get BACS bank details from WooCommerce settings
    $bacs_accounts = get_option( 'woocommerce_bacs_accounts', array() );
    $bank_html = '';
    if ( ! empty( $bacs_accounts ) ) {
        $acc = $bacs_accounts[0];
        $bank_name    = ! empty( $acc['bank_name'] )    ? esc_html( $acc['bank_name'] )    : '';
        $account_name = ! empty( $acc['account_name'] ) ? esc_html( $acc['account_name'] ) : '';
        $account_num  = ! empty( $acc['account_number'] ) ? esc_html( $acc['account_number'] ) : '';
        $sort_code    = ! empty( $acc['sort_code'] )    ? esc_html( $acc['sort_code'] )    : '';
        $iban         = ! empty( $acc['iban'] )         ? esc_html( $acc['iban'] )         : '';
        $bic          = ! empty( $acc['bic'] )          ? esc_html( $acc['bic'] )          : '';

        if ( $bank_name )    $bank_html .= '<div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Bank</span><span style="color:#fff;font-size:14px;font-weight:500;">' . $bank_name . '</span></div>';
        if ( $account_name ) $bank_html .= '<div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Account Name</span><span style="color:#fff;font-size:14px;font-weight:500;">' . $account_name . '</span></div>';
        if ( $account_num )  $bank_html .= '<div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Account No.</span><span style="color:#fff;font-size:14px;font-weight:500;">' . $account_num . '</span></div>';
        if ( $sort_code )    $bank_html .= '<div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Sort Code</span><span style="color:#fff;font-size:14px;font-weight:500;">' . $sort_code . '</span></div>';
        if ( $iban )         $bank_html .= '<div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">IBAN</span><span style="color:#fff;font-size:14px;font-weight:500;">' . $iban . '</span></div>';
        if ( $bic )          $bank_html .= '<div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">BIC / SWIFT</span><span style="color:#fff;font-size:14px;font-weight:500;">' . $bic . '</span></div>';
    }

    ob_start(); ?>
<div class="hero" style="background:linear-gradient(135deg,#1A3A5C,#0D1B2A);border-bottom:3px solid #3B82F6">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#127981;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">Complete Your Bank Transfer</h1>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Order #<?php echo $order_num; ?> is awaiting your payment proof.</p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <p style="color:#fff;font-size:17px;font-weight:600;margin-bottom:16px;">Hi <?php echo $name; ?>,</p>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Thank you for your order! You selected <strong style="color:#fff;">Bank Transfer (BACS)</strong> as your payment method. To activate your course access, please complete the following 2 steps:</p>

  <div style="margin:24px 0;">
    <div style="display:flex;align-items:flex-start;margin-bottom:18px;"><div style="background:<?php echo $accent; ?>;color:#fff;font-size:13px;font-weight:700;border-radius:50%;width:28px;height:28px;line-height:28px;text-align:center;flex-shrink:0;margin-right:14px;margin-top:2px;">1</div><div style="color:rgba(255,255,255,.8);font-size:14px;line-height:1.6;"><strong style="color:#fff;">Make the bank transfer</strong> using the details below. Use your order number <strong style="color:#fff;">#<?php echo $order_num; ?></strong> as the payment reference.</div></div>
    <div style="display:flex;align-items:flex-start;margin-bottom:18px;"><div style="background:<?php echo $accent; ?>;color:#fff;font-size:13px;font-weight:700;border-radius:50%;width:28px;height:28px;line-height:28px;text-align:center;flex-shrink:0;margin-right:14px;margin-top:2px;">2</div><div style="color:rgba(255,255,255,.8);font-size:14px;line-height:1.6;"><strong style="color:#fff;">Upload your payment proof</strong> (screenshot or PDF of transfer confirmation) in your My Account area so we can verify it faster.</div></div>
  </div>

  <?php if ( $bank_html ) : ?>
  <p style="font-weight:600;color:#fff;margin-top:20px">&#127970; Bank Transfer Details:</p>
  <div class="info-card" style="margin-top:8px">
    <?php echo $bank_html; ?>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Reference</span><span class="val" style="color:#22c55e;font-weight:700">#<?php echo $order_num; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Amount</span><span class="val" style="color:#22c55e;font-size:16px;font-weight:700"><?php echo $total; ?></span></div>
  </div>
  <?php endif; ?>

  <div style="text-align:center;margin:28px 0;">
    <a href="<?php echo $my_acc_url; ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;">&#128196; Upload Payment Proof</a>
  </div>

  <div style="background:linear-gradient(135deg,rgba(200,16,46,.15),rgba(200,16,46,.05));border-left:4px solid <?php echo $accent; ?>;border-radius:0 12px 12px 0;padding:16px 18px;margin:18px 0;">
    <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">&#9200;&#65039; <strong style="color:#fff;">Important:</strong> Your course access will be activated within <strong style="color:#fff;">1 business day</strong> after we verify your payment. Without the proof upload, verification may take 2-3 days.</p>
  </div>

  <div class="cta-wrap" style="margin-top:10px">
    <a href="<?php echo $order_url; ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;" style="background:linear-gradient(135deg,#1A2E45,#0D1B2A);box-shadow:none;border:1px solid rgba(255,255,255,.15)">View My Order</a>
  </div>

  <p style="text-align:center;color:rgba(255,255,255,.4);font-size:13px;margin-top:16px">Questions? <a href="https://wa.me/40736852666" style="color:#25D366">WhatsApp us</a> or email <a href="mailto:office@rima-academy.com" style="color:#C8102E">office@rima-academy.com</a></p>
</div>
<?php
    $inner = ob_get_clean();
    rima_send_email(
        $email,
        "Action Required: Complete Bank Transfer for Order #" . $order_num,
        $inner,
        "Upload your payment proof to activate your course access."
    );
}

/**
 * When admin approves BACS order (on-hold → processing) — notify user.
 */
add_action( 'woocommerce_order_status_on-hold_to_processing', 'rima_bacs_approved_email', 10, 2 );
function rima_bacs_approved_email( $order_id, $order ) {
    if ( $order->get_payment_method() !== 'bacs' ) return;

    $name      = rima_email_order_name( $order );
    $email     = $order->get_billing_email();
    $order_num = $order->get_order_number();
    $total     = $order->get_formatted_order_total();
    $my_acc    = esc_url( RIMA_SITE_URL . '/my-account/' );

    ob_start(); ?>
<div class="hero" style="background:linear-gradient(135deg,#0d3a2a,#0D1B2A);border-bottom:3px solid #22c55e">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#9989;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">Payment Verified!</h1>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Your bank transfer for order #<?php echo $order_num; ?> has been confirmed.</p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <p style="color:#fff;font-size:17px;font-weight:600;margin-bottom:16px;">Hi <?php echo $name; ?>,</p>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Great news! We have <strong style="color:#fff;">verified your bank transfer payment</strong> and your course access is now <strong style="color:#fff;">fully active</strong>. You can start learning immediately!</p>
  <div style="background:<?php echo $dark; ?>;border-radius:14px;padding:20px;margin:20px 0;border:1px solid rgba(255,255,255,.08);">
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Order #</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $order_num; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Amount</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $total; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Status</span><span class="val" style="color:#22c55e">&#10004; Payment Verified</span></div>
  </div>
  <div style="text-align:center;margin:28px 0;">
    <a href="<?php echo $my_acc; ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;">&#127891; Start Learning Now</a>
  </div>
</div>
<?php
    $inner = ob_get_clean();
    rima_send_email( $email, "Payment Verified — Your Course Access is Active!", $inner, "Bank transfer verified for order #{$order_num}. Course access is now active!" );
}


/* ============================================================
   10. LMS — COURSE COMPLETION + CERTIFICATE READY
   ============================================================ */

// Academist LMS hook: fires when a student completes a course
add_action( 'academist_lms_course_completed', 'rima_on_course_completed', 10, 2 );
// Also hook standard WP action used by many LMS plugins
add_action( 'learndash_course_completed',     'rima_on_course_completed_ld', 10, 1 );

function rima_on_course_completed( $course_id, $user_id ) {
    $user        = get_userdata( $user_id );
    if ( ! $user ) return;
    $name        = esc_html( $user->display_name ?: $user->user_login );
    $email       = $user->user_email;
    $course_name = esc_html( get_the_title( $course_id ) );
    $cert_url    = esc_url( RIMA_SITE_URL . '/my-account/certificates/' );
    $my_acc      = esc_url( RIMA_SITE_URL . '/my-account/' );

    // User: course completion email
    ob_start(); ?>
<div class="hero" style="background:linear-gradient(135deg,#1A4A2E,#0D1B2A);border-bottom:3px solid #22c55e">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#127881;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">Course Completed!</h1>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Congratulations! You've finished <strong style="color:#fff;"><?php echo $course_name; ?></strong>.</p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <p style="color:#fff;font-size:17px;font-weight:600;margin-bottom:16px;">Congratulations, <?php echo $name; ?>! &#127942;</p>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">You've successfully <strong style="color:#fff;">completed the <?php echo $course_name; ?> course</strong>. This is a major achievement — you should be proud!</p>

  <div style="background:<?php echo $dark; ?>;border-radius:14px;padding:20px;margin:20px 0;border:1px solid rgba(255,255,255,.08);">
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Course</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $course_name; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Status</span><span class="val" style="color:#22c55e">&#127942; Completed</span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Certificate</span><span class="val" style="color:#22c55e">&#10004; Available</span></div>
  </div>

  <div style="text-align:center;margin:28px 0;">
    <a href="<?php echo $cert_url; ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;">&#127891; Download Certificate</a>
  </div>

  <div style="background:linear-gradient(135deg,rgba(200,16,46,.15),rgba(200,16,46,.05));border-left:4px solid <?php echo $accent; ?>;border-radius:0 12px 12px 0;padding:16px 18px;margin:18px 0;">
    <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">&#127775; <strong style="color:#fff;">What's next?</strong> Browse our other courses and keep building your language skills. Each new course brings you closer to fluency!</p>
  </div>

  <div class="cta-wrap" style="margin-top:10px">
    <a href="<?php echo esc_url( RIMA_SITE_URL . '/our-courses/' ); ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;" style="background:linear-gradient(135deg,#1A2E45,#0D1B2A);box-shadow:none;border:1px solid rgba(255,255,255,.15)">Explore More Courses</a>
  </div>
</div>
<?php
    $inner = ob_get_clean();
    rima_send_email( $email, "&#127942; You Completed {$course_name} — Certificate Ready!", $inner, "Congratulations! You completed {$course_name}. Download your certificate now." );

    // Admin: completion alert
    $admin_body = '<div class="hero" style="background:linear-gradient(135deg,#1A4A2E,#0D1B2A);border-bottom:3px solid #22c55e"><div style="font-size:48px;line-height:1;margin-bottom:12px;">&#127942;</div><h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">Course Completed</h1><p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">' . esc_html( $name ) . ' finished ' . $course_name . '.</p></div><div style="background:<?php echo $mid; ?>;padding:36px 40px;"><div class="badge" style="background:rgba(34,197,94,.2);border-color:#22c55e;color:#22c55e">LMS Event</div><div style="background:<?php echo $dark; ?>;border-radius:14px;padding:20px;margin:20px 0;border:1px solid rgba(255,255,255,.08);"><div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Student</span><span style="color:#fff;font-size:14px;font-weight:500;">' . esc_html( $name ) . '</span></div><div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Email</span><span style="color:#fff;font-size:14px;font-weight:500;">' . esc_html( $email ) . '</span></div><div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Course</span><span style="color:#fff;font-size:14px;font-weight:500;">' . $course_name . '</span></div></div></div>';
    rima_send_email( RIMA_EMAIL_ADMIN, "&#127942; Course Completed: {$name} — {$course_name}", $admin_body, "{$name} completed {$course_name}." );
}

// LearnDash / generic LMS adapter
function rima_on_course_completed_ld( $data ) {
    $course_id = isset( $data['course']->ID ) ? $data['course']->ID : 0;
    $user_id   = isset( $data['user']->ID )   ? $data['user']->ID   : 0;
    if ( $course_id && $user_id ) {
        rima_on_course_completed( $course_id, $user_id );
    }
}


/* ============================================================
   11. LMS — COURSE EXPIRY REMINDER (7 days before)
   Run via WP-Cron daily.
   ============================================================ */

// Schedule daily cron if not already scheduled
if ( ! wp_next_scheduled( 'rima_daily_expiry_check' ) ) {
    wp_schedule_event( time(), 'daily', 'rima_daily_expiry_check' );
}

add_action( 'rima_daily_expiry_check', 'rima_check_course_expiries' );

function rima_check_course_expiries() {
    // Check for WooCommerce Subscriptions or custom expiry meta
    // We look for users whose course access expires in exactly 7 days
    $target_date = date( 'Y-m-d', strtotime( '+7 days' ) );

    // Query users with expiry meta (adjust meta key to match your LMS)
    $expiry_meta_key = '_course_expiry_date'; // adjust if your LMS uses a different key
    $users = get_users( array(
        'meta_key'     => $expiry_meta_key,
        'meta_value'   => $target_date,
        'meta_compare' => '=',
    ) );

    foreach ( $users as $user ) {
        $name        = esc_html( $user->display_name ?: $user->user_login );
        $email       = $user->user_email;
        $course_name = esc_html( get_user_meta( $user->ID, '_course_expiry_name', true ) ?: 'your course' );
        $renew_url   = esc_url( RIMA_SITE_URL . '/our-courses/' );

        ob_start(); ?>
<div class="hero" style="background:linear-gradient(135deg,#3D2A0A,#0D1B2A);border-bottom:3px solid #F59E0B">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#9200;&#65039;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">Your Course Access Expires in 7 Days</h1>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;"><?php echo $course_name; ?> access ends on <?php echo esc_html( $target_date ); ?>.</p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <p style="color:#fff;font-size:17px;font-weight:600;margin-bottom:16px;">Hi <?php echo $name; ?>,</p>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Just a friendly reminder: your access to <strong style="color:#fff;"><?php echo $course_name; ?></strong> will expire in <strong style="color:#fff;">7 days</strong> (on <?php echo esc_html( $target_date ); ?>). Don't lose your progress!</p>
  <div style="background:linear-gradient(135deg,rgba(200,16,46,.15),rgba(200,16,46,.05));border-left:4px solid <?php echo $accent; ?>;border-radius:0 12px 12px 0;padding:16px 18px;margin:18px 0;">
    <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">&#128197; <strong style="color:#fff;">Renew now</strong> to keep your momentum going. Continuing students get priority access to new lessons and live sessions.</p>
  </div>
  <div style="text-align:center;margin:28px 0;">
    <a href="<?php echo $renew_url; ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;">&#128260; Renew My Access</a>
  </div>
  <div style="background:<?php echo $dark; ?>;border-radius:14px;padding:20px;margin:20px 0;border:1px solid rgba(255,255,255,.08);">
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Course</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $course_name; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Expires</span><span class="val" style="color:#F59E0B"><?php echo esc_html( $target_date ); ?></span></div>
  </div>
  <p style="text-align:center;color:rgba(255,255,255,.4);font-size:13px;margin-top:16px">Questions? <a href="mailto:office@rima-academy.com" style="color:#C8102E">office@rima-academy.com</a></p>
</div>
<?php
        $inner = ob_get_clean();
        rima_send_email( $email, "&#9200;&#65039; Your {$course_name} Access Expires in 7 Days", $inner, "Renew your access to {$course_name} before it expires in 7 days." );
    }
}


/* ============================================================
   12. LMS — LIVE SESSION / ZOOM REMINDER (24h before)
   Fires when a custom post type 'rima_session' is published
   or via manual trigger from admin.
   ============================================================ */

/**
 * Public function to send a live session reminder to a user.
 * Call this from your scheduling plugin or admin panel.
 *
 * @param int    $user_id      Student user ID.
 * @param string $session_name e.g. "English B2 — Zoom Session"
 * @param string $session_date e.g. "Thursday, 24 Jul 2026 at 18:00"
 * @param string $zoom_url     The Zoom meeting link.
 */
function rima_send_live_session_reminder( $user_id, $session_name, $session_date, $zoom_url ) {
    $user  = get_userdata( $user_id );
    if ( ! $user ) return;
    $name  = esc_html( $user->display_name ?: $user->user_login );
    $email = $user->user_email;
    $zoom  = esc_url( $zoom_url );

    ob_start(); ?>
<div class="hero" style="background:linear-gradient(135deg,#1A3A5C,#0D1B2A);border-bottom:3px solid #3B82F6">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#127909;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">Live Session Tomorrow!</h1>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;"><?php echo esc_html( $session_name ); ?></p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <p style="color:#fff;font-size:17px;font-weight:600;margin-bottom:16px;">Hi <?php echo $name; ?>,</p>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Your live session is <strong style="color:#fff;">tomorrow</strong>! Make sure you're ready — your native tutor will be waiting for you.</p>
  <div style="background:<?php echo $dark; ?>;border-radius:14px;padding:20px;margin:20px 0;border:1px solid rgba(255,255,255,.08);">
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Session</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo esc_html( $session_name ); ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Date &amp; Time</span><span class="val" style="color:#3B82F6"><?php echo esc_html( $session_date ); ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Platform</span><span style="color:#fff;font-size:14px;font-weight:500;">Zoom (online)</span></div>
  </div>
  <div style="text-align:center;margin:28px 0;">
    <a href="<?php echo $zoom; ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;" style="background:linear-gradient(135deg,#2563EB,#1D4ED8);box-shadow:0 8px 24px rgba(37,99,235,.4)">Join Zoom Session</a>
  </div>
  <div style="margin:24px 0;">
    <div style="display:flex;align-items:flex-start;margin-bottom:18px;"><div style="background:<?php echo $accent; ?>;color:#fff;font-size:13px;font-weight:700;border-radius:50%;width:28px;height:28px;line-height:28px;text-align:center;flex-shrink:0;margin-right:14px;margin-top:2px;">&#10003;</div><div style="color:rgba(255,255,255,.8);font-size:14px;line-height:1.6;">Test your microphone and camera <strong style="color:#fff;">15 minutes before</strong> the session.</div></div>
    <div style="display:flex;align-items:flex-start;margin-bottom:18px;"><div style="background:<?php echo $accent; ?>;color:#fff;font-size:13px;font-weight:700;border-radius:50%;width:28px;height:28px;line-height:28px;text-align:center;flex-shrink:0;margin-right:14px;margin-top:2px;">&#10003;</div><div style="color:rgba(255,255,255,.8);font-size:14px;line-height:1.6;">Have your study materials ready — vocabulary notes, exercises, etc.</div></div>
    <div style="display:flex;align-items:flex-start;margin-bottom:18px;"><div style="background:<?php echo $accent; ?>;color:#fff;font-size:13px;font-weight:700;border-radius:50%;width:28px;height:28px;line-height:28px;text-align:center;flex-shrink:0;margin-right:14px;margin-top:2px;">&#10003;</div><div style="color:rgba(255,255,255,.8);font-size:14px;line-height:1.6;">Find a <strong style="color:#fff;">quiet space</strong> with stable internet for the best experience.</div></div>
  </div>
  <div style="background:linear-gradient(135deg,rgba(200,16,46,.15),rgba(200,16,46,.05));border-left:4px solid <?php echo $accent; ?>;border-radius:0 12px 12px 0;padding:16px 18px;margin:18px 0;">
    <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">&#128222; Can't make it? Please let your tutor know at least <strong style="color:#fff;">2 hours before</strong> via WhatsApp: <a href="https://wa.me/40736852666" style="color:#25D366">+40 736 852 666</a>.</p>
  </div>
</div>
<?php
    $inner = ob_get_clean();
    rima_send_email( $email, "&#127909; Live Session Tomorrow: {$session_name}", $inner, "Your live session '{$session_name}' is tomorrow. Join via Zoom!" );
}


/* ============================================================
   13. LMS — ADMIN MANUALLY ENROLLS A STUDENT
   ============================================================ */

/**
 * Call this from admin or WP-CLI when manually granting course access.
 *
 * @param int $user_id
 * @param int $course_id
 */
function rima_send_manual_enrollment_email( $user_id, $course_id ) {
    $user        = get_userdata( $user_id );
    if ( ! $user ) return;
    $name        = esc_html( $user->display_name ?: $user->user_login );
    $email       = $user->user_email;
    $course_name = esc_html( get_the_title( $course_id ) );
    $my_acc      = esc_url( RIMA_SITE_URL . '/my-account/' );

    ob_start(); ?>
<div style="background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);padding:32px 40px;text-align:center;">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#127891;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">You've Been Enrolled!</h1>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Your tutor has granted you access to <strong style="color:#fff;"><?php echo $course_name; ?></strong>.</p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <p style="color:#fff;font-size:17px;font-weight:600;margin-bottom:16px;">Hi <?php echo $name; ?>,</p>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Rima Academy has <strong style="color:#fff;">manually enrolled you</strong> in <strong style="color:#fff;"><?php echo $course_name; ?></strong>. Your access is active immediately — no payment required.</p>
  <div style="background:<?php echo $dark; ?>;border-radius:14px;padding:20px;margin:20px 0;border:1px solid rgba(255,255,255,.08);">
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Course</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $course_name; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Access</span><span class="val" style="color:#22c55e">&#10004; Granted</span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Type</span><span style="color:#fff;font-size:14px;font-weight:500;">Manual Enrollment</span></div>
  </div>
  <div style="text-align:center;margin:28px 0;">
    <a href="<?php echo $my_acc; ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;">Go to My Courses</a>
  </div>
  <div style="background:linear-gradient(135deg,rgba(200,16,46,.15),rgba(200,16,46,.05));border-left:4px solid <?php echo $accent; ?>;border-radius:0 12px 12px 0;padding:16px 18px;margin:18px 0;">
    <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">&#128222; Questions about your enrollment? Contact us at <a href="mailto:office@rima-academy.com" style="color:#C8102E">office@rima-academy.com</a></p>
  </div>
</div>
<?php
    $inner = ob_get_clean();
    rima_send_email( $email, "You've Been Enrolled in {$course_name} — Rima Academy", $inner, "You now have access to {$course_name}. Start learning now!" );
}

// Hook: auto-fire when admin uses WordPress user course meta update
add_action( 'added_user_meta', 'rima_check_manual_enrollment', 10, 4 );
function rima_check_manual_enrollment( $meta_id, $user_id, $meta_key, $meta_value ) {
    // Adjust the meta key to match your LMS (Academist uses 'enrolled_courses' or similar)
    if ( $meta_key !== 'academist_enrolled_course' && $meta_key !== '_course_enrolled' ) return;
    $course_id = is_array( $meta_value ) ? ( $meta_value[0] ?? 0 ) : (int) $meta_value;
    if ( $course_id ) {
        rima_send_manual_enrollment_email( $user_id, $course_id );
    }
}


/* ============================================================
   14. LMS — QUIZ PASSED / FAILED
   ============================================================ */

// Hook for quiz completion (Academist LMS / generic)
add_action( 'academist_lms_quiz_completed', 'rima_on_quiz_result', 10, 3 );
add_action( 'learndash_quiz_completed',     'rima_on_quiz_result_ld', 10, 1 );

function rima_on_quiz_result( $quiz_id, $user_id, $result ) {
    $user       = get_userdata( $user_id );
    if ( ! $user ) return;
    $name       = esc_html( $user->display_name ?: $user->user_login );
    $email      = $user->user_email;
    $quiz_name  = esc_html( get_the_title( $quiz_id ) );
    $score      = isset( $result['score'] )  ? intval( $result['score'] )  : 0;
    $passing    = isset( $result['passing'] ) ? intval( $result['passing'] ) : 70;
    $passed     = $score >= $passing;
    $my_acc     = esc_url( RIMA_SITE_URL . '/my-account/' );

    if ( $passed ) {
        ob_start(); ?>
<div class="hero" style="background:linear-gradient(135deg,#1A4A2E,#0D1B2A);border-bottom:3px solid #22c55e">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#127942;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">Quiz Passed! <?php echo $score; ?>%</h1>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">You passed <strong style="color:#fff;"><?php echo $quiz_name; ?></strong> with a great score!</p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <p style="color:#fff;font-size:17px;font-weight:600;margin-bottom:16px;">Well done, <?php echo $name; ?>! &#127881;</p>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">You've <strong style="color:#fff;">passed the quiz</strong> successfully. Keep up the excellent work!</p>
  <div style="background:<?php echo $dark; ?>;border-radius:14px;padding:20px;margin:20px 0;border:1px solid rgba(255,255,255,.08);">
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Quiz</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $quiz_name; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Your Score</span><span class="val" style="color:#22c55e;font-weight:700"><?php echo $score; ?>%</span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Passing Score</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $passing; ?>%</span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Result</span><span class="val" style="color:#22c55e">&#10004; Passed</span></div>
  </div>
  <div style="text-align:center;margin:28px 0;"><a href="<?php echo $my_acc; ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;">Continue Learning</a></div>
</div>
<?php
        $inner = ob_get_clean();
        rima_send_email( $email, "&#127942; Quiz Passed: {$quiz_name} ({$score}%)", $inner, "You passed {$quiz_name} with {$score}%. Great work!" );
    } else {
        ob_start(); ?>
<div class="hero" style="background:linear-gradient(135deg,#3D2A0A,#0D1B2A);border-bottom:3px solid #F59E0B">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#128218;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">Quiz Attempt — Keep Going!</h1>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;"><?php echo $quiz_name; ?> — Score: <?php echo $score; ?>%</p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <p style="color:#fff;font-size:17px;font-weight:600;margin-bottom:16px;">Hi <?php echo $name; ?>,</p>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">You scored <strong style="color:#fff;"><?php echo $score; ?>%</strong> on <strong style="color:#fff;"><?php echo $quiz_name; ?></strong>. The passing score is <strong style="color:#fff;"><?php echo $passing; ?>%</strong>. Don't worry — review the material and try again, you're close!</p>
  <div style="background:<?php echo $dark; ?>;border-radius:14px;padding:20px;margin:20px 0;border:1px solid rgba(255,255,255,.08);">
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Quiz</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $quiz_name; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Your Score</span><span class="val" style="color:#F59E0B;font-weight:700"><?php echo $score; ?>%</span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Passing Score</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $passing; ?>%</span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Result</span><span class="val" style="color:#F59E0B">&#8987; Not yet</span></div>
  </div>
  <div style="margin:24px 0;">
    <div style="display:flex;align-items:flex-start;margin-bottom:18px;"><div style="background:<?php echo $accent; ?>;color:#fff;font-size:13px;font-weight:700;border-radius:50%;width:28px;height:28px;line-height:28px;text-align:center;flex-shrink:0;margin-right:14px;margin-top:2px;">1</div><div style="color:rgba(255,255,255,.8);font-size:14px;line-height:1.6;"><strong style="color:#fff;">Review</strong> the lesson material again, focusing on areas you found difficult.</div></div>
    <div style="display:flex;align-items:flex-start;margin-bottom:18px;"><div style="background:<?php echo $accent; ?>;color:#fff;font-size:13px;font-weight:700;border-radius:50%;width:28px;height:28px;line-height:28px;text-align:center;flex-shrink:0;margin-right:14px;margin-top:2px;">2</div><div style="color:rgba(255,255,255,.8);font-size:14px;line-height:1.6;"><strong style="color:#fff;">Retake the quiz</strong> — you got this!</div></div>
  </div>
  <div style="text-align:center;margin:28px 0;"><a href="<?php echo $my_acc; ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;" style="background:linear-gradient(135deg,#3D2A0A,#1A1400);box-shadow:none;border:1px solid #F59E0B">Try Again</a></div>
  <div style="background:linear-gradient(135deg,rgba(200,16,46,.15),rgba(200,16,46,.05));border-left:4px solid <?php echo $accent; ?>;border-radius:0 12px 12px 0;padding:16px 18px;margin:18px 0;">
    <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">&#128222; Need help? Talk to your tutor via <a href="https://wa.me/40736852666" style="color:#25D366">WhatsApp</a> or attend the next live session.</p>
  </div>
</div>
<?php
        $inner = ob_get_clean();
        rima_send_email( $email, "Quiz Attempt: {$quiz_name} — {$score}% (keep going!)", $inner, "You scored {$score}% on {$quiz_name}. Review and retry to pass." );
    }
}

function rima_on_quiz_result_ld( $data ) {
    $quiz_id = isset( $data['quiz']->ID ) ? $data['quiz']->ID : 0;
    $user_id = isset( $data['user']->ID ) ? $data['user']->ID : 0;
    $result  = array(
        'score'   => isset( $data['score'] )        ? $data['score']        : 0,
        'passing' => isset( $data['pass_mark'] )    ? $data['pass_mark']    : 70,
    );
    if ( $quiz_id && $user_id ) {
        rima_on_quiz_result( $quiz_id, $user_id, $result );
    }
}


/* ============================================================
   15. LMS — PASSWORD CHANGE CONFIRMATION
   ============================================================ */

add_action( 'password_reset', 'rima_on_password_changed', 10, 2 );
add_action( 'profile_update', 'rima_on_profile_password_changed', 10, 2 );

function rima_on_password_changed( $user, $new_pass ) {
    $name  = esc_html( $user->display_name ?: $user->user_login );
    $email = $user->user_email;
    $ip    = esc_html( sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? 'Unknown' ) );
    $time  = current_time( 'mysql' );

    ob_start(); ?>
<div class="hero" style="background:linear-gradient(135deg,#1A3A5C,#0D1B2A);border-bottom:3px solid #3B82F6">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#128273;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">Password Changed Successfully</h1>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Your Rima Academy account password was updated.</p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <p style="color:#fff;font-size:17px;font-weight:600;margin-bottom:16px;">Hi <?php echo $name; ?>,</p>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">Your password has been <strong style="color:#fff;">successfully changed</strong>. If you made this change, no action is needed.</p>
  <div style="background:<?php echo $dark; ?>;border-radius:14px;padding:20px;margin:20px 0;border:1px solid rgba(255,255,255,.08);">
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Account</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo esc_html( $email ); ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">Changed at</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $time; ?></span></div>
    <div style="display:flex;align-items:center;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.06);"><span style="color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;width:110px;flex-shrink:0;">IP Address</span><span style="color:#fff;font-size:14px;font-weight:500;"><?php echo $ip; ?></span></div>
  </div>
  <div style="background:linear-gradient(135deg,rgba(200,16,46,.15),rgba(200,16,46,.05));border-left:4px solid <?php echo $accent; ?>;border-radius:0 12px 12px 0;padding:16px 18px;margin:18px 0;">
    <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">&#9888;&#65039; <strong style="color:#fff;">Didn't change your password?</strong> Your account may be compromised. Contact us immediately at <a href="mailto:office@rima-academy.com" style="color:#C8102E">office@rima-academy.com</a> or WhatsApp <a href="https://wa.me/40736852666" style="color:#25D366">+40 736 852 666</a>.</p>
  </div>
</div>
<?php
    $inner = ob_get_clean();
    rima_send_email( $email, 'Your Rima Academy Password Was Changed', $inner, 'Your account password was just updated. If this wasn\'t you, contact us immediately.' );
}

function rima_on_profile_password_changed( $user_id, $old_user_data ) {
    $user     = get_userdata( $user_id );
    if ( ! $user ) return;
    // Only fire if password was actually changed
    if ( $user->user_pass === $old_user_data->user_pass ) return;
    rima_on_password_changed( $user, '' );
}


/* ============================================================
   16. LMS — STUDENT INACTIVITY REMINDER (30 days)
   Run via WP-Cron weekly.
   ============================================================ */

if ( ! wp_next_scheduled( 'rima_weekly_inactivity_check' ) ) {
    wp_schedule_event( time(), 'weekly', 'rima_weekly_inactivity_check' );
}

add_action( 'rima_weekly_inactivity_check', 'rima_check_inactive_students' );

function rima_check_inactive_students() {
    // Find users who haven't logged in for 30+ days but have enrolled courses
    $thirty_days_ago = date( 'Y-m-d H:i:s', strtotime( '-30 days' ) );

    $users = get_users( array(
        'meta_key'     => 'session_tokens',
        'meta_compare' => 'EXISTS',
        'number'       => 200,
    ) );

    foreach ( $users as $user ) {
        $last_login = get_user_meta( $user->ID, 'last_login', true );
        if ( ! $last_login || $last_login > $thirty_days_ago ) continue;

        // Only notify enrolled students
        $enrolled = get_user_meta( $user->ID, 'academist_enrolled_courses', true );
        if ( empty( $enrolled ) ) continue;

        // Avoid spamming — check if we already sent this in the last 30 days
        $last_sent = get_user_meta( $user->ID, '_rima_inactivity_email_sent', true );
        if ( $last_sent && strtotime( $last_sent ) > strtotime( '-30 days' ) ) continue;

        $name       = esc_html( $user->display_name ?: $user->user_login );
        $email      = $user->user_email;
        $my_acc     = esc_url( RIMA_SITE_URL . '/my-account/' );

        ob_start(); ?>
<div class="hero" style="background:linear-gradient(135deg,#2D1A3A,#0D1B2A);border-bottom:3px solid #8B5CF6">
  <div style="font-size:48px;line-height:1;margin-bottom:12px;">&#128522;</div>
  <h1 style="color:#fff;font-size:24px;font-weight:800;line-height:1.3;margin:0;">We Miss You, <?php echo $name; ?>!</h1>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">You haven't visited Rima Academy in over 30 days.</p>
</div>
<div style="background:<?php echo $mid; ?>;padding:36px 40px;">
  <p style="color:#fff;font-size:17px;font-weight:600;margin-bottom:16px;">Hi <?php echo $name; ?>,</p>
  <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">We noticed you haven't been active on Rima Academy for a while. Your courses are <strong style="color:#fff;">still waiting for you</strong> — don't let your progress fade!</p>
  <div style="background:linear-gradient(135deg,rgba(200,16,46,.15),rgba(200,16,46,.05));border-left:4px solid <?php echo $accent; ?>;border-radius:0 12px 12px 0;padding:16px 18px;margin:18px 0;">
    <p style="color:rgba(255,255,255,.8);font-size:15px;line-height:1.7;margin-bottom:16px;">&#127919; <strong style="color:#fff;">Did you know?</strong> Even 15 minutes a day of language practice makes a huge difference. Jump back in — your native tutor is ready!</p>
  </div>
  <div style="text-align:center;margin:28px 0;">
    <a href="<?php echo $my_acc; ?>" style="display:inline-block;background:linear-gradient(135deg,<?php echo $accent; ?> 0%,#8B0A1E 100%);color:#fff!important;font-size:15px;font-weight:700;padding:15px 40px;border-radius:50px;letter-spacing:.5px;box-shadow:0 8px 24px rgba(200,16,46,.4);text-decoration:none;" style="background:linear-gradient(135deg,#7C3AED,#4C1D95)">Resume My Courses</a>
  </div>
  <div style="margin:24px 0;">
    <div style="display:flex;align-items:flex-start;margin-bottom:18px;"><div style="background:<?php echo $accent; ?>;color:#fff;font-size:13px;font-weight:700;border-radius:50%;width:28px;height:28px;line-height:28px;text-align:center;flex-shrink:0;margin-right:14px;margin-top:2px;">&#9889;</div><div style="color:rgba(255,255,255,.8);font-size:14px;line-height:1.6;"><strong style="color:#fff;">Pick up where you left off</strong> — your progress is saved.</div></div>
    <div style="display:flex;align-items:flex-start;margin-bottom:18px;"><div style="background:<?php echo $accent; ?>;color:#fff;font-size:13px;font-weight:700;border-radius:50%;width:28px;height:28px;line-height:28px;text-align:center;flex-shrink:0;margin-right:14px;margin-top:2px;">&#127909;</div><div style="color:rgba(255,255,255,.8);font-size:14px;line-height:1.6;"><strong style="color:#fff;">Book a live session</strong> with your tutor to get back on track fast.</div></div>
  </div>
  <p style="text-align:center;color:rgba(255,255,255,.4);font-size:13px;margin-top:16px">Not interested? <a href="<?php echo esc_url( RIMA_SITE_URL ); ?>" style="color:rgba(200,16,46,.6)">Unsubscribe from reminders</a></p>
</div>
<?php
        $inner = ob_get_clean();
        rima_send_email( $email, "We Miss You, {$name}! Your Courses are Waiting ἟0", $inner, "Jump back into your language courses — your tutor is ready!" );
        update_user_meta( $user->ID, '_rima_inactivity_email_sent', current_time( 'mysql' ) );
    }
}
