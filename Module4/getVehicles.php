<?php
declare(strict_types=1);
ob_start();
header("Content-Type: application/json; charset=utf-8");

require_once "auth.php";
require_once "db.php"; // make sure $conn is available

try {
    $user = require_any_role(["Safety_Staff", "Student"]);
    $uid = (int)$user["user_id"];

    if ($user["user_type"] === "Student") {
        // Only approved vehicles for this student
        $st = $conn->prepare("
            SELECT v.vehicle_id, v.license_plate 
            FROM vehicle v
            JOIN approval a ON v.vehicle_id = a.vehicle_id
            WHERE v.user_id = ? AND a.status = 'Approved'
            ORDER BY v.license_plate
        ");
        $st->bind_param("i", $uid);
        $st->execute();
        $res = $st->get_result();
    } else {
        // Staff: all approved vehicles
        $st = $conn->prepare("
            SELECT v.vehicle_id, v.license_plate 
            FROM vehicle v
            JOIN approval a ON v.vehicle_id = a.vehicle_id
            WHERE a.status = 'Approved'
            ORDER BY v.license_plate
        ");
        $st->execute();
        $res = $st->get_result();
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
