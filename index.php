<?php
require 'db.php';
session_start();
$me = isset($_SESSION['user_id']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Calendly Clone — Home</title>
  <style>
    /* Impressive internal CSS: modern, clean, glass cards, gradients */
    :root{--bg:#0f1724;--card:#0b1220;--accent:#7c3aed}
    *{box-sizing:border-box;font-family:Inter,ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,'Helvetica Neue',Arial}
    body{margin:0;background:linear-gradient(180deg,#071029 0%,#0f1724 100%);color:#e6eef8}
    header{padding:28px 32px;display:flex;justify-content:space-between;align-items:center}
    .brand{display:flex;gap:12px;align-items:center}
    .logo{width:48px;height:48px;border-radius:10px;background:linear-gradient(135deg,var(--accent),#06b6d4);display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff}
    .container{max-width:1100px;margin:24px auto;padding:20px}
    .hero{display:grid;grid-template-columns:1fr 420px;gap:28px;align-items:center}
    .card{background:linear-gradient(180deg,rgba(255,255,255,0.03),rgba(255,255,255,0.01));padding:22px;border-radius:18px;box-shadow:0 8px 30px rgba(2,6,23,0.6);}
    h1{margin:0 0 12px;font-size:28px}
    p.lead{margin:0 0 18px;color:#bcd7ff}
    .cta{display:flex;gap:10px}
    .btn{padding:10px 14px;border-radius:10px;border:0;cursor:pointer;font-weight:600}
    .btn-primary{background:linear-gradient(90deg,var(--accent),#06b6d4);color:#051025}
    .btn-ghost{background:transparent;color:#d5e6ff;border:1px solid rgba(255,255,255,0.06)}
    .small{font-size:13px;color:#a7c2ff}
    .grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:18px}
    footer{padding:20px;text-align:center;color:#98b6ff}
    a{text-decoration:none;color:inherit}
  </style>
</head>
<body>
  <header class="container">
    <div class="brand">
      <div class="logo">C</div>
      <div>
        <div style="font-weight:700">Calendly Clone</div>
        <div class="small">Schedule meetings — simple & fast</div>
      </div>
    </div>
    <nav>
      <?php if($me): ?>
        <button class="btn btn-ghost" onclick="redirect('dashboard.php')">Dashboard</button>
        <button class="btn" style="margin-left:8px" onclick="redirect('logout.php')">Logout</button>
      <?php else: ?>
        <button class="btn btn-ghost" onclick="redirect('login.php')">Log in</button>
        <button class="btn btn-primary" onclick="redirect('signup.php')">Get Started</button>
      <?php endif; ?>
    </nav>
  </header>

  <main class="container">
    <section class="hero">
      <div class="card">
        <h1>Make scheduling effortless</h1>
        <p class="lead">Create a personal booking link, set your availability, and let people book meetings on your calendar — no back-and-forth.</p>
        <div class="cta">
          <button class="btn btn-primary" onclick="redirect('signup.php')">Create account</button>
          <button class="btn btn-ghost" onclick="location.href='#how'">How it works</button>
        </div>
        <div class="grid" id="how">
          <div class="card" style="padding:12px">Set your availability</div>
          <div class="card" style="padding:12px">Share booking link</div>
          <div class="card" style="padding:12px">Receive confirmations</div>
        </div>
      </div>

      <div class="card">
        <h3>Try demo booking link</h3>
        <p class="small">Open a sample booking page for a demo user.</p>
        <div style="margin-top:12px">
          <input id="demoSlug" placeholder="demo-user" style="width:100%;padding:10px;border-radius:8px;border:1px solid rgba(255,255,255,0.04);background:transparent;color:inherit">
          <button class="btn btn-primary" style="width:100%;margin-top:12px" onclick="openPublic()">Open booking page</button>
        </div>
      </div>
    </section>

    <section style="margin-top:18px">
      <div class="card">
        <h3>Features</h3>
        <ul>
          <li>Create booking links</li>
          <li>Set recurring weekly availability</li>
          <li>Visitors book by selecting a time slot</li>
          <li>Dashboard with manage/cancel/reschedule</li>
        </ul>
      </div>
    </section>
  </main>

  <footer>
    Built with ❤️ — Open-source style clone
  </footer>

  <script>
    function redirect(url){
      // JS based file redirection per your requirement
      window.location.href = url;
    }
    function openPublic(){
      const slug = document.getElementById('demoSlug').value || 'demo-user';
      window.open('book.php?u='+encodeURIComponent(slug),'_blank');
    }
  </script>
</body>
</html>
