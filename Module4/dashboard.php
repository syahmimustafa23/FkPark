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
      <a class="sidebar2 active" href="dashboard.php">Manage Dashboard</a>
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
          <th>Actions</th>
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

    // ----------------- Load Summon History -----------------
    function loadSummonHistory() {
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
          <td>
            <button class="editBtn" data-id="${item.summon_id}">Edit</button>
            <button class="deleteBtn" data-id="${item.summon_id}">Delete</button>
          </td>
        `;
            tbody.appendChild(row);
          });
        })
        .catch(err => alert("Network error: " + err.message));
    }

    // ----------------- DELETE -----------------
    tbody.addEventListener("click", (e) => {
      if (e.target.classList.contains("deleteBtn")) {
        const id = e.target.dataset.id;
        if (!confirm("Are you sure you want to delete this summon?")) return;

        fetch("deleteSummon.php?summon_id=" + id)
          .then(res => res.json())
          .then(data => {
            if (data.ok) {
              alert("Deleted successfully");
              loadSummonHistory(); // reload table
            } else {
              alert("Delete failed: " + (data.error || "Unknown error"));
            }
          });
      }
    });

    // ----------------- EDIT -----------------
    tbody.addEventListener("click", (e) => {
      if (e.target.classList.contains("editBtn")) {
        const id = e.target.dataset.id;

        const newViolation = prompt("Enter new Violation ID:");
        const newVehicle = prompt("Enter new Vehicle ID:");
        const newArea = prompt("Enter new Area ID:");
        const newDatetime = prompt("Enter new DateTime (YYYY-MM-DD HH:MM:SS):");

        if (!newViolation || !newVehicle || !newArea || !newDatetime) {
          alert("All fields are required.");
          return;
        }

        fetch("updateSummon.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            summon_id: id,
            violation_id: newViolation,
            vehicle_id: newVehicle,
            area_id: newArea,
            datetime: newDatetime
          })
        })
          .then(res => res.json())
          .then(data => {
            if (data.ok) {
              alert("Updated successfully");
              loadSummonHistory();
            } else {
              alert("Update failed: " + (data.error || "Unknown error"));
            }
          });
      }
    });

    // Initial load
    loadSummonHistory();
  </script>

</body>

</html>