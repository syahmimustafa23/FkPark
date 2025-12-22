<?php
header("Content-Type: application/json; charset=utf-8");
require_once "db.php";

function json_fail(int $code, string $msg) {
  http_response_code($code);
  echo json_encode(["ok" => false, "error" => $msg]);
  exit;
}

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);
if (!is_array($data)) json_fail(400, "Invalid JSON");

$licensePlate = trim($data["license_plate"] ?? "");
$violationId  = (int)($data["violation_id"] ?? 0);
$areaName     = trim($data["area_name"] ?? "");
$dateTime     = trim($data["datetime_issued"] ?? "");

if ($licensePlate === "" || $violationId <= 0 || $areaName === "" || $dateTime === "") {
  json_fail(400, "Missing fields");
}

// datetime-local -> MySQL datetime
$dateTime = str_replace("T", " ", $dateTime);
if (strlen($dateTime) === 16) $dateTime .= ":00";

try {
  // 1) Get vehicle_id + user_id (owner) from license plate
  $st = $conn->prepare("SELECT vehicle_id, user_id FROM vehicle WHERE license_plate = ? LIMIT 1");
  if (!$st) throw new Exception("Prepare failed (vehicle lookup)");
  $st->bind_param("s", $licensePlate);
  $st->execute();
  $veh = $st->get_result()->fetch_assoc();
  if (!$veh) throw new Exception("Vehicle not found: " . $licensePlate);

  $vehicleId = (int)$veh["vehicle_id"];
  $userId    = (int)$veh["user_id"];

  // 2) Get Area_id from parking_area by name
  $st = $conn->prepare("SELECT Area_id FROM parking_area WHERE LOWER(Area_name)=LOWER(?) LIMIT 1");
  if (!$st) throw new Exception("Prepare failed (area lookup)");
  $st->bind_param("s", $areaName);
  $st->execute();
  $ar = $st->get_result()->fetch_assoc();
  if (!$ar) throw new Exception("Area not found in parking_area: " . $areaName);

  $areaId = (int)$ar["Area_id"];

  // 3) Detect if traffic_summon has vehicle_id column (so we can avoid duplicate vehicles later)
  $hasVehicleId = false;
  $sqlCheck = "
    SELECT 1
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'traffic_summon'
      AND COLUMN_NAME = 'vehicle_id'
    LIMIT 1
  ";
  $chk = $conn->query($sqlCheck);
  if ($chk && $chk->num_rows > 0) $hasVehicleId = true;

  // 4) Insert summon
  if ($hasVehicleId) {
    // Best: store vehicle_id
    $st = $conn->prepare("
      INSERT INTO traffic_summon (user_id, vehicle_id, Violation_id, Area_id, Datetime_issued)
      VALUES (?, ?, ?, ?, ?)
    ");
    if (!$st) throw new Exception("Prepare failed (insert with vehicle_id)");
    $st->bind_param("iiiis", $userId, $vehicleId, $violationId, $areaId, $dateTime);
  } else {
    // Fallback: only store user_id (will cause duplicates if user has 2 vehicles)
    $st = $conn->prepare("
      INSERT INTO traffic_summon (user_id, Violation_id, Area_id, Datetime_issued)
      VALUES (?, ?, ?, ?)
    ");
    if (!$st) throw new Exception("Prepare failed (insert without vehicle_id)");
    $st->bind_param("iiis", $userId, $violationId, $areaId, $dateTime);
  }

  $st->execute();

  echo json_encode([
    "ok" => true,
    "summon_id" => $conn->insert_id,
    "saved_vehicle_id" => $hasVehicleId ? $vehicleId : null
  ]);

} catch (Throwable $e) {
  json_fail(500, $e->getMessage());
}
