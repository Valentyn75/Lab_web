<?php
require_once '_db.php';

header('Content-Type: application/json');

// Запит без умови WHERE - забираємо все
$stmt = $pdo->prepare("SELECT * FROM reservations ORDER BY start ASC");
$stmt->execute();

$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$result = [];

foreach ($reservations as $res) {
    $paidAmount = (int)$res['paid'];
    $status = $res['status']; // Очікуємо: New, Confirmed, Arrived, CheckedOut, Expired
    
    $barColor = "#3498db"; // Дефолтний синій

    // Оновлена логіка статусів згідно з вашими атрибутами
    switch ($status) {
        case 'New':
            $barColor = "#f39c12"; // Помаранчевий (Нове)
            $statusLabel = "Нове";
            break;
        case 'Confirmed':
            $barColor = "#2ecc71"; // Світло-зелений (Підтверджено)
            $statusLabel = "Підтверджено";
            break;
        case 'Arrived':
            $barColor = "#16a085"; // Темно-зелений (Заселено)
            $statusLabel = "Заселено";
            break;
        case 'CheckedOut':
            $barColor = "#95a5a6"; // Сірий (Перевірено/Виїхав)
            $statusLabel = "Перевірено";
            break;
        case 'Expired':
            $barColor = "#e74c3c"; // Червоний (Протерміновано)
            $statusLabel = "Expired";
            break;
        default:
            $statusLabel = $status;
    }

    // Формуємо текст для картки
    $timeStart = date("H:i", strtotime($res['start']));
    $timeEnd = date("H:i", strtotime($res['end']));

    $result[] = [
        "id"       => (string)$res['id'],
        "resource" => (string)$res['room_id'],
        "start"    => substr($res['start'], 0, 10),
        "end"      => substr($res['end'], 0, 10),
        "barColor" => $barColor,
        "status"   => $status, // технічна назва для форми
        "paid"     => $paidAmount,
        
        // Виводимо текст вже з перекладеним статусом
        "text"     => "{$res['name']}<br>" . 
                      "<span style='font-size: 11px; color: #555;'>" . 
                      "🕒 $timeStart-$timeEnd | $statusLabel | $paidAmount%" . 
                      "</span>"
    ];
}

echo json_encode($result);