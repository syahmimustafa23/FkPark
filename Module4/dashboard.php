<?php
require_once "auth.php";
require_once "db.php";
$user = require_role("Safety_Staff");

// Fetch areas
$areasResult = $conn->query("SELECT Area_id, Area_name FROM parking_area ORDER BY Area_name");
$areas = $areasResult->fetch_all(MYSQLI_ASSOC);

// Violation types
$violations = [
    1 => "Parking violation",
    2 => "Regulation Non-compliance",
    3 => "Accident Caused"
];

// Get violation counts for stats & chart
$violationCounts = [];
$sql = "SELECT violation_id, COUNT(*) as count FROM traffic_summon GROUP BY violation_id";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $violationCounts[$row['violation_id']] = $row['count'];
}

// Ensure all types exist
foreach ($violations as $id => $name) {
    if (!isset($violationCounts[$id])) $violationCounts[$id] = 0;
}
$totalViolations = array_sum($violationCounts);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard – Summon History</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; font-family: Arial, Helvetica, sans-serif; }
    body { background: #f4f6f9; }
    body.staff header { background-color: #fd7e14; padding: 30px; margin: 20px; }
    body.student header { background-color: #28a745; padding: 30px; margin: 20px; }
    .navbar1 { display:flex; justify-content:flex-end; }
    .navbar1 a { display:block; padding:0 20px; text-decoration:none; color:black; font-size:16px; }
    .navbar1 a:hover { background-color:black; color:white; }

    .sidebar { height:100%; width:200px; position:fixed; top:20px; left:20px; background-color:#fff; display:flex; flex-direction:column; }
    .logo { width:200px; height:auto; }
    .sidebar2 { padding:15px 20px; text-decoration:none; font-size:18px; color:black; display:block; }
    .sidebar2:hover { background-color:black; color:white; }
    .sidebar2.active { font-weight:bold; }

    .main-content { margin-left:260px; padding:40px; }

    label { display:block; margin-top:15px; font-weight:bold; }
    input, select { width:100%; padding:10px; margin-top:5px; border:1px solid #bbb; background:#f9fafb; }
    input:focus, select:focus { border-color:#fd7e14; outline:none; background:#fff; }

    .btn-row { margin-top:20px; }
    button { padding:10px 20px; border:none; cursor:pointer; font-size:14px; }
    button[type="reset"] { background:#fff; border:1px solid #999; }
    button[type="reset"]:hover { background:#e5e7eb; }
    button[type="button"] { background:#007bff; color:white; }
    button[type="button"]:hover { background:#0056b3; }

    .table-container {
      max-height: 400px;
      overflow-y: auto;
      overflow-x: auto;
      border:1px solid #ccc;
      background:white;
      margin-top:20px;
    }
    .table-container table {
      width:100%;
      border-collapse: collapse;
      min-width:600px;
    }
    .table-container th, .table-container td {
      padding:12px;
      border:1px solid #ccc;
      text-align:left;
    }
    .table-container th {
      position:sticky;
      top:0;
      background-color:#fd7e14;
      color:white;
      z-index:2;
    }
    .table-container tbody tr:nth-child(even) { background:#f7fffb; }

    .btn-back {
      display:inline-block; margin-top:20px; padding:10px 16px;
      background:#fd7e14; color:white; text-decoration:none; border-radius:4px;
    }
    .btn-back:hover { background:#0b7051; }

    .stats { display:flex; justify-content:space-between; margin-bottom:30px; }
    .stat { flex:1; text-align:center; padding:20px; margin:0 110px; background:#f9f9f9; border-radius:6px; border:1px solid #e0e0e0; box-shadow:0 1px 3px rgba(0,0,0,0.1); }
    .stat h3 { font-size:26px; color:#333; margin-bottom:5px; }
    .stat p { color:#555; font-size:16px; }

    .chart-container { margin-top:20px; }
    .bar { display:flex; align-items:center; margin-bottom:12px; background:#f9f9f9; border-radius:6px; padding:8px 12px; box-shadow:0 1px 2px rgba(0,0,0,0.1); }
    .bar-label { width:180px; font-weight:bold; color:#333; font-size:14px; }
    .bar-fill {
      height:32px; border-radius:4px; color:white; display:flex;
      align-items:center; justify-content:center; font-size:14px; font-weight:bold;
      transition: width 0.3s ease;
    }
    .bar-fill:nth-child(1) { background-color:#ffc107; }
    .bar-fill:nth-child(2) { background-color:#28a745; }
    .bar-fill:nth-child(3) { background-color:#dc3545; }

    /* New QR button styling */
    .action-btn { margin-right:5px; padding:6px 10px; font-size:13px; border:none; border-radius:4px; cursor:pointer; }
    .edit-btn { background:#007bff; color:white; }
    .delete-btn { background:#dc3545; color:white; }
    .qr-btn { background:#28a745; color:white; }
    .action-btn:hover { opacity:0.8; }
  </style>
</head>
<body class="staff">

<header>
  <div class="navbar1">
    <a href="../Module1/security_profile.php">Profile</a>
    <a href="../logout.php">Logout</a>
  </div>
</header>

<div class="sidebar">
  <a href="#"><img class="logo" src="umpsa-logo-png.png"></a>
  <a class="sidebar2" href="../Module2/security_view.php">View Parking</a>
  <a class="sidebar2" href="../Module1/security_list_vehicles.php">Vehicle Approval</a>
  <a class="sidebar2" href="manage-summon.php">Manage Traffic Summon</a>
  <a class="sidebar2" href="dashboard.php">Manage Dashboard</a>
  <a class="sidebar2" href="../dashboards/manage_report.php">Manage Report</a>
  <a class="sidebar2" href="view-status.php">View Update Point & Status</a>
</div>

<div class="main-content">
  <h2>Summon Dashboard</h2>

  <div class="stats">
    <?php foreach ($violations as $id => $name): ?>
      <div class="stat">
        <h3><?= $violationCounts[$id] ?></h3>
        <p><?= htmlspecialchars($name) ?></p>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="chart-container">
    <h3>Violation Type Chart</h3>
    <?php foreach ($violations as $id => $name):
      $count = $violationCounts[$id];
      $percent = $totalViolations ? ($count / $totalViolations * 100) : 0;
    ?>
      <div class="bar">
        <div class="bar-label"><?= htmlspecialchars($name) ?></div>
        <div class="bar-fill" style="width: <?= $percent ?>%;"><?= $count ?> (<?= round($percent,2) ?>%)</div>
      </div>
    <?php endforeach; ?>
    <?php if ($totalViolations == 0): ?>
      <p>No violations recorded yet.</p>
    <?php endif; ?>
  </div>

  <div class="table-container">
    <table id="historyTable">
      <thead>
        <tr>
          <th>Violation type</th>
          <th>Vehicle Number</th>
          <th>Date & Time</th>
          <th>Area</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
  <a href="manage-summon.php" class="btn-back">Add New Summon</a>
</div>

<!-- Edit Modal -->
<div id="editModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:#fff; padding:20px; border:1px solid #ccc; z-index:999;">
  <h3>Edit Summon</h3>
  <label>Violation</label>
  <select id="editViolation"><?php foreach($violations as $id => $name): ?>
    <option value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
  <?php endforeach; ?></select>

  <label>Area</label>
  <select id="editArea"><?php foreach($areas as $a): ?>
    <option value="<?= $a['Area_id'] ?>"><?= htmlspecialchars($a['Area_name']) ?></option>
  <?php endforeach; ?></select>

  <label>Date & Time</label>
  <input type="datetime-local" id="editDatetime" />

  <div class="btn-row">
    <button id="cancelEdit">Cancel</button>
    <button id="saveEdit">Save</button>
  </div>
</div>

<script>
const tbody = document.querySelector("#historyTable tbody");
let editSummonId = 0;

async function loadSummonHistory() {
  try {
    const res = await fetch("getSummonHistory.php");
    const data = await res.json();
    if(!data.ok){ alert("Load failed: "+(data.error||"Unknown error")); return; }
    tbody.innerHTML="";
    data.rows.forEach(item=>{
      const row = document.createElement("tr");
      row.innerHTML=`
        <td>${item.violation}</td>
        <td>${item.vehicle}</td>
        <td>${item.datetime}</td>
        <td>${item.area}</td>
        <td>
          <button class="action-btn edit-btn" 
            data-id="${item.summon_id}" 
            data-violation="${item.violation}" 
            data-area="${item.area}" 
            data-datetime="${item.datetime}">Edit</button>
          <button class="action-btn delete-btn" data-id="${item.summon_id}">Delete</button>
          <a href="view-summon.php?id=${item.summon_id}" class="action-btn qr-btn">View QR</a>
        </td>`;
      tbody.appendChild(row);
    });
  } catch(err){ alert("Network error: "+err.message); }
}

// Delete
tbody.addEventListener("click", async e=>{
  if(e.target.classList.contains("delete-btn")){
    const id=e.target.dataset.id;
    if(!confirm("Are you sure?")) return;
    try{
      const res = await fetch("deleteSummon.php?summon_id="+id);
      const data = await res.json();
      if(data.ok){ alert("Deleted successfully"); loadSummonHistory(); }
      else{ alert("Delete failed: "+(data.error||"Unknown error")); }
    } catch(err){ alert("Network error: "+err.message); }
  }
});

// Edit
tbody.addEventListener("click", e=>{
  if(e.target.classList.contains("edit-btn")){
    editSummonId = e.target.dataset.id;
    const btn = e.target;
    [...document.getElementById("editViolation").options].forEach(o=>{ o.selected = (o.text===btn.dataset.violation); });
    [...document.getElementById("editArea").options].forEach(o=>{ o.selected = (o.text===btn.dataset.area); });
    const dt = new Date(btn.dataset.datetime);
    const local = dt.toISOString().slice(0,16);
    document.getElementById("editDatetime").value = local;
    document.getElementById("editModal").style.display="block";
  }
});

document.getElementById("cancelEdit").addEventListener("click", ()=>{
  document.getElementById("editModal").style.display="none";
});

document.getElementById("saveEdit").addEventListener("click", async ()=>{
  const violationId = document.getElementById("editViolation").value;
  const areaId = document.getElementById("editArea").value;
  const datetime = document.getElementById("editDatetime").value;
  if(!violationId || !areaId || !datetime){ alert("All fields required"); return; }
  const datetime_mysql = datetime.replace("T"," ")+":00";
  try{
    const res = await fetch("updateSummon.php",{
      method:"POST",
      headers:{"Content-Type":"application/json"},
      body: JSON.stringify({
        summon_id: editSummonId,
        violation_id: violationId,
        area_id: areaId,
        datetime: datetime_mysql
      })
    });
    const data = await res.json();
    if(data.ok){
      alert("Updated successfully");
      document.getElementById("editModal").style.display="none";
      loadSummonHistory();
    }else{
      alert("Update failed: "+(data.error||"Unknown error"));
    }
  }catch(err){ alert("Network error: "+err.message); }
});

loadSummonHistory();
</script>
</body>
</html>
