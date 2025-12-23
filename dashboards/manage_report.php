<?php
/**
 * FKPark Security Manage Report
 * Shows charts for vehicle approval statuses
 */

require_once '../config.php';
requireLogin();

if ($_SESSION['role'] !== 'security') {
    header("Location: ../login.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
$user_id = $_SESSION['user_id'];

// Query counts
$sql = "SELECT status, COUNT(*) as count FROM approval GROUP BY status";
$result = mysqli_query($conn, $sql);
$counts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $counts[$row['status']] = $row['count'];
}
$pending = $counts['Pending'] ?? 0;
$approved = $counts['Approved'] ?? 0;
$rejected = $counts['Rejected'] ?? 0;
$total = $pending + $approved + $rejected;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Report | FKPark</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        header { background: #fd7e14; color: white; padding: 20px; margin-bottom: 30px; border-radius: 4px; }
        header h1 { font-size: 24px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .welcome h2 { font-size: 20px; color: #333; margin-bottom: 10px; }
        .welcome { margin-bottom: 20px; }
        .user-info { margin-bottom: 30px; padding: 10px; background: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 4px; font-size: 14px; color: #555; }
        .buttons { margin-top: 30px; text-align: center; }
        .logout-btn { background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 4px; text-decoration: none; cursor: pointer; font-size: 14px; }
        .navbar1{text-decoration-line: none;text-decoration: none; float: right; overflow: hidden; list-style-type: none; display: flex; text-align: center; padding: 0px; margin:0px;}
        .navbar1 a{ 
            display: block;
  color: black;
  padding: 0px 20px;
  text-decoration: none;
text-align: center;
}

.navbar1 a:hover{
    background-color: black;
  color: white;
}
  .sidebar {
    height: 100%;               /* full height */
    width: 250px;               /* sidebar width */
    width: 200px;               /* sidebar width */
    position: fixed;            /* stick to left */
    top: 20px;
    left: 20px;
    background-color: #7fffd6;
    padding-top: 20px;
    background-color: #FFFFFF;
    
    display: flex;
    flex-direction: column;
}

.sidebar a {
    padding: 15px 25px;
    padding:0px 0px;
    text-decoration: none;
    font-size: 18px;
    color: black;
    display: block;
}

.sidebar a:hover{
    background-color:black;
  color: white;
}

a.sidebar2{
    padding: 15px 20px;
}

.logo{
    width: 200px;
    height: auto;
}
.chart-container {
    margin-top: 30px;
}
.bar {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    background: #f9f9f9;
    border-radius: 8px;
    padding: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.bar-label {
    width: 120px;
    font-weight: bold;
    color: #333;
}
.bar-fill {
    height: 40px;
    margin-left: 20px;
    border-radius: 4px;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: bold;
    transition: width 0.3s ease;
}
.bar-fill.pending {
    background: linear-gradient(90deg, #ffc107, #ff8c00);
}
.bar-fill.approved {
    background: linear-gradient(90deg, #28a745, #20c997);
}
.bar-fill.rejected {
    background: linear-gradient(90deg, #dc3545, #c82333);
}
.stats {
    display: flex;
    justify-content: space-around;
    margin-bottom: 20px;
}
.stat {
    text-align: center;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 4px;
    border: 1px solid #e0e0e0;
    margin-right: 20px;
}
.stat h3 {
    font-size: 24px;
    color: #333;
}
.stat p {
    color: #555;
}
   </style>
</head>
<body>
    <header>
        <div class="navbar1">
            <a href="../Module1/security_profile.php">Profile</a>
            <a href="../logout.php">Logout</a>
        </div>
    </header>
    <div class="sidebar">
        <a href="#home"><img class="logo" src="../photo/logoUmpsa.png"></a>
        <a class="sidebar2" href="../Module2/security_view.php">View Parking</a>
        <a class="sidebar2" href="../Module1/security_list_vehicles.php">Vehicle Approval</a>
        <a class="sidebar2" href="../Module4/manage-summon.php">Manage Traffic Summon</a>
        <a class="sidebar2" href="../Module4/dashboard.php">Manage Dashboard</a>
        <a class="sidebar2 active" href="manage_report.php">Manage Report</a>
        <a class="sidebar2" href="../Module4/view-status.php">View Update Point & Status</a>
    </div>
    <div class="container">
        <div class="welcome">
            <h2>Manage Report</h2>
            <p>Vehicle Approval Statistics</p>
        </div>
        <div class="user-info">
            <strong>User ID:</strong> <?php echo htmlspecialchars($user_id); ?> |
            <strong>Role:</strong> Security
        </div>
        <div class="stats">
            <div class="stat">
                <h3><?php echo $pending; ?></h3>
                <p>Pending</p>
            </div>
            <div class="stat">
                <h3><?php echo $approved; ?></h3>
                <p>Approved</p>
            </div>
            <div class="stat">
                <h3><?php echo $rejected; ?></h3>
                <p>Rejected</p>
            </div>
        </div>
        <div class="chart-container">
            <h3>Approval Status Chart</h3>
            <?php if ($total > 0): ?>
            <div class="bar">
                <div class="bar-label">Pending</div>
                <div class="bar-fill pending" style="width: <?php echo ($pending / $total) * 100; ?>%;"><?php echo $pending; ?> (<?php echo round(($pending / $total) * 100, 2); ?>%)</div>
            </div>
            <div class="bar">
                <div class="bar-label">Approved</div>
                <div class="bar-fill approved" style="width: <?php echo ($approved / $total) * 100; ?>%;"><?php echo $approved; ?> (<?php echo round(($approved / $total) * 100, 2); ?>%)</div>
            </div>
            <div class="bar">
                <div class="bar-label">Rejected</div>
                <div class="bar-fill rejected" style="width: <?php echo ($rejected / $total) * 100; ?>%;"><?php echo $rejected; ?> (<?php echo round(($rejected / $total) * 100, 2); ?>%)</div>
            </div>
            <?php else: ?>
            <p>No data available.</p>
            <?php endif; ?>
        </div>
        <div class="buttons">
            <a href="security_dashboard.php" class="logout-btn">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>