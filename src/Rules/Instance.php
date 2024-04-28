<?php

namespace Macocci7\PurephpValidation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Instance implements ValidationRule
{
    /**
     * @var string[]    $classes
     */
    protected array $classes = [];

    /**
     * constructor
     *
     * @param   string[]    $classes
     */
    public function __construct(array $classes)
    {
        $this->classes = $classes;
    }

    /**
     * Run the validation rule.
     *
     * @param   string  $attribute
     * @param   mixed   $value
     * @param   Closure $fail
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $matched = false;
        foreach ($this->classes as $class) {
            if ($value instanceof $class) {
                $matched = true;
                break;
            }
        }
        if (!$matched) {
            $fail('validation.instance')->translate([
                'classes' => implode(', ', $this->classes),
            ]);
        }
    }

    /**
     * Sets class names and returns an instance of this class
     *
     * @param   string|string[] $class
     * @return  Instance
     */
    public static function of(string|array $class)
    {
        return new static(is_string($class) ? [$class] : $class);
    }
}
