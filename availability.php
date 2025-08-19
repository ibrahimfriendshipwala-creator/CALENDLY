<?php
require 'db.php';
session_start(); if(!isset($_SESSION['user_id'])){ echo "<script>window.location='login.php'</script>"; exit; }
$uid = $_SESSION['user_id'];
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  // Expect arrays day[], start[], end[], duration[]
  $days = $_POST['day'] ?? [];
  $starts = $_POST['start'] ?? [];
  $ends = $_POST['end'] ?? [];
  $durs = $_POST['duration'] ?? [];
  // Remove existing availabilities then insert new
  $pdo->prepare('DELETE FROM availabilities WHERE user_id=?')->execute([$uid]);
  $ins = $pdo->prepare('INSERT INTO availabilities (user_id,day_of_week,start_time,end_time,duration_minutes) VALUES (?,?,?,?,?)');
  for($i=0;$i<count($days);$i++){
    if(!$starts[$i] || !$ends[$i]) continue;
    $ins->execute([$uid,(int)$days[$i],$starts[$i],$ends[$i],(int)$durs[$i]]);
  }
  echo "<script>window.location='dashboard.php'</script>"; exit;
}
// fetch availabilities
$stmt = $pdo->prepare('SELECT * FROM availabilities WHERE user_id=?'); $stmt->execute([$uid]); $avails = $stmt->fetchAll();
?>
<!doctype html>
<html><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Set Availability</title>
<style>
  body{background:linear-gradient(180deg,#071029,#0f1724);color:#e6eef8;font-family:Inter;margin:0}
  .wrap{max-width:900px;margin:28px auto;padding:20px}
  .card{background:linear-gradient(180deg,rgba(255,255,255,0.03),rgba(255,255,255,0.01));padding:18px;border-radius:14px}
  .row{display:flex;gap:8px;margin-bottom:8px}
  select,input{padding:8px;border-radius:8px;border:1px solid rgba(255,255,255,0.04);background:transparent;color:inherit}
  .btn{padding:8px 10px;border-radius:8px;border:0;background:linear-gradient(90deg,#7c3aed,#06b6d4);cursor:pointer}
</style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <h3>Weekly availability</h3>
      <p class="small">Add the days and times you are available. Visitors will be able to pick slots within these windows.</p>
      <form method="post" id="availForm">
        <div id="rows">
          <?php if($avails): foreach($avails as $a): ?>
            <div class="row">
              <select name="day[]">
                <?php for($d=0;$d<7;$d++): $sel = $d==$a['day_of_week']? 'selected':''; ?>
                  <option value="<?php echo $d ?>" <?php echo $sel ?>><?php echo ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'][$d] ?></option>
                <?php endfor; ?>
              </select>
              <input type="time" name="start[]" value="<?php echo $a['start_time'] ?>">
              <input type="time" name="end[]" value="<?php echo $a['end_time'] ?>">
              <input type="number" name="duration[]" value="<?php echo $a['duration_minutes'] ?>" style="width:90px" min="10">
              <button type="button" onclick="this.parentNode.remove()" class="btn">Remove</button>
            </div>
          <?php endforeach; else: ?>
            <div class="row">
              <select name="day[]"><?php for($d=0;$d<7;$d++): ?><option value="<?php echo $d ?>"><?php echo ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'][$d] ?></option><?php endfor; ?></select>
              <input type="time" name="start[]">
              <input type="time" name="end[]">
              <input type="number" name="duration[]" value="30" style="width:90px" min="10">
              <button type="button" onclick="this.parentNode.remove()" class="btn">Remove</button>
            </div>
          <?php endif; ?>
        </div>
        <div style="margin-top:12px">
          <button type="button" class="btn" onclick="addRow()">Add row</button>
          <button type="submit" class="btn" style="margin-left:8px">Save availability</button>
        </div>
      </form>
    </div>
  </div>
<script>
  function addRow(){
    const container = document.getElementById('rows');
    const div = document.createElement('div'); div.className='row';
    div.innerHTML = `<select name="day[]">` +
      ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'].map((d,i)=>`<option value="${i}">${d}</option>`).join('') +
      `</select><input type="time" name="start[]"><input type="time" name="end[]"><input type="number" name="duration[]" value="30" style="width:90px" min="10"><button type="button" onclick="this.parentNode.remove()" class="btn">Remove</button>`;
    container.appendChild(div);
  }
</script>
</body>
</html>
