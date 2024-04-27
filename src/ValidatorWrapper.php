<?php

/**
 * This file is a modified version of the following code:
 * - BaseCode: https://github.com/jeffochoa/validator-factory/blob/master/src/ValidatorFactory.php
 * - BaseTag:  https://github.com/jeffochoa/validator-factory/releases/tag/1.0.1
 */

 namespace Macocci7\PurephpValidation;

use Illuminate\Validation;
use Illuminate\Translation;
use Illuminate\Validation\Factory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;

class ValidatorWrapper
{
    public string $lang;
    public string $group;
    public Factory $factory;
    public string $namespace;

    // Translations root directory
    public string $basePath;

    public static Translator $translator;

    /**
     * constructor
     * @param   string  $namespace
     * @param   string  $lang
     * @param   string  $group
     */
    public function __construct(
        string $namespace = 'lang',
        string $lang = 'en',
        string $group = 'validation'
    ) {
        $this->lang = $lang;
        $this->group = $group;
        $this->namespace = $namespace;
        $this->basePath = $this->getTranslationsRootPath();
        $this->factory = new Factory($this->loadTranslator());
    }

    /**
     * Sets tranlations root path
     * @param   string  $path = ''
     * @return  $this
     */
    public function translationsRootPath(string $path = '')
    {
        if (!empty($path)) {
            $this->basePath = $path;
            $this->reloadValidatorFactory();
        }
        return $this;
    }

    /**
     * Reloads ValidatorFactory
     * @return  $this
     */
    private function reloadValidatorFactory()
    {
        $this->factory = new Factory($this->loadTranslator());
        return $this;
    }

    /**
     * Returns translations root path
     * @return  string
     */
    public function getTranslationsRootPath(): string
    {
        return __DIR__ . '/';
    }

    /**
     * Loads and returns Translator
     * @return  Translator
     */
    public function loadTranslator(): Translator
    {
        $loader = new FileLoader(
            new Filesystem(),
            $this->basePath . $this->namespace
        );
        $loader->addNamespace(
            $this->namespace,
            $this->basePath . $this->namespace
        );
        $loader->load($this->lang, $this->group, $this->namespace);
        return static::$translator = new Translator($loader, $this->lang);
    }

    /**
     * Method overloading
     * @param   string  $method
     * @param   array<string, string>   $args
     * @return  mixed|false
     * @see https://www.php.net/manual/en/language.oop5.overloading.php#object.call
     */
    public function __call(string $method, array $args)
    {
        return call_user_func_array([$this->factory, $method], $args);
    }
}
