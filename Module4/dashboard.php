<?php
require_once "auth.php";
require_once "db.php";
$user = require_role("Safety_Staff");

// Fetch dropdown data
$vehiclesResult = $conn->query("SELECT vehicle_id, license_plate FROM vehicle ORDER BY license_plate");
$vehicles = $vehiclesResult->fetch_all(MYSQLI_ASSOC);

$areasResult = $conn->query("SELECT Area_id, Area_name FROM parking_area ORDER BY Area_name");
$areas = $areasResult->fetch_all(MYSQLI_ASSOC);

$violations = [
    ["id"=>1,"name"=>"Parking violation"],
    ["id"=>2,"name"=>"Regulation Non-compliance"],
    ["id"=>3,"name"=>"Accident Caused"]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard – Summon History</title>
  <link rel="stylesheet" href="style.css?v=2" />
</head>
<body class="<?php echo ($user["user_type"]==="Student")?"student":"staff"; ?>">

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
</div>

<div class="main-content">
  <h2>Summon History</h2>
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
  <a href="manage-summon.php" class="btn-back">Add New Summon</a>
</div>

<!-- Edit Modal -->
<div id="editModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:#fff; padding:20px; border:1px solid #ccc; z-index:999;">
  <h3>Edit Summon</h3>
  <label>Violation</label>
  <select id="editViolation"><?php foreach($violations as $v): ?>
    <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['name']) ?></option>
  <?php endforeach; ?></select>

  <label>Vehicle</label>
  <select id="editVehicle"><?php foreach($vehicles as $v): ?>
    <option value="<?= $v['vehicle_id'] ?>"><?= htmlspecialchars($v['license_plate']) ?></option>
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

// Load Summon History
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
          <button class="editBtn" 
            data-id="${item.summon_id}" 
            data-violation="${item.violation}" 
            data-vehicle="${item.vehicle}" 
            data-area="${item.area}" 
            data-datetime="${item.datetime}">Edit</button>
          <button class="deleteBtn" data-id="${item.summon_id}">Delete</button>
        </td>`;
      tbody.appendChild(row);
    });
  } catch(err){ alert("Network error: "+err.message); }
}

// Delete
tbody.addEventListener("click", async e=>{
  if(e.target.classList.contains("deleteBtn")){
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
  if(e.target.classList.contains("editBtn")){
    editSummonId = e.target.dataset.id;
    const btn = e.target;

    // Set dropdowns
    [...document.getElementById("editViolation").options].forEach(o=>{ o.selected = (o.text===btn.dataset.violation); });
    [...document.getElementById("editVehicle").options].forEach(o=>{ o.selected = (o.text===btn.dataset.vehicle); });
    [...document.getElementById("editArea").options].forEach(o=>{ o.selected = (o.text===btn.dataset.area); });

    // Set datetime
    const dt = new Date(btn.dataset.datetime);
    const local = dt.toISOString().slice(0,16);
    document.getElementById("editDatetime").value = local;

    document.getElementById("editModal").style.display="block";
  }
});

// Cancel edit
document.getElementById("cancelEdit").addEventListener("click", ()=>{
  document.getElementById("editModal").style.display="none";
});

// Save edit
document.getElementById("saveEdit").addEventListener("click", async ()=>{
  const violationId = document.getElementById("editViolation").value;
  const vehicleId = document.getElementById("editVehicle").value;
  const areaId = document.getElementById("editArea").value;
  const datetime = document.getElementById("editDatetime").value;

  if(!violationId || !vehicleId || !areaId || !datetime){ alert("All fields required"); return; }

  const datetime_mysql = datetime.replace("T"," ")+":00"; // MySQL format

  try{
    const res = await fetch("updateSummon.php",{
      method:"POST",
      headers:{"Content-Type":"application/json"},
      body: JSON.stringify({
        summon_id: editSummonId,
        violation_id: violationId,
        vehicle_id: vehicleId,
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

// Initial load
loadSummonHistory();
</script>
</body>
</html>
