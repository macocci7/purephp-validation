<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Macocci7\PurephpValidation\ValidatorFactory;

// Input
$user = [
    'name' => 'hogehogehogeh',
    'email' => 'foo',
    'level' => 0,
];

// Valiation Rules
$rules = [
    'name' => 'required|string|min:3|max:10',
    'email' => 'required|string|email:rfc,dns',
    'level' => 'required|int|min:1,max:99',
];

// Set lang Root Path
ValidatorFactory::translationsRootPath(__DIR__ . '/');

// Set lang
ValidatorFactory::lang('ja');

// Validation
$validator = ValidatorFactory::make($user, $rules);

// Checking Result
if ($validator->fails()) {
    var_dump($validator->errors());
} else {
    echo "passed.\n";
}
