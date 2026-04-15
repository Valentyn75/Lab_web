<?php
require_once '_db.php';

// Встановлюємо заголовок, що ми повертаємо дані у форматі JSON
header('Content-Type: application/json');

try {
    // 1. Отримуємо та очищуємо дані від системи (автоматичні дані з календаря)
    // Формат ISO (з літерою T) міняємо на стандартний SQL формат
    $start   = str_replace("T", " ", $_POST['start']);
    $end     = str_replace("T", " ", $_POST['end']);
    $room_id = (int)$_POST['resource'];

    // 2. Отримуємо дані від користувача (введені вручну у формі)
    $name    = $_POST['name'];
    $status  = $_POST['status'];
    $paid    = (int)$_POST['paid'];

    // 3. ПЕРЕВІРКА НА НАКЛАДАННЯ (Захист від Overbooking)
    // Шукаємо записи в тій самій кімнаті, де часові інтервали перетинаються.
    // Логіка: Запис перетинається, якщо він НЕ закінчується до початку нового 
    // і НЕ починається після закінчення нового.
    $stmt_check = $pdo->prepare("
        SELECT count(*) 
        FROM reservations 
        WHERE room_id = :room_id 
        AND NOT (end <= :start OR start >= :end)
    ");

    $stmt_check->execute([
        ':room_id' => $room_id,
        ':start'   => $start,
        ':end'     => $end
    ]);

    // Якщо знайдено хоча б один перетин — повертаємо повідомлення про помилку
    if ($stmt_check->fetchColumn() > 0) {
        echo json_encode([
            "result" => "Error", 
            "message" => "Цей номер уже зайнятий на обрані дати!"
        ]);
        exit; // Припиняємо виконання скрипта
    }

    // 4. СТВОРЕННЯ ЗАПИСУ (виконується тільки якщо номер вільний)
    $stmt = $pdo->prepare("
        INSERT INTO reservations (name, start, end, room_id, status, paid) 
        VALUES (:name, :start, :end, :room_id, :status, :paid)
    ");

    $stmt->execute([
        ':name'    => $name,
        ':start'   => $start,
        ':end'     => $end,
        ':room_id' => $room_id,
        ':status'  => $status,
        ':paid'    => $paid
    ]);

    // Повертаємо успішну відповідь
    echo json_encode(["result" => "OK"]);

} catch (PDOException $e) {
    // Обробка критичних помилок (наприклад, збій підключення до БД)
    echo json_encode([
        "result" => "Error", 
        "message" => "Помилка бази даних: " . $e->getMessage()
    ]);
}
?>