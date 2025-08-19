<?php
require 'db.php';
session_start();
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $email = strtolower(trim($_POST['email'] ?? ''));
  $pw = $_POST['password'] ?? '';
  $stmt = $pdo->prepare('SELECT id,password FROM users WHERE email=?');
  $stmt->execute([$email]);
  $u = $stmt->fetch();
  if($u && password_verify($pw,$u['password'])){
    $_SESSION['user_id']=$u['id'];
    echo "<script>window.location='dashboard.php'</script>"; exit;
  } else $err = 'Invalid credentials.';
}
?>
<!doctype html>
<html><head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login</title>
  <style>
    body{background:linear-gradient(180deg,#071029,#0f1724);color:#e6eef8;font-family:Inter;margin:0}
    .wrap{max-width:420px;margin:60px auto;padding:24px}
    .card{background:linear-gradient(180deg,rgba(255,255,255,0.03),rgba(255,255,255,0.01));padding:22px;border-radius:14px}
    input{width:100%;padding:12px;margin:8px 0;border-radius:8px;border:1px solid rgba(255,255,255,0.06);background:transparent;color:inherit}
    .btn{width:100%;padding:12px;border-radius:8px;border:0;margin-top:8px;background:linear-gradient(90deg,#7c3aed,#06b6d4);color:#051025;font-weight:700}
    .err{color:#ffb4b4}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <h2>Log in</h2>
      <?php if($err): ?><div class="err"><?php echo $err ?></div><?php endif; ?>
      <form method="post">
        <input name="email" type="email" placeholder="Email" required>
        <input name="password" type="password" placeholder="Password" required>
        <button class="btn">Log in</button>
      </form>
      <div style="margin-top:8px">No account? <a href="signup.php">Sign up</a></div>
    </div>
  </div>
</body>
</html>
