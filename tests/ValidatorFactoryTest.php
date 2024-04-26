<?php

declare(strict_types=1);

namespace Macocci7\PurephpValidation;

use Macocci7\PurephpValidation\ValidatorFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
final class ValidatorFactoryTest extends TestCase
{
    public static function provide_translationsRootPath_can_set_path_correctly(): array
    {
        return [
            'default' => [ 'path' => __DIR__ . '/../src/' ],
            'example' => [ 'path' => __DIR__ . '/../example/' ],
        ];
    }

    #[DataProvider('provide_translationsRootPath_can_set_path_correctly')]
    public function test_translationsRootPath_can_set_path_correctly(string $path): void
    {
        ValidatorFactory::translationsRootPath($path);
        $r = new \ReflectionClass(ValidatorFactory::class);
        $p = $r->getProperty('basePath');
        $p->setAccessible(true);
        $this->assertSame(realpath($path) . '/', $p->getValue());
    }

    public static function provide_lang_can_set_lang_correctly(): array
    {
        return [
            [ 'lang' => 'en', ],
            [ 'lang' => 'ja', ],
        ];
    }

    #[DataProvider('provide_lang_can_set_lang_correctly')]
    public function test_lang_can_set_lang_correctly($lang): void
    {
        ValidatorFactory::translationsRootPath();
        ValidatorFactory::lang($lang);
        $this->assertSame($lang, ValidatorFactory::lang());
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public static function provide_make_can_make_validator_correctly(): array
    {
        $rule = [
            'id' => 'required|int|min:1',
            'name' => 'required|string|min:3|max:10',
            'email' => 'required|string|email:rfc,dns',
        ];
        return [
            [
                'input' => [
                    'lang' => 'en',
                    'data' => [
                        'id' => 'a',
                        'name' => null,
                        'email' => '',
                    ],
                    'rule' => $rule,
                    'messages' => [],
                ],
                'expected' => [
                    'fails' => true,
                    'errors' => [
                        'id' => ['The id must be an integer.'],
                        'name' => ['The name field is required.'],
                        'email' => ['The email field is required.'],
                    ],
                ],
            ],
            [
                'input' => [
                    'lang' => 'en',
                    'data' => [
                        'id' => 0,
                        'name' => 'ho',
                        'email' => 'hoge',
                    ],
                    'rule' => $rule,
                    'messages' => [],
                ],
                'expected' => [
                    'fails' => true,
                    'errors' => [
                        'id' => ['The id must be at least 1.'],
                        'name' => ['The name must be at least 3 characters.'],
                        'email' => ['The email must be a valid email address.'],
                    ],
                ],
            ],
            [
                'input' => [
                    'lang' => 'en',
                    'data' => [
                        'id' => 1,
                        'name' => 'hogehogehog',
                        'email' => 'hoge@gmail.com',
                    ],
                    'rule' => $rule,
                    'messages' => [],
                ],
                'expected' => [
                    'fails' => true,
                    'errors' => [
                        'name' => ['The name may not be greater than 10 characters.'],
                    ],
                ],
            ],
            [
                'input' => [
                    'lang' => 'en',
                    'data' => [
                        'id' => 1,
                        'name' => 'hogehogeho',
                        'email' => 'hoge@gmail.com',
                    ],
                    'rule' => $rule,
                    'messages' => [],
                ],
                'expected' => [
                    'fails' => false,
                    'errors' => [],
                ],
            ],
            [
                'input' => [
                    'lang' => 'ja',
                    'data' => [
                        'id' => 'a',
                        'name' => null,
                        'email' => '',
                    ],
                    'rule' => $rule,
                    'messages' => [],
                ],
                'expected' => [
                    'fails' => true,
                    'errors' => [
                        'id' => ['idは整数で指定してください。'],
                        'name' => ['nameは必ず指定してください。'],
                        'email' => ['emailは必ず指定してください。'],
                    ],
                ],
            ],
            [
                'input' => [
                    'lang' => 'ja',
                    'data' => [
                        'id' => 0,
                        'name' => 'ho',
                        'email' => 'hoge',
                    ],
                    'rule' => $rule,
                    'messages' => [],
                ],
                'expected' => [
                    'fails' => true,
                    'errors' => [
                        'id' => ['idには、1以上の数字を指定してください。'],
                        'name' => ['nameは、3文字以上で指定してください。'],
                        'email' => ['emailには、有効なメールアドレスを指定してください。'],
                    ],
                ],
            ],
            [
                'input' => [
                    'lang' => 'ja',
                    'data' => [
                        'id' => 1,
                        'name' => 'hogehogehog',
                        'email' => 'hoge@gmail.com',
                    ],
                    'rule' => $rule,
                    'messages' => [],
                ],
                'expected' => [
                    'fails' => true,
                    'errors' => [
                        'name' => ['nameは、10文字以下で指定してください。'],
                    ],
                ],
            ],
        ];
    }

    #[DataProvider('provide_make_can_make_validator_correctly')]
    public function test_make_can_make_validator_correctly(array $input, array $expected): void
    {
        ValidatorFactory::lang($input['lang']);
        $validator = ValidatorFactory::make(
            $input['data'],
            $input['rule'],
            $input['messages']
        );
        $this->assertSame(
            \Illuminate\Validation\Validator::class,
            $validator::class
        );
        $this->assertSame($expected['fails'], $validator->fails());
        if ($validator->fails()) {
            $this->assertSame(
                $expected['errors'],
                $validator->errors()->messages()
            );
        }
    }
}
