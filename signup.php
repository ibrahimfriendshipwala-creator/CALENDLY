<?php
require 'db.php';
session_start();
$errors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    if(!$name || !$email || !$password) $errors[] = 'All fields required.';
    if(empty($errors)){
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email=?');
        $stmt->execute([$email]);
        if($stmt->fetch()) $errors[] = 'Email already registered.';
    }
    if(empty($errors)){
        $hash = password_hash($password, PASSWORD_DEFAULT);
        // create a simple unique booking slug
        $slugBase = preg_replace('/[^a-z0-9]+/','-',strtolower($name));
        $slug = $slugBase . '-' . substr(md5(uniqid()),0,6);
        $stmt = $pdo->prepare('INSERT INTO users (name,email,password,booking_slug) VALUES (?,?,?,?)');
        $stmt->execute([$name,$email,$hash,$slug]);
        $id = $pdo->lastInsertId();
        $_SESSION['user_id'] = $id;
        // redirect using JS
        echo "<script>window.location='dashboard.php'</script>";
        exit;
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Sign up</title>
  <style>
    body{background:linear-gradient(180deg,#071029,#0f1724);color:#e6eef8;font-family:Inter;margin:0}
    .wrap{max-width:420px;margin:60px auto;padding:24px}
    .card{background:linear-gradient(180deg,rgba(255,255,255,0.03),rgba(255,255,255,0.01));padding:22px;border-radius:14px}
    input{width:100%;padding:12px;margin:8px 0;border-radius:8px;border:1px solid rgba(255,255,255,0.06);background:transparent;color:inherit}
    .btn{width:100%;padding:12px;border-radius:8px;border:0;margin-top:8px;background:linear-gradient(90deg,#7c3aed,#06b6d4);color:#051025;font-weight:700}
    .err{color:#ffb4b4}
    .small{font-size:13px;color:#bcd7ff}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <h2>Create your account</h2>
      <?php if($errors): ?>
        <div class="err"><?php echo implode('<br>', $errors) ?></div>
      <?php endif; ?>
      <form method="post">
        <input name="name" placeholder="Full name" required>
        <input name="email" type="email" placeholder="Email" required>
        <input name="password" type="password" placeholder="Password" required>
        <button class="btn">Sign up</button>
      </form>
      <div class="small" style="margin-top:10px">Already have an account? <a href="login.php">Log in</a></div>
    </div>
  </div>
</body>
</html>
