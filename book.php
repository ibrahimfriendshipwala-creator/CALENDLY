<?php
require 'db.php';
// public booking page: ?u=booking_slug
$slug = $_GET['u'] ?? '';
if(!$slug){ echo 'No booking link specified.'; exit; }
$stmt = $pdo->prepare('SELECT * FROM users WHERE booking_slug=?'); $stmt->execute([$slug]); $user = $stmt->fetch();
if(!$user){ echo 'Booking link not found.'; exit; }
// fetch availabilities
$stmt = $pdo->prepare('SELECT * FROM availabilities WHERE user_id=?'); $stmt->execute([$user['id']]); $avails = $stmt->fetchAll();
// fetch existing bookings for next 30 days
$stmt = $pdo->prepare('SELECT * FROM bookings WHERE user_id=? AND start_datetime >= NOW() ORDER BY start_datetime ASC'); $stmt->execute([$user['id']]); $booked = $stmt->fetchAll();
$bookedMap = [];
foreach($booked as $b) $bookedMap[substr($b['start_datetime'],0,16)] = true;
?>
<!doctype html>
<html><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Book with <?php echo htmlspecialchars($user['name']) ?></title>
<style>
  body{background:linear-gradient(180deg,#071029,#0f1724);color:#e6eef8;font-family:Inter;margin:0}
  .wrap{max-width:900px;margin:28px auto;padding:20px}
  .card{background:linear-gradient(180deg,rgba(255,255,255,0.03),rgba(255,255,255,0.01));padding:18px;border-radius:14px}
  .calendar{display:flex;gap:12px}
  .day{flex:1}
  .slot{padding:8px;border-radius:8px;margin:6px 0;border:1px solid rgba(255,255,255,0.04);cursor:pointer}
  .disabled{opacity:0.35;cursor:not-allowed}
  input,button{padding:10px;border-radius:8px;border:1px solid rgba(255,255,255,0.04);background:transparent;color:inherit}
  .btn{background:linear-gradient(90deg,#7c3aed,#06b6d4);border:0;color:#051025}
</style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <h2>Book a meeting with <?php echo htmlspecialchars($user['name']) ?></h2>
      <p class="small">Select a date and an available time slot. Times are shown in server local time.</p>
      <div id="picker"></div>
      <div style="margin-top:12px" id="bookingForm" class="card" style="display:none">
        <h3>Confirm booking</h3>
        <form method="post" action="create_booking.php">
          <input type="hidden" name="user_id" value="<?php echo $user['id'] ?>">
          <input type="hidden" name="start" id="startInput">
          <div style="margin-top:8px"><input name="visitor_name" placeholder="Your name" required></div>
          <div style="margin-top:8px"><input name="visitor_email" type="email" placeholder="Email" required></div>
          <div style="margin-top:8px"><button class="btn" type="submit">Confirm booking</button></div>
        </form>
      </div>
    </div>
  </div>

<script>
  // Build a simple 7-day picker based on availabilities fetched server-side
  const avails = <?php echo json_encode($avails) ?>;
  const bookedMap = <?php echo json_encode($bookedMap) ?>;
  const durationDefaults = {};
  avails.forEach(a=>{ durationDefaults[a.day_of_week]=a.duration_minutes; });

  function formatDate(d){ return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0'); }

  const picker = document.getElementById('picker');
  const today = new Date();
  for(let i=0;i<7;i++){
    const day = new Date(); day.setDate(today.getDate()+i);
    const dow = day.getDay();
    const container = document.createElement('div'); container.className='card';
    container.innerHTML = `<h4>${day.toDateString()}</h4>`;
    // find matching avail for this weekday
    const matches = avails.filter(a=>parseInt(a.day_of_week)===dow);
    if(matches.length===0){ container.innerHTML += '<div class="small">No availability</div>'; }
    matches.forEach(m=>{
      // generate slots
      const start = m.start_time.split(':'); const end = m.end_time.split(':');
      let st = new Date(day.getFullYear(), day.getMonth(), day.getDate(), parseInt(start[0]), parseInt(start[1]));
      const en = new Date(day.getFullYear(), day.getMonth(), day.getDate(), parseInt(end[0]), parseInt(end[1]));
      while(st.getTime() + m.duration_minutes*60000 <= en.getTime()){
        const sKey = st.getFullYear()+"-"+String(st.getMonth()+1).padStart(2,'0')+"-"+String(st.getDate()).padStart(2,'0')+" "+String(st.getHours()).padStart(2,'0')+":"+String(st.getMinutes()).padStart(2,'0');
        const div = document.createElement('div');
        div.className='slot';
        if(bookedMap[sKey]){ div.classList.add('disabled'); div.innerText = (st.getHours().toString().padStart(2,'0')+':'+st.getMinutes().toString().padStart(2,'0')) + ' — Booked'; }
        else{ div.innerText = (st.getHours().toString().padStart(2,'0')+':'+st.getMinutes().toString().padStart(2,'0')); div.onclick = ()=>selectSlot(sKey); }
        container.appendChild(div);
        st = new Date(st.getTime() + m.duration_minutes*60000);
      }
    });
    picker.appendChild(container);
  }
  function selectSlot(sKey){
    document.getElementById('bookingForm').style.display='block';
    document.getElementById('startInput').value = sKey+':00';
    window.scrollTo(0,document.body.scrollHeight);
  }
</script>
</body>
</html>
