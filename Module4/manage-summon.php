<?php
require_once "auth.php";
$user = require_role("Safety_Staff");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Manage Traffic Summon</title>
  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <!-- HORIZONTAL HEADER -->
  <header>
    <div class="navbar1">
      <a href="#">Homepage</a>
      <a href="#">Profile</a>
      <a href="dashboard.php">Dashboard</a>
      <a href="../logout.php">Logout</a>
    </div>
  </header>

  <!-- SIDEBAR -->
<div class="sidebar">
  <a href="#"><img class="logo" src="umpsa-logo-png.png"></a>

  <?php if ($user["user_type"] === "Safety_Staff"): ?>
    <a class="sidebar2" href="manage-summon.php">Manage Traffic Summon</a>
    <a class="sidebar2" href="dashboard.php">Manage Dashboard</a>
  <?php endif; ?>

  <?php if (in_array($user["user_type"], ["Safety_Staff", "Student"], true)): ?>
    <a class="sidebar2 <?php echo basename($_SERVER["PHP_SELF"]) === "view-status.php" ? "active" : ""; ?>"
       href="view-status.php">
      View Update Point & Status
    </a>
  <?php endif; ?>
</div>


  <!-- MAIN CONTENT -->
  <div class="main-content">
    <h3>Manage Traffic Summon</h3>

    <label>Violation type</label>
    <select id="violationType">
      <option value="1">Parking violation</option>
      <option value="2">Traffic regulation breach</option>
      <option value="3">Cause accident</option>
    </select>


    <label>Vehicle number</label>
    <input id="vehicleNo" type="text" placeholder="Enter vehicle number" />

    <label>Date and time</label>
    <input id="Datetime_issued" type="datetime-local" />

    <label>Area of violation issued</label>
    <input id="area" type="text" placeholder="Enter area" />

    <div class="btn-row">
      <button type="reset">Reset</button>
      <button type="button" id="submitBtn">Submit</button>
    </div>
  </div>

  <script>
    document.getElementById("submitBtn").addEventListener("click", () => {
      let violationType = document.getElementById("violationType").value;
      let vehicleNo = document.getElementById("vehicleNo").value.trim();
      let Datetime_issued = document.getElementById("Datetime_issued").value;
      let area = document.getElementById("area").value.trim();

      if (!vehicleNo || !Datetime_issued || !area) {
        alert("Please fill all fields.");
        return;
      }

      const record = {
        violation_id: parseInt(violationType, 10),
        license_plate: vehicleNo,
        datetime_issued: Datetime_issued,
        area_name: area
      };


      fetch("trafficSummon.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(record),
      })
        .then((res) => res.json())
        .then((data) => {
          if (!data.ok) {
            alert("Save failed: " + (data.error || "Unknown error"));
            return;
          }
          // optional: show QR link
          // alert("Summon created. QR: " + data.qr_url);
          window.location.href = "dashboard.php";
        })
        .catch((err) => {
          alert("Network error: " + err.message);
        });
    });
  </script>

</body>

</html>