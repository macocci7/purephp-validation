<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Macocci7\PurephpValidation\ValidatorFactory as Validator;

// Input
$user = [
    'name' => 'fo',
    'email' => 'foo',
    'password' => 'pass',
];

// Valiation Rules
$rules = [
    'name' => 'required|string|min:3|max:10',
    'email' => 'required|string|email:rfc,dns',
    'password' => 'required|string|min:8|max:16',
];

// Validation
$validator = Validator::make($user, $rules);

// Checking Result
if ($validator->fails()) {
    var_dump($validator->errors()->messages());
} else {
    echo "🎊 Passed 🎉" . PHP_EOL;
}
