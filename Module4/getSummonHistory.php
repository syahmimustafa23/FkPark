<?php
header("Content-Type: application/json");
require_once "db.php";

$sql = "
  SELECT 
    vt.Violation_name AS violation,
    v.license_plate   AS vehicle,
    ts.Datetime_issued AS datetime,
    pa.Area_name      AS area
  FROM traffic_summon ts
  JOIN violation_type vt ON vt.Violation_id = ts.Violation_id
  JOIN parking_area pa ON pa.Area_id = ts.Area_id
  JOIN vehicle v ON v.vehicle_id = (
      SELECT MIN(v2.vehicle_id)
      FROM vehicle v2
      WHERE v2.user_id = ts.user_id
  )
  ORDER BY ts.Datetime_issued DESC
  LIMIT 100
";

$res = $conn->query($sql);

$data = [];
if ($res) {
  while ($row = $res->fetch_assoc()) $data[] = $row;
}

echo json_encode(["ok" => true, "rows" => $data]);
