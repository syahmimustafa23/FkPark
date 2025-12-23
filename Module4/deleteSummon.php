<?php
header("Content-Type: application/json; charset=utf-8");
require_once "auth.php";

// Only Safety Staff can delete
$user = require_role("Safety_Staff");

$summonId = (int)($_GET["summon_id"] ?? 0);
if ($summonId <= 0) {
    http_response_code(400);
    echo json_encode(["ok" => false, "error" => "Missing summon_id"]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM traffic_summon WHERE summon_id = ?");
$stmt->bind_param("i", $summonId);

if ($stmt->execute()) {
    echo json_encode(["ok" => true]);
} else {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $stmt->error]);
}
