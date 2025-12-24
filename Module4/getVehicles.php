<?php
header("Content-Type: application/json; charset=utf-8");
require_once "auth.php";
require_once "db.php";

$user = require_any_role(["Safety_Staff", "Student"]);

try {
    if ($user["user_type"] === "Student") {
        $stmt = $conn->prepare("SELECT vehicle_id, license_plate FROM vehicle WHERE user_id = ? ORDER BY license_plate");
        $stmt->bind_param("i", $user["user_id"]);
    } else {
        $stmt = $conn->prepare("SELECT vehicle_id, license_plate FROM vehicle ORDER BY license_plate");
    }

    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($r = $res->fetch_assoc()) $rows[] = $r;

    echo json_encode(["ok" => true, "rows" => $rows]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}
