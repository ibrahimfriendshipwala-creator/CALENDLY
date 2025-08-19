<?php
require 'db.php';
session_start();
if($_SERVER['REQUEST_METHOD'] !== 'POST'){ echo 'Invalid request'; exit; }
$id = (int)($_POST['id'] ?? 0);
// Only allow owner to cancel
if(!isset($_SESSION['user_id'])){ echo 'Not authorized'; exit; }
$uid = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT * FROM bookings WHERE id=? AND user_id=?'); $stmt->execute([$id,$uid]); $b = $stmt->fetch();
if(!$b){ echo 'Booking not found'; exit; }
$pdo->prepare('UPDATE bookings SET status="cancelled" WHERE id=?')->execute([$id]);
echo "<script>alert('Booking cancelled'); window.location='dashboard.php';</script>";
exit;
?>
