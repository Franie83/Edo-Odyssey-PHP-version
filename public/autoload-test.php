<?php

echo "Step 1: Before autoload\n";

require __DIR__.'/../vendor/autoload.php';

echo "Step 2: After autoload\n";

use Illuminate\Container\Container;

echo "Step 3: After use statement\n";

$container = new Container();

echo "Step 4: Container created successfully!";