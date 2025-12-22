<?php
declare(strict_types=1);

ob_start();
header("Content-Type: application/json; charset=utf-8");

require_once "auth.php";
$user = require_any_role(["Safety_Staff", "Student"]);

try {
  $vehicleId = (int)($_GET["vehicle_id"] ?? 0);
  if ($vehicleId <= 0) {
    http_response_code(400);
    ob_clean();
    echo json_encode(["ok" => false, "error" => "Missing vehicle_id"]);
    exit;
  }

  // Students can only access their own vehicle, staff can access any
  if ($user["user_type"] === "Student") {
    $uid = (int)$user["user_id"];
    $st = $conn->prepare("SELECT license_plate FROM vehicle WHERE vehicle_id = ? AND user_id = ? LIMIT 1");
    $st->bind_param("ii", $vehicleId, $uid);
  } else {
    $st = $conn->prepare("SELECT license_plate FROM vehicle WHERE vehicle_id = ? LIMIT 1");
    $st->bind_param("i", $vehicleId);
  }

  $st->execute();
  $v = $st->get_result()->fetch_assoc();

  if (!$v) {
    http_response_code(403);
    ob_clean();
    echo json_encode(["ok" => false, "error" => "Forbidden"]);
    exit;
  }

  $sql = "
    SELECT
      vt.Violation_name  AS violation,
      v.license_plate    AS vehicle,
      ts.Datetime_issued AS datetime,
      pa.Area_name       AS area,
      vt.Points          AS points
    FROM traffic_summon ts
    JOIN violation_type vt ON vt.Violation_id = ts.Violation_id
    JOIN vehicle v         ON v.vehicle_id = ts.vehicle_id
    JOIN parking_area pa   ON pa.Area_id = ts.Area_id
    WHERE ts.vehicle_id = ?
    ORDER BY ts.Datetime_issued DESC
    LIMIT 200
  ";

  $st = $conn->prepare($sql);
  $st->bind_param("i", $vehicleId);
  $st->execute();
  $res = $st->get_result();

  $rows = [];
  $total = 0;
  while ($r = $res->fetch_assoc()) {
    $total += (int)$r["points"];
    $rows[] = $r;
  }

  $enforcement =
    ($total < 20) ? "Warning given" :
    (($total < 50) ? "Revoke of in campus vehicle permission for 1 semester" :
    (($total < 80) ? "Revoke of in campus vehicle permission for 2 semesters" :
                     "Revoke of in campus vehicle permission for the entire study duration"));

  ob_clean();
  echo json_encode([
    "ok" => true,
    "vehicle" => $v["license_plate"],
    "latest_violation" => $rows[0]["violation"] ?? "-",
    "total_points" => $total,
    "enforcement" => $enforcement,
    "history" => $rows
  ], JSON_UNESCAPED_UNICODE);
  exit;

} catch (Throwable $e) {
  http_response_code(500);
  ob_clean();
  echo json_encode(["ok" => false, "error" => $e->getMessage()]);
  exit;
}
