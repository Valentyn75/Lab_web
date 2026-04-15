<?php
require_once '_db.php';

// Отримуємо дані
$id    = $_POST['id'];
$room  = $_POST['newResource'];

// DayPilot надсилає дати як 2026-04-10T14:00:00. 
// MySQL потребує 2026-04-10 14:00:00.
$start = str_replace("T", " ", $_POST['newStart']);
$end   = str_replace("T", " ", $_POST['newEnd']);

$stmt = $pdo->prepare("UPDATE reservations SET start = :start, end = :end, room_id = :room WHERE id = :id");

$success = $stmt->execute([
    ':id'    => $id,
    ':start' => $start,
    ':end'   => $end,
    ':room'  => $room
]);

header('Content-Type: application/json');
echo json_encode(["result" => $success ? "OK" : "Error"]);