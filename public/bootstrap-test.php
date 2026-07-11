<?php

echo "Step 1: Starting\n";

$app_path = __DIR__.'/../bootstrap/app.php';

echo "Step 2: Looking for " . $app_path . "\n";

if (file_exists($app_path)) {
    echo "Step 3: File exists!\n";
} else {
    echo "Step 3: File NOT found!\n";
    exit;
}

echo "Step 4: About to require bootstrap\n";

try {
    $app = require_once $app_path;
    echo "Step 5: Bootstrap loaded successfully!\n";
    echo "Step 6: App type: " . get_class($app) . "\n";
} catch (Exception $e) {
    echo "Step 5: ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}