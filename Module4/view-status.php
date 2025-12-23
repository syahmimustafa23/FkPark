<?php
require_once "auth.php";
$user = require_any_role(["Safety_Staff", "Student"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Update Point & Status</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- HORIZONTAL HEADER -->
<header>
    <div class="navbar1">
        <a href="dashboard.php">Dashboard</a>
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
  <?php endif; ?>

  <?php if (in_array($user["user_type"], ["Safety_Staff", "Student"], true)): ?>
    <a class="sidebar2 <?php echo basename($_SERVER["PHP_SELF"]) === "../Module2/student-view.php" ? "active" : ""; ?>"
       href="../Module2/student-view.php">View Parking</a>
    <a class="sidebar2 <?php echo basename($_SERVER["PHP_SELF"]) === "../Module 3/view_bookings.php" ? "active" : ""; ?>"
       href="../Module 3/view_bookings.php">View Booking</a>
    <a class="sidebar2 <?php echo basename($_SERVER["PHP_SELF"]) === "view-status.php" ? "active" : ""; ?>"
       href="view-status.php">View Update Point & Status</a>
  <?php endif; ?>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

    <div style="margin-bottom: 20px;">
        <label for="vehicleSelect"><strong>Select Vehicle:</strong></label>
        <select id="vehicleSelect" onchange="changeVehicle(this.value)"></select>
    </div>

    <h3>View Update Point & Status</h3>

    <div class="info-box">
    <div class="profile-img">
        <img id="profilePic" src="profile-placeholder.png" alt="Profile Picture">
    </div>

        <div class="profile-details">
            <div>
                <strong>Status of enforcement:</strong>
                <span id="statusText">Loading...</span>
            </div>
        </div>
    </div>

    <div class="details-panel">

        <div><strong>Latest Violation:</strong> <span id="vehicleType">-</span></div>
        <div><strong>Vehicle number:</strong> <span id="vehicleNumber">-</span></div>
        <div><strong>Total demerit points:</strong> <span id="demeritPoints">0</span></div>

        <h4 style="margin-top:20px;">History of Summons</h4>

        <table id="historyTable">
            <thead>
                <tr>
                    <th>Violation</th>
                    <th>Vehicle</th>
                    <th>Date & Time</th>
                    <th>Area</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

    </div>
</div>

<script>
  const dropdown = document.getElementById("vehicleSelect");
  const tbody = document.querySelector("#historyTable tbody");

  function renderStatus(data) {
    document.getElementById("statusText").textContent = data.enforcement || "—";
    document.getElementById("vehicleNumber").textContent = data.vehicle || "—";
    document.getElementById("vehicleType").textContent = data.latest_violation || "—";
    document.getElementById("demeritPoints").textContent = data.total_points ?? 0;

    tbody.innerHTML = "";

    if (!data.history || data.history.length === 0) {
      tbody.innerHTML = `<tr><td colspan="4">No summons for this vehicle</td></tr>`;
      return;
    }

    data.history.forEach(item => {
      const row = document.createElement("tr");
      row.innerHTML = `
        <td>${item.violation}</td>
        <td>${item.vehicle}</td>
        <td>${item.datetime}</td>
        <td>${item.area}</td>
      `;
      tbody.appendChild(row);
    });
  }

  function loadVehicleStatus(vehicleId) {
    fetch("getVehicleStatus.php?vehicle_id=" + encodeURIComponent(vehicleId))
      .then(res => res.json())
      .then(data => {
        if (!data.ok) {
          alert("Load failed: " + (data.error || "Unknown error"));
          return;
        }
        renderStatus(data);
      })
      .catch(err => alert("Network error: " + err.message));
  }

  // Load dropdown vehicles
  fetch("getVehicles.php")
    .then(res => res.json())
    .then(data => {
      if (!data.ok) {
        alert("Failed to load vehicles");
        return;
      }

      dropdown.innerHTML = "";
      if (!data.rows || data.rows.length === 0) {
        dropdown.innerHTML = `<option value="">No vehicles found</option>`;
        return;
      }

      // restore last selected vehicle_id
      const saved = localStorage.getItem("selectedVehicleId");
      const firstId = data.rows[0].vehicle_id;
      const selectedId = saved ? parseInt(saved, 10) : firstId;

      data.rows.forEach(v => {
        const opt = document.createElement("option");
        opt.value = v.vehicle_id;
        opt.textContent = v.license_plate;
        if (parseInt(v.vehicle_id, 10) === selectedId) opt.selected = true;
        dropdown.appendChild(opt);
      });

      loadVehicleStatus(selectedId);
    })
    .catch(err => alert("Network error: " + err.message));

  window.changeVehicle = function(vehicleId) {
    localStorage.setItem("selectedVehicleId", vehicleId);
    loadVehicleStatus(vehicleId);
  };
</script>

</body>
</html>
