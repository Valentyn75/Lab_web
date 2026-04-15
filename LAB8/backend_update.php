<?php
require_once '_db.php'; // Перевірте назву, у вас на скрині _db.php

// Додайте ці рядки, щоб уникнути помилок, якщо запит порожній
$id = isset($_POST['id']) ? $_POST['id'] : null;
$action = isset($_POST['action']) ? $_POST['action'] : '';

if (!$id) {
    die(json_encode(["result" => "Error", "message" => "No ID provided"]));
}

if ($action == 'update') {
    $stmt = $db->prepare("UPDATE reservations SET name = :name, start = :start, end = :end, room_id = :room, status = :status, paid = :paid WHERE id = :id");
    $stmt->execute([
        "id" => $_POST['id'],
        "name" => $_POST['name'],
        "start" => $_POST['start'],
        "end" => $_POST['end'],
        "room" => $_POST['room_id'],
        "status" => $_POST['status'],
        "paid" => $_POST['paid']
    ]);
} else if ($action == 'delete') {
    $stmt = $db->prepare("DELETE FROM reservations WHERE id = :id");
    $stmt->execute(["id" => $_POST['id']]);
}

echo json_encode(["result" => "OK"]);