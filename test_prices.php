<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=futsal_reservasi', 'root', '');
$stmt = $pdo->query('SELECT * FROM prices WHERE field_id = 1');
$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Count: " . count($res) . "\n";
print_r(array_slice($res, 0, 2));
