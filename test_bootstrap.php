<?php
echo "Testing Laravel bootstrap...\n";

try {
    require_once __DIR__ . '/vendor/autoload.php';
    echo "✓ Autoloader loaded successfully\n";
    
    $app = require_once __DIR__ . '/bootstrap/app.php';
    echo "✓ Bootstrap app loaded successfully\n";
    
    echo "SUCCESS: Laravel is working!\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
