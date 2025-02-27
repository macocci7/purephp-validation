# Purephp Validation

## 1. Features

`Purephp Validation` is a standalone library to use the [Illuminate\Validation](https://github.com/illuminate/validation) package outside the Laravel framework.

This library is based on [jeffochoa/validator-factory](https://github.com/jeffochoa/validator-factory),

This library is customized to use with static calls, like a Laravel Facade:

```php
$validator = Validator::make(
    $data,
    $rules,
    $messages,
    $attributes,
);
```

It also supports `Password` rule object and `File` rule object.

```php
$validator = Validator::make(
    data: $data,
    rules: [
        'password' => [
            'required',
            Password::min(8),
        ],
        'attachment' => [
            'required',
            File::image(),
        ],
    ];
);
```

Additionally and uniquely, `Instance` rule object is supported.

```php
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
```

## 2. Contents

- [1. Features](#1-features)
- 2\. Contents
- [3. Requirements](#3-requirements)
- [4. Installation](#4-installation)
- [5. Usage](#5-usage)
    - [5.1. Basic Usage](#51-basic-usage)
    - [5.2. Setting Traslations Root Path and Language](#52-setting-translations-root-path-and-language)
    - [5.3. Using Passowrd Rule Object](#53-using-password-rule-object)
    - [5.4. Using File Rule Object](#54-using-file-rule-object)
    - [5.5. Using Instance Rule Object](#55-using-instance-rule-object)
- [6. Examples](#6-examples)
- [7. LICENSE](#7-license)

## 3. Requirements

- PHP 8.2 or later
- Composer installed

## 4. Installation

```bash
composer require macocci7/purephp-validation
```

## 5. Usage

### 5.1. Basic Usage

First, import `autoload.php` into your code (in `src/` folder) like this:

```php
require_once __DIR__ . '/../vendor/autoload.php';
```

Then, create a new instance of the `Illuminate\Validation\Validator` as follows:

```php
use Macocci7\PurephpValidation\ValidatorFactory as Validator;

$validator = Validator::make(
    data: [
        'name' => 'foo',
        'email' => 'foo@example.com',
        'password' => 'Passw0rd',
    ],
    rules: [
        'name' => 'required|string|min:3|max:40',
        'email' => 'required|email:rfc',
        'password' => 'required|string|min:8|max:16',
    ],
);
```

Now, you can check the validation results:

```php
if ($validator->fails()) {
    var_dump($validator->errors()->message);
} else {
    echo "🎊 Passed 🎉" . PHP_EOL;
}
```

You can learn more about writing validation rules at the [Laravel Official Documentation](https://laravel.com/docs/11.x/validation#quick-writing-the-validation-logic).

Here's also an example code for basic usage: [BasicUsage.php](examples/BasicUsage.php)

### 5.2. Setting Translations Root Path and Language

You'll probably want to place the `lang` folder somewhere else outside of `vendor/`.

You can set the Translations Root Path before creating an instance of `Validator`:

```php
// Set Translations Root Path (optional)
// - The path must end with '/'.
// - 'lang/' folder must be placed under the path.
Validator::translationsRootPath(__DIR__ . '/');
```

You can also set the Language before creating an instance of `Validator`:
```php
// Set lang: 'en' as default (optional)
Validator::lang('ja');
```

Here's an example code for setting Translations Root Path and Language: [SetTranslationsRootPath.php](examples/SetTranslationsRootPath.php)

### 5.3. Using Password Rule Object

You can validate passwords using Laravel's `Password` rule object.

```php
use Macocci7\PurephpValidation\Rules\PasswordWrapper as Password;

$validator = Validator::make(
    data: [ 'password' => 'pass' ],
    rules: [
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
    ],
);
```

You can learn more about Laravel's `Password` rule object at the [Laravel Official Document](https://laravel.com/docs/11.x/validation#validating-passwords).

Here's an example code for using `Password` rule object: [ValidatePassword.php](examples/ValidatePassword.php)

### 5.4. Using File Rule Object

You can validate files using Laravel's `File` rule object.

```php
use Illuminate\Validation\Rule;
use Macocci7\PurephpValidation\Rules\FileWrapper as File;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

$path = __DIR__ . '/../storage/uploaded/foo.jpg';

$validator = Validator::make(
    data: [
        'photo' => new SymfonyFile($path),
    ],
    rules: [
        'photo' => [
            'required',
            File::image()
                ->min(10)   // kilo bytes
                ->max(144)  // kilo bytes
                ->dimensions(
                    Rule::dimensions()
                        ->maxWidth(200)     // pix
                        ->maxHeight(300)    // pix
                ),
        ],
    ],
);
```

You can learn more about Laravel's `File` rule object at the [Laravel Official Document](https://laravel.com/docs/11.x/validation#validating-files).

Here's an example code for using Laravel's `File` rule object: [ValidateFile.php](examples/ValidateFile.php)

### 5.5. Using Instance Rule Object

You can validate objects using `Instance` rule object. (unique feature)

By using `Instance::of($class)` method as a rule, you can perform `$value instanceof $class` in the validation.

`Instance::of()` accepts class name(s) as an argument.

```php
use Macocci7\PurephpValidation\Rules\Instance;

$validator = Validator::make(
    data: $data,
    rules: [
        'prop1' => Instance::of(Instance::class),
        'prop2' => Instance::of([
            // Macocci7\PurephpValidation\Rules\Instance
            Instance::class,
            // Macocci7\PurephpValidation\ValidatorFactory
            Validator::class,
            // Closure
            (fn () => true)::class,
        ]),
        'prop3' => Instance::of('Closure'),
    ],
);
```

Here's an example code for using `Instance` rule object: [ValidateInstance.php](examples/ValidateInstance.php)

## 6. Examples

- [BasicUsage.php](examples/BasicUsage.php)
- [SetTranslationsRootPath.php](examples/SetTranslationsRootPath.php)
- [ValidatePassword.php](examples/ValidatePassword.php)
- [ValidateFile.php](examples/ValidateFile.php)
- [ValidateInstance.php](examples/ValidateInstance.php)

## 7. LICENSE

[MIT](LICENSE)

***

Copyright 2024 - 2025 macocci7.
