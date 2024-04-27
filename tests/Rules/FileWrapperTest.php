<?php

declare(strict_types=1);

namespace Macocci7\PurephpValidation;

use Illuminate\Validation\Rule;
use Macocci7\PurephpValidation\Rules\FileWrapper;
use Macocci7\PurephpValidation\ValidatorFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
final class FileWrapperTest extends TestCase
{
    public static function provide_file_rules_can_work_correctly(): array
    {
        $jpeg = __DIR__ . '/input/image.jpg';
        $text = __DIR__ . '/input/dummy.txt';
        return [
            [
                'input' => [
                    'data' => ['photo' => new SymfonyFile($jpeg)],
                    'rules' => [
                        'photo' => [
                            FileWrapper::image()
                                ->min(40)
                                ->dimensions(
                                    Rule::dimensions()->maxWidth(200)->maxHeight(300)
                                )
                        ],
                    ],
                    'messages' => [],
                    'attributes' => [],
                ],
                'expected' => [
                    'fails' => true,
                    'messages' => [
                        'photo' => [
                            'The photo must be at least 40 kilobytes.',
                            'validation.dimensions',
                        ],
                    ],
                ],
            ],
            [
                'input' => [
                    'data' => ['photo' => new SymfonyFile($jpeg)],
                    'rules' => [
                        'photo' => [
                            FileWrapper::types(['png', 'gif'])
                                ->max(30)
                        ],
                    ],
                    'messages' => [],
                    'attributes' => [],
                ],
                'expected' => [
                    'fails' => true,
                    'messages' => [
                        'photo' => [
                            'The photo must be a file of type: png, gif.',
                            'The photo may not be greater than 30 kilobytes.',
                        ],
                    ],
                ],
            ],
            [
                'input' => [
                    'data' => ['photo' => new SymfonyFile($text)],
                    'rules' => [
                        'photo' => [
                            FileWrapper::image()
                                ->min(20)
                                ->max(30),
                        ],
                    ],
                    'messages' => [],
                    'attributes' => [],
                ],
                'expected' => [
                    'fails' => true,
                    'messages' => [
                        'photo' => [
                            'The photo must be between 20 and 30 kilobytes.',
                            'The photo must be an image.',
                        ],
                    ],
                ],
            ],
            [
                'input' => [
                    'data' => ['photo' => new SymfonyFile($jpeg)],
                    'rules' => [
                        'photo' => [
                            FileWrapper::image()
                                ->min(20)
                                ->max(1024)
                                ->dimensions(
                                    Rule::dimensions()->maxWidth(1024)->maxHeight(768)
                                ),
                        ],
                    ],
                    'messages' => [],
                    'attributes' => [],
                ],
                'expected' => [
                    'fails' => false,
                    'messages' => [
                    ],
                ],
            ],
        ];
    }

    #[DataProvider('provide_file_rules_can_work_correctly')]
    public function test_file_rules_can_work_correctly(array $input, array $expected): void
    {
        $validator = ValidatorFactory::make(
            $input['data'],
            $input['rules'],
            $input['messages'],
            $input['attributes'],
        );
        $this->assertSame($expected['fails'], $validator->fails());
        if ($validator->fails()) {
            $this->assertSame($expected['messages'], $validator->errors()->messages());
        }
    }
}
