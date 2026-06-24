<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=futsal_reservasi', 'root', '');
$stmt = $pdo->query('SELECT model_id, role_id, (SELECT name FROM roles WHERE id = role_id) as role_name FROM model_has_roles');
$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($res);
