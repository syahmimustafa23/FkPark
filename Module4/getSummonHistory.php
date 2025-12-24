<?php
header("Content-Type: application/json; charset=utf-8");
require_once "db.php";

try {
    $sql = "
    SELECT 
        ts.Summon_id AS summon_id,
        v.license_plate AS vehicle,
        vt.Violation_name AS violation,
        pa.Area_name AS area,
        ts.Datetime_issued AS datetime
    FROM traffic_summon ts
    LEFT JOIN vehicle v ON ts.user_id = v.user_id
    LEFT JOIN violation_type vt ON ts.violation_id = vt.Violation_id
    LEFT JOIN parking_area pa ON ts.area_id = pa.Area_id
    ORDER BY ts.Datetime_issued DESC
    ";
    $res = $conn->query($sql);
    $rows = [];
    while($r = $res->fetch_assoc()) $rows[]=$r;
    echo json_encode(["ok"=>true,"rows"=>$rows]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["ok"=>false,"error"=>$e->getMessage()]);
}
