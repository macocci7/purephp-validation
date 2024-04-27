<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Macocci7\PurephpValidation\ValidatorFactory as Validator;

// Input
$user = [
    'name' => 'fo',
    'email' => 'foo',
    'pionts' => -1,
];

// Valiation Rules
$rules = [
    'name' => 'required|string|min:3|max:10',
    'email' => 'required|string|email:rfc,dns',
    'pionts' => 'required|int|min:1',
];

// Set Tranlations Root Path
// - 'lang/' folder must be placed under the path.
Validator::translationsRootPath(__DIR__ . '/');

// Set lang: 'en' as default
Validator::lang('ja');

// Validation
$validator = Validator::make($user, $rules);

// Checking result
if ($validator->fails()) {
    var_dump($validator->errors()->messages());
} else {
    echo "🎊 Passed 🎉" . PHP_EOL;
}
