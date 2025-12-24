<?php
header("Content-Type: application/json; charset=utf-8");
require_once "db.php";

$data = json_decode(file_get_contents("php://input"), true);
if (!is_array($data)) { http_response_code(400); echo json_encode(["ok"=>false,"error"=>"Invalid JSON"]); exit; }

$violationId = (int)($data["violation_id"] ?? 0);
$areaId      = (int)($data["area_id"] ?? 0);
$dateTime    = trim($data["datetime_issued"] ?? "");
$vehicleId   = (int)($data["vehicle_id"] ?? 0);

if ($violationId <=0 || $areaId <=0 || !$dateTime || $vehicleId<=0) {
    http_response_code(400); echo json_encode(["ok"=>false,"error"=>"Missing fields"]); exit;
}

// Get user_id from vehicle
$stmt = $conn->prepare("SELECT user_id FROM vehicle WHERE vehicle_id=? LIMIT 1");
$stmt->bind_param("i", $vehicleId);
$stmt->execute();
$veh = $stmt->get_result()->fetch_assoc();
if(!$veh) { http_response_code(404); echo json_encode(["ok"=>false,"error"=>"Vehicle not found"]); exit; }

$userId = (int)$veh["user_id"];

// Insert into traffic_summon
$stmt = $conn->prepare("
    INSERT INTO traffic_summon (user_id, violation_id, area_id, Datetime_issued)
    VALUES (?, ?, ?, ?)
");
$stmt->bind_param("iiis", $userId, $violationId, $areaId, $dateTime);
if($stmt->execute()){
    echo json_encode(["ok"=>true,"summon_id"=>$conn->insert_id]);
}else{
    http_response_code(500);
    echo json_encode(["ok"=>false,"error"=>$stmt->error]);
}
