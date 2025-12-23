<?php
header("Content-Type: application/json; charset=utf-8");

require_once "auth.php";
require_once "db.php";

$user = require_role("Safety_Staff");

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

$summonId    = (int)($data["summon_id"] ?? 0);
$violationId = (int)($data["violation_id"] ?? 0);
$vehicleId   = (int)($data["vehicle_id"] ?? 0);
$areaId      = (int)($data["area_id"] ?? 0);
$datetime    = trim($data["datetime"] ?? "");

if ($summonId <= 0 || $violationId <= 0 || $vehicleId <= 0 || $areaId <= 0 || $datetime === "") {
    http_response_code(400);
    echo json_encode(["ok" => false, "error" => "Missing fields"]);
    exit;
}

// Convert datetime to MySQL format
$datetimeObj = DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
if (!$datetimeObj) {
    // Try ISO format fallback
    $datetimeObj = DateTime::createFromFormat('Y-m-d\TH:i', $datetime);
}
if (!$datetimeObj) {
    http_response_code(400);
    echo json_encode(["ok" => false, "error" => "Invalid datetime format"]);
    exit;
}
$datetime_mysql = $datetimeObj->format('Y-m-d H:i:s');

$stmt = $conn->prepare("
    UPDATE traffic_summon 
    SET Violation_id = ?, vehicle_id = ?, Area_id = ?, Datetime_issued = ?
    WHERE summon_id = ?
");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $conn->error]);
    exit;
}

$stmt->bind_param("iiisi", $violationId, $vehicleId, $areaId, $datetime_mysql, $summonId);

if ($stmt->execute()) {
    echo json_encode(["ok" => true, "datetime" => $datetime_mysql]);
} else {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $stmt->error]);
}
