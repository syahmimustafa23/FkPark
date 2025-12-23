<?php
/**
 * FKPark Student Dashboard (minimal)
 */
require_once '../config.php';
requireLogin();
if ($_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}
$id = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT * FROM users WHERE user_id = '$id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if (!$user) { die("User not found."); }

// Process the update form submission 
if (isset($_POST['update'])) {
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $uname = mysqli_real_escape_string($conn, $_POST['username']);
    $role = $_POST['user_type'];

    $update_sql = "UPDATE users SET full_name='$name', username='$uname' WHERE user_id='$id'";
    
    if (mysqli_query($conn, $update_sql)) {
        header("Location: student_profile.php?msg=updated");
        exit();
    }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard  | FKPark</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        header { background: #28a745; color: white; padding: 30px; margin-bottom: 30px; border-radius: 4px; }
        header h1 { font-size: 24px; }
        .container { max-width: 400px; margin: 0 auto; background: white; padding: 30px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .welcome h2 { font-size: 20px; color: #333; margin-bottom: 10px; }
        .welcome { margin-bottom: 20px; }
        .user-info { margin-bottom: 30px; padding: 10px; background: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 4px; font-size: 14px; color: #555; }
        .buttons { margin-top: 30px; text-align: center; }
        .logout-btn { background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 4px; text-decoration: none; cursor: pointer; font-size: 14px; }
        img{width: 100px; }
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

.save{
    text-decoration:  none; 
    list-style-type: none;

    background: #219bffff; color: white; padding: 10px 20px; border: none; border-radius: 4px; text-decoration: none; cursor: pointer; font-size: 14px; 
}

.cancel{
    text-decoration:  none; 
    list-style-type: none;

    background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 4px; text-decoration: none; cursor: pointer; font-size: 14px; 
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
td{
    padding: 10px;
    text-align: center;
}



    </style>
</head>
<body>
    <header>
        
        <div class="navbar1">
         
            <a href="student_profile.php">Profile</a>
                <a href="../logout.php">Logout</a>
    </div>
    </header>
    <div class="sidebar">
         <a href="#home"><img class="logo" src="../photo/logoUmpsa.png"></a>
    <a class="sidebar2" href="../Module2/student_view.php">View Parking</a>
    <a class="sidebar2" href="../Module 3/viewBooking.php">View Booking</a>
 
    <a class="sidebar2" href="sidebar2">Traffic Summons</a>
 
    </div>
    
   
    <div class="container">
      <form method="POST">
    <h2>Update Profile Info</h2><br>
    <label>Full Name:</label><br><br>
    <input type="text" name="full_name" value="<?php echo $user['full_name']; ?>" required><br>
<br>
    <label>Username:</label><br><br>
    <input type="text" name="username" value="<?php echo $user['username']; ?>" required>

    <br>
<br>
    <button class="save" type="submit" name="update">Save Changes</button>
    <button class="cancel" type="button" onclick="window.location.href='student_profile.php'">Cancel</button>
</form>
</div>


     
</body>
</html>











