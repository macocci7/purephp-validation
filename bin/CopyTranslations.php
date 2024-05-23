<?php

namespace Macocci7\PurephpValidation;

require_once __DIR__ . '/../vendor/autoload.php';

use Macocci7\PurephpValidation\FileSystem;

$targets = [
    [
        'from' => __DiR__ . '/../vendor/illuminate/translation/lang/en/validation.php',
        'to' => __DIR__ . '/../src/lang/en/validation.php',
    ],
    [
        'from' => __DiR__ . '/../vendor/askdkc/breezejp/stubs/lang/ja/validation.php',
        'to' => __DIR__ . '/../src/lang/ja/validation.php',
    ],
];

foreach ($targets as $target) {
    try {
        FileSystem::copy($target['from'], $target['to']);
    } catch (\Exception $e) {
        echo $e->getMessage() . PHP_EOL;
    }
}
