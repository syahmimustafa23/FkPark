<?php
header("Content-Type: application/json; charset=utf-8");
require_once "db.php";

$summonId = (int)($_GET['summon_id'] ?? 0);
if(!$summonId){ http_response_code(400); echo json_encode(["ok"=>false,"error"=>"Missing summon_id"]); exit; }

$stmt = $conn->prepare("DELETE FROM traffic_summon WHERE Summon_id=?");
$stmt->bind_param("i",$summonId);
if($stmt->execute()){
    echo json_encode(["ok"=>true]);
}else{
    http_response_code(500);
    echo json_encode(["ok"=>false,"error"=>$stmt->error]);
}
