<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Validation\Rule;
use Macocci7\PurephpValidation\Rules\FileWrapper as File;
use Macocci7\PurephpValidation\ValidatorFactory as Validator;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

// Input
$path = __DIR__ . '/../storage/uploaded/IMG_0707.JPG';
$input = [
    'filename' => basename($path),
    'photo' => new SymfonyFile($path),
];

// Rules
$rules = [
    'filename' => 'required|string|max:255',
    'photo' => [
        'required',
        File::image()
        //File::types(['jpg', 'png'])
            ->min(10)
            ->max(144)
            ->dimensions(
                Rule::dimensions()->maxWidth(200)->maxHeight(300)
            ),
    ],
];

// Messages
$messages = [
    'photo.required' => 'I want your :attribute 💖',
    'photo.image' => 'I want :attribute as an image💖',
    'photo.mimes' => ':attribute must be a type of: :values 💖',
    'photo.min' => ':attribute expected to be at least :min KB💖',
    'photo.max' => ':attribute expected to be at most :max KB💖',
    'photo.between' => ':attribute expected to be between :min KB and :max KB💖',
    'photo.dimensions' => ':attribute expected within the size of :max_width x :max_height in pixcels.💖',
];

// Attributes
$attributes = [
    'filename' => 'File Name',
    'photo' => 'Your Photo',
];

// Creating an instance
$validator = Validator::make(
    data:       $input,
    rules:      $rules,
    messages:   $messages,
    attributes: $attributes
);

// Checking result
if ($validator->fails()) {
    var_dump($validator->errors()->messages());
} else {
    echo "🎊 Passed 🎉" . PHP_EOL;
}
