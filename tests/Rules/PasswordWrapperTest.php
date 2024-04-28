<?php

declare(strict_types=1);

namespace Macocci7\PurephpValidation;

use Macocci7\PurephpValidation\Rules\PasswordWrapper;
use Macocci7\PurephpValidation\ValidatorFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
final class PasswordWrapperTest extends TestCase
{
    public static function provide_password_rule_can_work_correctly(): array
    {
        return [
            [
                'input' => [
                    'data' => [
                        'password' => 'passwor',
                    ],
                    'rules' => [
                        'password' => [PasswordWrapper::min(8)],
                    ],
                ],
                'expected' => [
                    'fails' => true,
                    'messages' => [
                        'password' => ['The password field must be at least 8 characters.'],
                    ],
                ],
            ],
            [
                'input' => [
                    'data' => [
                        'password' => 'password',
                    ],
                    'rules' => [
                        'password' => [PasswordWrapper::min(8)->max(16)],
                    ],
                ],
                'expected' => [
                    'fails' => false,
                    'messages' => [
                    ],
                ],
            ],
            [
                'input' => [
                    'data' => [
                        'password' => 'passwordpasswordpassword',
                    ],
                    'rules' => [
                        'password' => [PasswordWrapper::min(8)->max(16)],
                    ],
                ],
                'expected' => [
                    'fails' => true,
                    'messages' => [
                        'password' => ['The password field must not be greater than 16 characters.'],
                    ],
                ],
            ],
            [
                'input' => [
                    'data' => [
                        'password' => 'password',
                    ],
                    'rules' => [
                        'password' => [
                            PasswordWrapper::min(8)
                            ->max(16)
                            ->letters()
                            ->mixedCase()
                            ->numbers()
                            ->symbols()
                            ->uncompromised(),
                        ],
                    ],
                ],
                'expected' => [
                    'fails' => true,
                    'messages' => [
                        'password' => [
                            'The password field must contain at least one uppercase and one lowercase letter.',
                            'The password field must contain at least one symbol.',
                            'The password field must contain at least one number.',
                        ],
                    ],
                ],
            ],
        ];
    }

    #[DataProvider('provide_password_rule_can_work_correctly')]
    public function test_password_rule_can_work_correctly(array $input, array $expected): void
    {
        $validator = ValidatorFactory::make($input['data'], $input['rules']);
        $this->assertSame($expected['fails'], $validator->fails());
        if ($validator->fails()) {
            $this->assertSame($expected['messages'], $validator->errors()->messages());
        }
    }
}
