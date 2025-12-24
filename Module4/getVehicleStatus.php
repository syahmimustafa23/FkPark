<?php
declare(strict_types=1);
ob_start();
header("Content-Type: application/json; charset=utf-8");

require_once "auth.php";
require_once "db.php";

$user = require_any_role(["Safety_Staff", "Student"]);

try {
    $vehicleId = (int)($_GET["vehicle_id"] ?? 0);
    if ($vehicleId <= 0) {
        http_response_code(400);
        ob_clean();
        echo json_encode(["ok" => false, "error" => "Missing vehicle_id"]);
        exit;
    }

    // Get user_id and license_plate for this vehicle
    $stmt = $conn->prepare("SELECT user_id, license_plate FROM vehicle WHERE vehicle_id = ?");
    $stmt->bind_param("i", $vehicleId);
    $stmt->execute();
    $vehicle = $stmt->get_result()->fetch_assoc();

    if (!$vehicle) {
        http_response_code(404);
        ob_clean();
        echo json_encode(["ok" => false, "error" => "Vehicle not found"]);
        exit;
    }

    $userId = (int)$vehicle['user_id'];
    $licensePlate = $vehicle['license_plate'];

    $rows = [];
    $totalPoints = 0;

    if ($user["user_type"] === "Student") {
        // Ensure this vehicle belongs to the student
        if ($userId !== $user["user_id"]) {
            echo json_encode([
                "ok" => true,
                "vehicle" => $licensePlate,
                "latest_violation" => "-",
                "total_points" => 0,
                "enforcement" => "No summons for this vehicle",
                "history" => []
            ]);
            exit;
        }

        // Fetch all summons for this student and filter by selected vehicle
        $sql = "
            SELECT
                vt.Violation_name AS violation,
                v.license_plate AS vehicle,
                ts.Datetime_issued AS datetime,
                pa.Area_name AS area,
                vt.Points AS points
            FROM traffic_summon ts
            JOIN violation_type vt ON vt.Violation_id = ts.Violation_id
            JOIN parking_area pa ON pa.Area_id = ts.Area_id
            JOIN vehicle v ON v.user_id = ts.user_id
            WHERE ts.user_id = ?
            ORDER BY ts.Datetime_issued DESC
            LIMIT 200
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result();

        while ($r = $res->fetch_assoc()) {
            if ($r['vehicle'] === $licensePlate) {
                $totalPoints += (int)$r["points"];
                $rows[] = $r;
            }
        }
    } else {
        // Staff: original logic
        $sql = "
            SELECT
                vt.Violation_name AS violation,
                ? AS vehicle,
                ts.Datetime_issued AS datetime,
                pa.Area_name AS area,
                vt.Points AS points
            FROM traffic_summon ts
            JOIN violation_type vt ON vt.Violation_id = ts.Violation_id
            JOIN parking_area pa ON pa.Area_id = ts.Area_id
            WHERE ts.user_id = ?
            ORDER BY ts.Datetime_issued DESC
            LIMIT 200
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $licensePlate, $userId);
        $stmt->execute();
        $res = $stmt->get_result();

        while ($r = $res->fetch_assoc()) {
            $totalPoints += (int)$r["points"];
            $rows[] = $r;
        }
    }

    $enforcement = ($totalPoints < 20) ? "Warning given" :
                   (($totalPoints < 50) ? "Revoke of in-campus vehicle permission for 1 semester" :
                   (($totalPoints < 80) ? "Revoke of in-campus vehicle permission for 2 semesters" :
                                          "Revoke of in-campus vehicle permission for the entire study duration"));

    ob_clean();
    echo json_encode([
        "ok" => true,
        "vehicle" => $licensePlate,
        "latest_violation" => $rows[0]["violation"] ?? "-",
        "total_points" => $totalPoints,
        "enforcement" => $enforcement,
        "history" => $rows
    ]);
    exit;

} catch (Throwable $e) {
    http_response_code(500);
    ob_clean();
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
    exit;
}
