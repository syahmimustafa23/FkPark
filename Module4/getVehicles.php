<?php
declare(strict_types=1);
ob_start();
header("Content-Type: application/json; charset=utf-8");

require_once "auth.php";

try {
  $user = require_any_role(["Safety_Staff", "Student"]);

  $uid = (int)$user["user_id"];

  if ($user["user_type"] === "Student") {
    $st = $conn->prepare("SELECT vehicle_id, license_plate FROM vehicle WHERE user_id = ? ORDER BY license_plate");
    $st->bind_param("i", $uid);
    $st->execute();
    $res = $st->get_result();
  } else {
    $res = $conn->query("SELECT vehicle_id, license_plate FROM vehicle ORDER BY license_plate");
  }

  $rows = [];
  while ($r = $res->fetch_assoc()) $rows[] = $r;

  ob_clean();
  echo json_encode(["ok" => true, "rows" => $rows]);
  exit;

} catch (Throwable $e) {
  http_response_code(400);
  ob_clean();
  echo json_encode(["ok" => false, "error" => $e->getMessage()]);
  exit;
}
