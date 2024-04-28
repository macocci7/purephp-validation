<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Macocci7\PurephpValidation\Rules\Instance;
use Macocci7\PurephpValidation\ValidatorFactory as Validator;

// Creating instance
$validator = Validator::make(
    data: [
        'prop1' => new Instance([]),
        'prop2' => 'Instance',
        'prop3' => fn () => true,
    ],
    rules: [
        'prop1' => Instance::of(Instance::class),
        'prop2' => Instance::of([
            Instance::class,
            Validator::class,
            (fn () => true)::class,
        ]),
        'prop3' => Instance::of('Closure'),
    ],
);

// Checking result
if ($validator->fails()) {
    var_dump($validator->errors()->messages());
} else {
    echo "🎊 passed 🎉" . PHP_EOL;
}
