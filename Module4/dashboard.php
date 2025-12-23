<?php
require_once "auth.php";
$user = require_role("Safety_Staff");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard – Summon History</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

<!-- HORIZONTAL HEADER -->
<header>
  <div class="navbar1">
    <a href="#">Homepage</a>
    <a href="#">Profile</a>
    <a href="dashboard.html">Dashboard</a>
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

  <h2>Summon History</h2>

  <table id="historyTable">
    <thead>
      <tr>
        <th>Violation type</th>
        <th>Vehicle Number</th>
        <th>Date & Time</th>
        <th>Area</th>
      </tr>
    </thead>
    <tbody>
      <!-- Records auto-load here -->
    </tbody>
  </table>

  <a href="manage-summon.php" class="btn-back">Add New Summon</a>

</div>

<script>
  const tbody = document.querySelector("#historyTable tbody");

  fetch("getSummonHistory.php")
    .then(res => res.json())
    .then(data => {
      if (!data.ok) {
        alert("Load failed: " + (data.error || "Unknown error"));
        return;
      }

      tbody.innerHTML = "";

      data.rows.forEach(item => {
        const row = document.createElement("tr");
        row.innerHTML = `
          <td>${item.violation}</td>
          <td>${item.vehicle}</td>
          <td>${item.datetime}</td>
          <td>${item.area}</td>
        `;
        tbody.appendChild(row);
      });
    })
    .catch(err => alert("Network error: " + err.message));
</script>

</body>
</html>
