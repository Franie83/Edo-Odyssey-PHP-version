<?php

$required = ['pdo', 'pdo_pgsql', 'mbstring', 'json', 'xml', 'ctype', 'fileinfo', 'tokenizer', 'gd'];

echo "Checking PHP extensions:\n\n";

foreach ($required as $ext) {
    echo $ext . ': ' . (extension_loaded($ext) ? '✅ LOADED' : '❌ MISSING') . "\n";
}

echo "\nAll loaded extensions:\n";
print_r(get_loaded_extensions());