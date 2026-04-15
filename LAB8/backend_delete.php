<?php
require_once '_db.php';
if(isset($_POST['id'])) {
    $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = :id");
    $stmt->execute(['id' => $_POST['id']]);
    echo json_encode(["result" => "OK"]);
}