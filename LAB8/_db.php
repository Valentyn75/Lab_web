<?php
$host = '127.0.0.1';
$db   = 'hotel_booking';
$user = 'user';
$pass = '1532674899'; // Якщо OpenServer, спробуйте 'root'

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}