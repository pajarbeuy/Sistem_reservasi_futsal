<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=futsal_reservasi', 'root', '');
$stmt = $pdo->query('SELECT * FROM bookings');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
