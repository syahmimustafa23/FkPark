<?php
declare(strict_types=1);
ob_start();
header("Content-Type: application/json; charset=utf-8");
require_once "auth.php";
require_once "db.php";

$user = require_any_role(["Safety_Staff", "Student"]);

try {
    $vehicleId = (int)($_GET["vehicle_id"] ?? 0);
    if ($vehicleId <= 0) throw new Exception("Missing vehicle_id");

    // Get vehicle info
    if ($user["user_type"] === "Student") {
        $stmt = $conn->prepare("SELECT vehicle_id, license_plate, user_id FROM vehicle WHERE vehicle_id = ? AND user_id = ? LIMIT 1");
        $stmt->bind_param("ii", $vehicleId, $user["user_id"]);
    } else {
        $stmt = $conn->prepare("SELECT vehicle_id, license_plate, user_id FROM vehicle WHERE vehicle_id = ? LIMIT 1");
        $stmt->bind_param("i", $vehicleId);
    }

    $stmt->execute();
    $vehicle = $stmt->get_result()->fetch_assoc();
    if (!$vehicle) throw new Exception("Forbidden");

    $userId = (int)$vehicle["user_id"];

    // Fetch summons for this user
    $sql = "
        SELECT
          vt.Violation_name AS violation,
          ? AS vehicle,
          ts.Datetime_issued AS datetime,
          pa.Area_name AS area,
          vt.Points AS points
        FROM traffic_summon ts
        JOIN violation_type vt ON vt.Violation_id = ts.violation_id
        JOIN parking_area pa   ON pa.Area_id = ts.area_id
        WHERE ts.user_id = ?
        ORDER BY ts.Datetime_issued DESC
        LIMIT 200
    ";
    $st = $conn->prepare($sql);
    $st->bind_param("si", $vehicle["license_plate"], $userId);
    $st->execute();
    $res = $st->get_result();

    $rows = []; $total = 0;
    while ($r = $res->fetch_assoc()) { $total += (int)$r["points"]; $rows[] = $r; }

    $enforcement =
        ($total < 20) ? "Warning given" :
        (($total < 50) ? "Revoke of in campus vehicle permission for 1 semester" :
        (($total < 80) ? "Revoke of in campus vehicle permission for 2 semesters" :
                         "Revoke of in campus vehicle permission for the entire study duration"));

    ob_clean();
    echo json_encode([
        "ok" => true,
        "vehicle" => $vehicle["license_plate"],
        "latest_violation" => $rows[0]["violation"] ?? "-",
        "total_points" => $total,
        "enforcement" => $enforcement,
        "history" => $rows
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    ob_clean();
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}
