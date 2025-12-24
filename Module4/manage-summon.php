<?php
require_once "auth.php";
require_once "db.php"; // include database connection
$user = require_role("Safety_Staff");

/* ROLE-BASED BODY CLASS */
$roleClass = ($user["user_type"] === "Student") ? "student" : "staff";

// Fetch only APPROVED vehicles from approval table
$vehiclesResult = $conn->query("
    SELECT v.vehicle_id, v.license_plate
    FROM vehicle v
    JOIN approval a ON v.vehicle_id = a.vehicle_id
    WHERE a.status = 'Approved'
    ORDER BY v.license_plate
");
$vehicles = $vehiclesResult->fetch_all(MYSQLI_ASSOC);

// Fetch parking areas
$areasResult = $conn->query("SELECT Area_id, Area_name FROM parking_area ORDER BY Area_name");
$areas = $areasResult->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Traffic Summon</title>
  <link rel="stylesheet" href="style.css?v=2" />
</head>

<body class="<?php echo $roleClass; ?>">
  <!-- HORIZONTAL HEADER -->
  <header>
    <div class="navbar1">
      <a href="../Module1/security_profile.php">Profile</a>
      <a href="../logout.php">Logout</a>
    </div>
  </header>

  <!-- SIDEBAR -->
  <div class="sidebar">
    <a href="#"><img class="logo" src="umpsa-logo-png.png"></a>
    <?php if ($user["user_type"] === "Safety_Staff"): ?>
      <a class="sidebar2" href="../Module2/security_view.php">View Parking</a>
      <a class="sidebar2" href="../Module1/security_list_vehicles.php">Vehicle Approval</a>
      <a class="sidebar2" href="manage-summon.php">Manage Traffic Summon</a>
      <a class="sidebar2" href="dashboard.php">Manage Dashboard</a>
      <a class="sidebar2" href="../dashboards/manage_report.php">Manage Report</a>
    <?php endif; ?>
    <a class="sidebar2 <?php echo basename($_SERVER["PHP_SELF"]) === "view-status.php" ? "active" : ""; ?>"
       href="view-status.php">
      View Update Point & Status
    </a>
  </div>

  <!-- MAIN CONTENT -->
  <div class="main-content">
    <h3>Manage Traffic Summon</h3>

    <label>Violation type</label>
    <select id="violationType">
      <option value="1">Parking violation</option>
      <option value="2">Regulation Non-compliance</option>
      <option value="3">Accident Caused</option>
    </select>

    <label>Vehicle number</label>
    <select id="vehicleNo">
      <?php if (empty($vehicles)): ?>
        <option value="">No approved vehicles</option>
      <?php else: ?>
        <?php foreach ($vehicles as $v): ?>
          <option value="<?= $v['vehicle_id'] ?>"><?= htmlspecialchars($v['license_plate']) ?></option>
        <?php endforeach; ?>
      <?php endif; ?>
    </select>

    <label>Date and time</label>
    <input id="Datetime_issued" type="datetime-local" />

    <label>Area of violation issued</label>
    <select id="area">
      <?php foreach ($areas as $a): ?>
        <option value="<?= $a['Area_id'] ?>"><?= htmlspecialchars($a['Area_name']) ?></option>
      <?php endforeach; ?>
    </select>

    <div class="btn-row">
      <button type="reset">Reset</button>
      <button type="button" id="submitBtn">Submit</button>
    </div>
  </div>

  <script>
    document.getElementById("submitBtn").addEventListener("click", () => {
      const violationType = document.getElementById("violationType").value;
      const vehicleId = document.getElementById("vehicleNo").value;
      const Datetime_issued = document.getElementById("Datetime_issued").value;
      const areaId = document.getElementById("area").value;

      if (!Datetime_issued || !vehicleId || !areaId) {
        alert("Please fill all fields.");
        return;
      }

      const record = {
        violation_id: parseInt(violationType, 10),
        vehicle_id: parseInt(vehicleId, 10),
        datetime_issued: Datetime_issued,
        area_id: parseInt(areaId, 10)
      };

      fetch("trafficSummon.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(record),
      })
      .then(res => res.json())
      .then(data => {
        if (!data.ok) {
          alert("Save failed: " + (data.error || "Unknown error"));
          return;
        }
        window.location.href = "dashboard.php";
      })
      .catch(err => alert("Network error: " + err.message));
    });
  </script>
</body>
</html>
