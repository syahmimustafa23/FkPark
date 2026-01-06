<?php
require_once '../config.php';
requireLogin();

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
$user_id = $_SESSION['user_id'];

// Get all areas for dropdown
$areas = mysqli_query($conn, "SELECT * FROM parking_area ORDER BY Area_name");

// Get selected area
$selected_area = $_GET['area_id'] ?? null;
$spaces = null;

if ($selected_area) {
    $selected_area = (int)$selected_area;
    $spaces = mysqli_query($conn, "SELECT * FROM parking_space WHERE Area_id = '$selected_area' ORDER BY Space_num");
}

// Handle Update Space (only status can be updated)
if (isset($_POST['update_space'])) {
    $space_id = (int)$_POST['space_id'];
    $status = $_POST['status'];
    $area_id = (int)$_POST['area_id'];
    
    $sql = "UPDATE parking_space SET Current_status='$status' WHERE Space_id='$space_id'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: admin_manage_spaces.php?area_id=$area_id&msg=space_updated");
        exit();
    } else {
        $error = "Error updating space: " . mysqli_error($conn);
    }
}

// Handle Delete Space
if (isset($_GET['delete_id'])) {
    $space_id = (int)$_GET['delete_id'];
    $area_id = (int)$_GET['area_id'];
    
    // Check if space is occupied
    $space_check = mysqli_query($conn, "SELECT Current_status FROM parking_space WHERE Space_id = '$space_id'");
    $space_data = mysqli_fetch_assoc($space_check);
    
    if ($space_data['Current_status'] == 'Occupied') {
        $error = "Cannot delete an occupied space. Please mark it as available or maintenance first.";
    } else {
        // Delete bookings first
        $delete_bookings = "DELETE FROM booking WHERE Space_id = '$space_id'";
        mysqli_query($conn, $delete_bookings);
        
        // Delete usage records
        $delete_usage = "DELETE FROM parking_usage WHERE Space_id = '$space_id'";
        mysqli_query($conn, $delete_usage);
        
        // Delete space
        $delete_space = "DELETE FROM parking_space WHERE Space_id = '$space_id'";
        if (mysqli_query($conn, $delete_space)) {
            header("Location: admin_manage_spaces.php?area_id=$area_id&msg=space_deleted");
            exit();
        }
    }
}

$edit_space = null;
if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];
    $result = mysqli_query($conn, "SELECT * FROM parking_space WHERE Space_id = '$edit_id'");
    $edit_space = mysqli_fetch_assoc($result);
    $selected_area = $edit_space['Area_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Parking Spaces | FKPark</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        header { background: #667eea; color: white; padding: 20px 30px; margin-bottom: 30px; border-radius: 4px; }
        header h1 { font-size: 24px; }
        .navbar1 { float: right; display: flex; gap: 20px; }
        .navbar1 a { color: white; text-decoration: none; }
        .navbar1 a:hover { text-decoration: underline; }
        .sidebar {
            width: 200px;
            position: fixed;
            top: 20px;
            left: 20px;
            background: white;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .sidebar a {
            display: block;
            padding: 12px;
            margin: 5px 0;
            color: black;
            text-decoration: none;
            border-radius: 4px;
        }
        .sidebar a:hover {
            background: #667eea;
            color: white;
        }
        .logo {
            width: 100%;
            margin-bottom: 20px;
        }
        .container {
            margin-left: 250px;
            max-width: 1000px;
            background: white;
            padding: 30px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        h2 { margin-bottom: 20px; color: #333; }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .success { color: green; padding: 10px; background: #d4edda; border-radius: 4px; margin-bottom: 20px; }
        .error { color: red; padding: 10px; background: #f8d7da; border-radius: 4px; margin-bottom: 20px; }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .status-available { color: green; font-weight: bold; }
        .status-maintenance { color: orange; font-weight: bold; }
        .status-occupied { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <header>
        <h1>FKPark</h1>
        <div class="navbar1">
            <a href="../Module1/admin_view_profile.php">Profile</a>
            <a href="../logout.php">Logout</a>
        </div>
    </header>

    <div class="sidebar">
        <img class="logo" src="../photo/logoUmpsa.png" alt="Logo">
        <a href="../Module2/admin_list_area.php">Manage Area</a>
        <a href="../Module2/admin_manage_spaces.php">Manage Space</a>
        <a href="../Module2/admin_view.php">Parking Availability</a>
        <a href="../Module 3/admin_parking_report.php">Parking Report</a>
        <a href="../Module1/admin_list_users.php">Manage User</a>
    </div>

    <div class="container">
        <h2>Manage Parking Spaces</h2>

        <?php if (isset($_GET['msg'])): ?>
            <div class="success">
                <?php 
                    if ($_GET['msg'] == 'space_updated') echo "Space updated successfully!";
                    elseif ($_GET['msg'] == 'space_deleted') echo "Space deleted successfully!";
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Area Selection -->
        <div class="mb-3">
            <label class="form-label">Select Area:</label>
            <select class="form-select" onchange="window.location.href='?area_id=' + this.value">
                <option value="">-- Select Area --</option>
                <?php 
                mysqli_data_seek($areas, 0);
                while ($area = mysqli_fetch_assoc($areas)): 
                ?>
                    <option value="<?php echo $area['Area_id']; ?>" <?php echo ($selected_area == $area['Area_id']) ? 'selected' : ''; ?>>
                        <?php echo $area['Area_name']; ?> (<?php echo $area['Category']; ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <?php if ($selected_area): ?>
            <!-- Search Box -->
            <div class="mb-3 d-flex gap-2">
                <input type="text" id="searchInput" class="form-control" placeholder="🔍 Search space number or status (e.g., A01, Available, Maintenance)..." 
                       style="max-width: 500px;">
                <button onclick="clearSearch()" class="btn btn-secondary">Clear Search</button>
            </div>

            <?php if (isset($edit_space)): ?>
                <!-- Edit Space Form -->
                <div class="card bg-light mb-4">
                    <div class="card-body">
                    <h3 class="card-title mb-3">Edit Space <?php echo htmlspecialchars($edit_space['Space_num']); ?></h3>
                    <form method="POST">
                        <input type="hidden" name="area_id" value="<?php echo $selected_area; ?>">
                        <input type="hidden" name="space_id" value="<?php echo $edit_space['Space_id']; ?>">

                        <div class="mb-3">
                            <label class="form-label">Status:</label>
                            <select name="status" class="form-select" required>
                                <option value="Available" <?php echo ($edit_space['Current_status'] == 'Available') ? 'selected' : ''; ?>>Available</option>
                                <option value="Maintenance" <?php echo ($edit_space['Current_status'] == 'Maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" name="update_space" class="btn btn-primary">Update Status</button>
                            <a href="?area_id=<?php echo $selected_area; ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Spaces List -->
            <h3>Spaces in <?php echo htmlspecialchars(mysqli_fetch_assoc(mysqli_query($conn, "SELECT Area_name FROM parking_area WHERE Area_id = '$selected_area'"))['Area_name']); ?></h3>
            <?php 
            if ($spaces && mysqli_num_rows($spaces) > 0): 
            ?>
                <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Space Number</th>
                            <th>Status</th>
                            <th>QR Code</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
while ($space = mysqli_fetch_assoc($spaces)): 
    // Logic to handle all 4 possible statuses correctly
    if ($space['Current_status'] == 'Available') {
        $color_class = "status-available";
        $status_label = "Available";
    } elseif ($space['Current_status'] == '') {
        $color_class = "status-occupied"; // Using maintenance color for yellow
        $status_label = "Reserved";
    } elseif ($space['Current_status'] == 'Occupied') {
        $color_class = "status-occupied";
        $status_label = "Occupied";
    } else {
        $color_class = "status-maintenance";
        $status_label = "Maintenance";
    }
?>
                        <tr class="space-row" data-space-name="<?php echo htmlspecialchars($space['Space_num']); ?>" data-status="<?php echo htmlspecialchars($status_label); ?>">
                            <td><strong><?php echo htmlspecialchars($space['Space_num']); ?></strong></td>
                            <td>
                                <span class="<?php echo $color_class; ?>">
            <?php echo $status_label; ?>
        </span>
                            </td>
                            <td>
                                <?php if ($space['Space_id']): ?>
                                    <a href="qr_display.php?space_id=<?php echo $space['Space_id']; ?>&back_from=manage_spaces&area_id=<?php echo $selected_area; ?>" target="_blank">View QR</a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?area_id=<?php echo $selected_area; ?>&edit_id=<?php echo $space['Space_id']; ?>" class="link-primary small">Edit</a>
                                <a href="?area_id=<?php echo $selected_area; ?>&delete_id=<?php echo $space['Space_id']; ?>" 
                                   class="link-danger small ms-2"
                                   onclick="return confirm('Delete this space?');">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                </div>
            <?php else: ?>
                <p>No spaces in this area yet. Create spaces using the Manage Area section.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function searchSpaces(searchTerm) {
            const rows = document.querySelectorAll('.space-row');
            let foundCount = 0;

            rows.forEach(row => {
                const spaceName = row.getAttribute('data-space-name').toLowerCase();
                const status = row.getAttribute('data-status').toLowerCase();
                
                if (spaceName.includes(searchTerm.toLowerCase()) || status.includes(searchTerm.toLowerCase())) {
                    row.style.display = 'table-row';
                    foundCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (foundCount === 0 && searchTerm.length > 0) {
                alert('No parking spaces found matching your search.');
            }
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            const rows = document.querySelectorAll('.space-row');
            rows.forEach(row => {
                row.style.display = 'table-row';
            });
        }

        // Add real-time search
        if (document.getElementById('searchInput')) {
            document.getElementById('searchInput').addEventListener('keyup', function() {
                searchSpaces(this.value);
            });
        }
    </script>
</body>
</html>
