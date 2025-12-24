<?php
header("Content-Type: application/json; charset=utf-8");
require_once "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$stmt = $conn->prepare("
    UPDATE traffic_summon
    SET violation_id = ?,
        area_id = ?,
        Datetime_issued = ?
    WHERE Summon_id = ?
");
$stmt->bind_param(
    "iisi",
    $data['violation_id'],
    $data['area_id'],
    $data['datetime'],
    $data['summon_id']
);
if($stmt->execute()){
    echo json_encode(["ok"=>true]);
}else{
    http_response_code(500);
    echo json_encode(["ok"=>false,"error"=>$stmt->error]);
}
