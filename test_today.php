<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=futsal_reservasi', 'root', '');
$stmt = $pdo->query('SELECT * FROM bookings WHERE start_time >= "2026-06-23" AND start_time <= "2026-06-24"');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
