<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$db = $app->make('db');
$prices = $db->table('prices')->get();
echo "Total prices: " . count($prices) . "\n";
if (count($prices) > 0) {
    echo "First price: " . json_encode($prices[0]) . "\n";
    echo "Sample: " . $prices[0]->time_period . " (" . $prices[0]->start_time . "-" . $prices[0]->end_time . ")\n";
}
