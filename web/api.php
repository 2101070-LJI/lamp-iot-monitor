<?php
/**
 * api.php - AJAX용 JSON 데이터 API
 * GET /api.php?sensor=SENS-001&limit=20
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$conn = new mysqli('localhost', 'sensor_user', 'sensor_pass123', 'sensor_db');
$conn->set_charset('utf8mb4');

$sensor = isset($_GET['sensor']) ? $conn->real_escape_string($_GET['sensor']) : '';
$limit  = isset($_GET['limit'])  ? (int)$_GET['limit']                        : 20;
$limit  = max(1, min(200, $limit));

$where  = $sensor ? "WHERE sensor_id = '$sensor'" : '';
$result = $conn->query("
    SELECT sensor_id, sensor_name, temperature, humidity, pressure, light, status, recorded_at
    FROM sensor_data
    $where
    ORDER BY recorded_at DESC
    LIMIT $limit
");

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$conn->close();

echo json_encode([
    'status' => 'ok',
    'count'  => count($data),
    'data'   => array_reverse($data),
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
