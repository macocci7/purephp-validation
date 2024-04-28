<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Macocci7\PurephpValidation\Rules\PasswordWrapper as Password;
use Macocci7\PurephpValidation\ValidatorFactory as Validator;

// Input
$user = [
    'name' => 'foo bar',
    'email' => 'foo-bar@example.com',
    'password' => 'foo',
];

// Valiation Rules
$rules = [
    'name' => 'required|string|min:3|max:10',
    'email' => 'required|string|email:rfc',
    'password' => [
        'required',
        Password::min(8)
            ->max(16)
            // at least one letter
            ->letters()
            // at least one uppercase
            // and at least one lowercase letter
            ->mixedCase()
            // at least one number
            ->numbers()
            // at least one symbol
            ->symbols()
            // not in a data leak
            ->uncompromised(),
    ],
];

// Messages
$messages = [
    'password.mixed' => 'Password must include at least one uppercase and one lowercase letter.',
    'password.numbers' => 'Password must include at least one number.',
    'password.symbols' => 'Password must include at least one symbol.',
];

// Attributes
$attributes = [];

// Validation
$validator = Validator::make(
    data:       $user,
    rules:      $rules,
    messages:   $messages,
    attributes: $attributes
);

// Checking Result
if ($validator->fails()) {
    var_dump($validator->errors()->messages());
} else {
    echo "🎊 Passed 🎉" . PHP_EOL;
}
