<?php
require_once '_db.php';

// Очищаємо буфер (на випадок, якщо десь є пробіли або помилки)
ob_clean();

header('Content-Type: application/json');

// === Фільтри з GET-запиту ===
$capacity = isset($_GET['capacity']) ? (int)$_GET['capacity'] : 0;

// Базовий SQL
$sql = "SELECT id, name, capacity, status 
        FROM rooms 
        WHERE 1=1";

$params = [];

if ($capacity > 0) {
    if ($capacity >= 4) {
        $sql .= " AND capacity >= 4";
    } else {
        $sql .= " AND capacity = :capacity";
        $params[':capacity'] = $capacity;
    }
}

$sql .= " ORDER BY name";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

$result = [];
foreach ($rooms as $room) {
    $result[] = [
        "id"       => (string)$room['id'],
        "name"     => $room['name'],
        "capacity" => (int)$room['capacity'],
        "status"   => $room['status'] ?? 'Unknown'
    ];
}

echo json_encode($result);
exit;