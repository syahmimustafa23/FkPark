<?php
require_once "db.php";

// Get summon ID from GET
$summonId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($summonId <= 0) {
    die("Invalid summon ID.");
}

// Fetch summon details
$stmt = $conn->prepare("
    SELECT 
        ts.summon_id,
        ts.Datetime_issued,
        v.license_plate,
        pa.Area_name,
        ts.violation_id
    FROM traffic_summon ts
    JOIN vehicle v ON ts.user_id = v.user_id
    JOIN parking_area pa ON ts.area_id = pa.Area_id
    WHERE ts.summon_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $summonId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Summon not found.");
}

$data = $result->fetch_assoc();

// Violation mapping
$violations = [
    1 => ["name" => "Parking Violation", "points" => 10],
    2 => ["name" => "Regulation Non-compliance", "points" => 20],
    3 => ["name" => "Accident Caused", "points" => 50]
];

$violationName = $violations[$data['violation_id']]['name'] ?? "Unknown";
$points = $violations[$data['violation_id']]['points'] ?? 0;

// Generate QR code pointing to this summon page
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$path = "/Module4/view-summon.php"; // adjust path
$summonLink = $protocol . $host . $path . "?id=" . $summonId;
$qrCodeUrl = "https://chart.googleapis.com/chart?cht=qr&chs=250x250&chl=" . urlencode($summonLink);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Traffic Summon Details</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6f8;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding-top: 40px;
      min-height: 100vh;
    }
    .card {
      background: #fff;
      padding: 30px;
      width: 400px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.15);
      text-align: center;
      position: relative;
    }
    h2 { margin-bottom: 20px; }
    .row { margin-bottom: 12px; text-align:left; }
    .label { font-weight: bold; color: #555; }
    .value { margin-top: 4px; }
    .points {
      background: #ffecec;
      color: #c0392b;
      padding: 10px;
      font-weight: bold;
      border-radius: 5px;
      margin-top: 20px;
    }
    .qr-code { margin-top: 20px; }
    .footer {
      text-align: center;
      margin-top: 20px;
      font-size: 13px;
      color: #777;
    }
    .btn-back, .btn-print {
      display: inline-block;
      margin-bottom: 20px;
      padding: 10px 16px;
      background: #fd7e14;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .btn-back:hover, .btn-print:hover { background: #0b7051; }

    /* PRINT STYLES */
    @media print {
      body { background: #fff; }
      .btn-back, .btn-print { display: none; }
      .card { box-shadow: none; border: 1px solid #000; }
    }
  </style>
</head>
<body>

<div class="card">
  <a href="dashboard.php" class="btn-back">← Back to Dashboard</a>
  <button onclick="window.print()" class="btn-print">🖨️ Print Summon</button>

  <h2>Traffic Summon</h2>

  <div class="row">
    <div class="label">Summon ID</div>
    <div class="value"><?= htmlspecialchars($data['summon_id']) ?></div>
  </div>

  <div class="row">
    <div class="label">Violation Type</div>
    <div class="value"><?= htmlspecialchars($violationName) ?></div>
  </div>

  <div class="row">
    <div class="label">Vehicle Number</div>
    <div class="value"><?= htmlspecialchars($data['license_plate']) ?></div>
  </div>

  <div class="row">
    <div class="label">Area</div>
    <div class="value"><?= htmlspecialchars($data['Area_name']) ?></div>
  </div>

  <div class="row">
    <div class="label">Date & Time Issued</div>
    <div class="value"><?= htmlspecialchars($data['Datetime_issued']) ?></div>
  </div>

  <div class="points">
    Demerit Points: <?= $points ?>
  </div>

  <!-- QR Code -->
  <div class="qr-code">
    <h3>Scan QR Code</h3>
    <img src="<?= $qrCodeUrl ?>" alt="Summon QR Code" />
  </div>

  <div class="footer">
    This summon was issued by UMPSA Safety Unit
  </div>
</div>

</body>
</html>
