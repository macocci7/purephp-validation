<?php

declare(strict_types=1);

namespace Macocci7\PurephpValidation;

use Illuminate\Validation\Rule;
use Macocci7\PurephpValidation\Rules\Instance;
use Macocci7\PurephpValidation\ValidatorFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
final class InstanceTest extends TestCase
{
    public static function provide_of_can_work_correctly(): array
    {
        return [
            [
                'input' => [
                    'lang' => 'en',
                    'data' => [
                        'prop1' => new Instance([]),
                        'prop2' => 'Instance',
                        'prop3' => fn () => true,
                    ],
                    'rules' => [
                        'prop1' => Instance::of(Instance::class),
                        'prop2' => Instance::of([
                            Instance::class,
                            ValidatorFactory::class,
                            (fn () => true)::class,
                        ]),
                        'prop3' => Instance::of('Closure'),
                    ],
                ],
                'expected' => [
                    'fails' => true,
                    'messages' => [
                        'prop2' => ['The prop2 must be an instance of: Macocci7\PurephpValidation\Rules\Instance, Macocci7\PurephpValidation\ValidatorFactory, Closure.'],
                    ],
                ],
            ],
            [
                'input' => [
                    'lang' => 'ja',
                    'data' => [
                        'prop1' => new Instance([]),
                        'prop2' => 'Instance',
                        'prop3' => fn () => true,
                    ],
                    'rules' => [
                        'prop1' => Instance::of(Instance::class),
                        'prop2' => Instance::of([
                            Instance::class,
                            ValidatorFactory::class,
                            (fn () => true)::class,
                        ]),
                        'prop3' => Instance::of('Closure'),
                    ],
                ],
                'expected' => [
                    'fails' => true,
                    'messages' => [
                        'prop2' => ['prop2は次のいずれかのインスタンスを指定してください: Macocci7\PurephpValidation\Rules\Instance, Macocci7\PurephpValidation\ValidatorFactory, Closure.'],
                    ],
                ],
            ],
        ];
    }

    #[DataProvider('provide_of_can_work_correctly')]
    public function test_of_can_work_correctly(array $input, array $expected): void
    {
        ValidatorFactory::lang($input['lang']);
        $validator = ValidatorFactory::make($input['data'], $input['rules']);
        $this->assertSame($expected['fails'], $validator->fails());
        if ($validator->fails()) {
            $this->assertSame($expected['messages'], $validator->errors()->messages());
        }
    }
}
