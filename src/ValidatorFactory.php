<?php

namespace Macocci7\PurephpValidation;

class ValidatorFactory
{
    private static string $lang = 'en';

    private static string $basePath = '';

    /**
     * returns the file path of the caller
     * @return  string
     */
    private static function getCallerFilePath()
    {
        return debug_backtrace()[1]['file'];
    }

    /**
     * returns normalized path
     * @param   string  $path
     * @param   string  $basePath
     * @return  string
     */
    private static function normalizePath(string $path, string $basePath)
    {
        if (str_starts_with($path, '/')) {
            return realpath($path) . '/';
        }
        return realpath($basePath . $path) . '/';
    }

    /**
     * sets the basePath
     * @param   string  $path = __DIR__ . '/'
     * @return  void
     */
    public static function translationsRootPath(string $path = __DIR__ . '/')
    {
        if (!empty($path)) {
            self::$basePath = self::normalizePath(
                $path,
                dirname(self::getCallerFilePath()) . '/'
            );
        }
    }

    /**
     * Sets lang or returns current lang
     * @param   string  $lang = ''
     * @return  string|null
     */
    public static function lang(string $lang = '')
    {
        if (strlen($lang) === 0) {
            return self::$lang;
        }
        if (strlen(self::$basePath) === 0) {
            self::translationsRootPath();
        }
        $path = self::$basePath . 'lang/' . $lang . '/validation.php';
        if (!is_readable($path)) {
            throw new \Exception("Cannot read {$path}.");
        }
        return self::$lang = $lang;
    }

    /**
     * Creates Validator
     * @param   array<string, mixed>    $data
     * @param   array<string, string>   $rule
     * @param   array<string, string>   $messages = []
     * @return  \Illuminate\Validation\Validator
     */
    public static function make(
        array $data,
        array $rule,
        array $messages = []
    ) {
        if (strlen(self::$basePath) === 0) {
            self::translationsRootPath();
        }
        // @phpstan-ignore-next-line
        return (new ValidatorWrapper(
            lang: self::$lang
        ))
        ->translationsRootPath(self::$basePath)
        ->make($data, $rule, $messages);
    }
}
