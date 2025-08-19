<?php
require 'db.php';
// Simple booking handler. In production you'd perform more checks and timezone handling.
if($_SERVER['REQUEST_METHOD'] !== 'POST') { echo 'Invalid request'; exit; }
$user_id = (int)($_POST['user_id'] ?? 0);
$visitor_name = trim($_POST['visitor_name'] ?? '');
$visitor_email = trim($_POST['visitor_email'] ?? '');
$start = $_POST['start'] ?? '';
if(!$user_id || !$visitor_name || !$visitor_email || !$start){ echo 'Missing fields'; exit; }
// assume duration 30 minutes by default — get from availabilities
$stmt = $pdo->prepare('SELECT duration_minutes FROM availabilities WHERE user_id=? LIMIT 1'); $stmt->execute([$user_id]); $r = $stmt->fetch(); $dur = $r ? (int)$r['duration_minutes'] : 30;
$start_dt = date('Y-m-d H:i:s', strtotime($start));
$end_dt = date('Y-m-d H:i:s', strtotime($start) + $dur*60);
// check double booking
$stmt = $pdo->prepare('SELECT COUNT(*) as c FROM bookings WHERE user_id=? AND start_datetime=?'); $stmt->execute([$user_id,$start_dt]); $c = $stmt->fetchColumn();
if($c>0){ echo 'Slot already booked.'; exit; }
$ins = $pdo->prepare('INSERT INTO bookings (user_id,visitor_name,visitor_email,start_datetime,end_datetime) VALUES (?,?,?,?,?)');
$ins->execute([$user_id,$visitor_name,$visitor_email,$start_dt,$end_dt]);

// send a simple confirmation email (requires server mail configured)
try{
  $to = $visitor_email;
  $subject = 'Booking confirmation';
  $msg = "Hi $visitor_name,\n\nYour meeting is confirmed for $start_dt.\n\nThank you.";
  @mail($to,$subject,$msg);
} catch (Exception $e) {}

// JS redirect back to a simple confirmation
echo "<script>alert('Booking confirmed'); window.location='index.php';</script>";
exit;
?>
