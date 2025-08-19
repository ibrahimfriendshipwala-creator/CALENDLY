<?php
require 'db.php';
session_start();
if(!isset($_SESSION['user_id'])){ echo "<script>window.location='login.php'</script>"; exit; }
$uid = $_SESSION['user_id'];
// fetch user
$stmt = $pdo->prepare('SELECT * FROM users WHERE id=?'); $stmt->execute([$uid]); $user = $stmt->fetch();
// fetch upcoming bookings
$stmt = $pdo->prepare('SELECT * FROM bookings WHERE user_id=? ORDER BY start_datetime ASC LIMIT 30'); $stmt->execute([$uid]); $bookings = $stmt->fetchAll();
// fetch availabilities
$stmt = $pdo->prepare('SELECT * FROM availabilities WHERE user_id=?'); $stmt->execute([$uid]); $avails = $stmt->fetchAll();
?>
<!doctype html>
<html><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard</title>
<style>
  body{background:linear-gradient(180deg,#071029,#0f1724);color:#e6eef8;font-family:Inter;margin:0}
  .wrap{max-width:1100px;margin:28px auto;padding:20px}
  .grid{display:grid;grid-template-columns:2fr 1fr;gap:16px}
  .card{background:linear-gradient(180deg,rgba(255,255,255,0.03),rgba(255,255,255,0.01));padding:18px;border-radius:14px}
  table{width:100%;border-collapse:collapse}
  td,th{padding:10px;border-bottom:1px solid rgba(255,255,255,0.03)}
  .btn{padding:8px 10px;border-radius:8px;border:0;background:linear-gradient(90deg,#7c3aed,#06b6d4);cursor:pointer}
</style>
</head>
<body>
  <div class="wrap">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
      <div>
        <h2>Welcome, <?php echo htmlspecialchars($user['name']) ?></h2>
        <div class="small">Your booking link: <a href="book.php?u=<?php echo urlencode($user['booking_slug']) ?>" target="_blank"><?php echo htmlspecialchars($user['booking_slug']) ?></a></div>
      </div>
      <div>
        <button class="btn" onclick="redirect('availability.php')">Set Availability</button>
        <button class="btn" style="margin-left:8px" onclick="redirect('index.php')">Home</button>
      </div>
    </div>

    <div class="grid">
      <div class="card">
        <h3>Upcoming bookings</h3>
        <?php if(!$bookings): ?>
          <div>No upcoming bookings</div>
        <?php else: ?>
          <table>
            <tr><th>Visitor</th><th>When</th><th>Status</th><th>Actions</th></tr>
            <?php foreach($bookings as $b): ?>
              <tr>
                <td><?php echo htmlspecialchars($b['visitor_name']) ?><br><small><?php echo htmlspecialchars($b['visitor_email']) ?></small></td>
                <td><?php echo $b['start_datetime'] ?> — <?php echo $b['end_datetime'] ?></td>
                <td><?php echo $b['status'] ?></td>
                <td>
                  <form style="display:inline" method="post" action="cancel_booking.php">
                    <input type="hidden" name="id" value="<?php echo $b['id'] ?>">
                    <button class="btn" type="submit">Cancel</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </table>
        <?php endif; ?>
      </div>

      <div class="card">
        <h3>Your availability</h3>
        <?php if(!$avails): ?>
          <div>No availability set. <br><button class="btn" onclick="redirect('availability.php')">Add availability</button></div>
        <?php else: ?>
          <ul>
            <?php foreach($avails as $a):
              $days=['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
            ?>
            <li><?php echo $days[$a['day_of_week']] ?>: <?php echo substr($a['start_time'],0,5) ?> - <?php echo substr($a['end_time'],0,5) ?> (<?php echo $a['duration_minutes'] ?> min)</li>
            <?php endforeach; ?>
          </ul>
          <button class="btn" onclick="redirect('availability.php')">Edit</button>
        <?php endif; ?>
      </div>
    </div>
  </div>

<script>
  function redirect(url){window.location.href=url;}
</script>
</body>
</html>
