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

$vehicleId   = (int)($data["vehicle_id"] ?? 0);
$violationId = (int)($data["violation_id"] ?? 0);
$areaId      = (int)($data["area_id"] ?? 0);
$dateTime    = trim($data["datetime_issued"] ?? "");

if ($vehicleId <= 0 || $violationId <= 0 || $areaId <= 0 || $dateTime === "") {
    json_fail(400, "Missing fields");
}

// datetime-local -> MySQL datetime
$dateTime = str_replace("T", " ", $dateTime);
if (strlen($dateTime) === 16) $dateTime .= ":00";

try {
    // 1️⃣ Check if vehicle is APPROVED
    $st = $conn->prepare("SELECT * FROM approval WHERE vehicle_id = ? AND status = 'Approved' LIMIT 1");
    if (!$st) throw new Exception("Prepare failed (approval check)");
    $st->bind_param("i", $vehicleId);
    $st->execute();
    $result = $st->get_result();
    if ($result->num_rows === 0) {
        json_fail(403, "Cannot create summon: vehicle is not approved.");
    }

    // 2️⃣ Get user_id from vehicle
    $st = $conn->prepare("SELECT user_id FROM vehicle WHERE vehicle_id = ? LIMIT 1");
    if (!$st) throw new Exception("Prepare failed (vehicle lookup)");
    $st->bind_param("i", $vehicleId);
    $st->execute();
    $veh = $st->get_result()->fetch_assoc();
    if (!$veh) throw new Exception("Vehicle not found: ID " . $vehicleId);
    $userId = (int)$veh["user_id"];

    // 3️⃣ Insert into traffic_summon
    $st = $conn->prepare("
        INSERT INTO traffic_summon (user_id, vehicle_id, Violation_id, Area_id, Datetime_issued)
        VALUES (?, ?, ?, ?, ?)
    ");
    if (!$st) throw new Exception("Prepare failed (insert summon)");
    $st->bind_param("iiiis", $userId, $vehicleId, $violationId, $areaId, $dateTime);
    $st->execute();

    echo json_encode([
        "ok" => true,
        "summon_id" => $conn->insert_id,
        "saved_vehicle_id" => $vehicleId
    ]);

} catch (Throwable $e) {
    json_fail(500, $e->getMessage());
}
