<?php

namespace Macocci7\PurephpValidation\Rules;

use Illuminate\Validation\Rules\Password;
use Macocci7\PurephpValidation\ValidatorFactory;

class PasswordWrapper extends Password
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function passes($attribute, $value)
    {
        $this->messages = [];

        $validator = ValidatorFactory::make(
            $this->data,
            [$attribute => [
                'string',
                'min:' . $this->min,
                ...($this->max ? ['max:' . $this->max] : []),
                ...$this->customRules,
            ]],
            $this->validator->customMessages,
            $this->validator->customAttributes
        )->after(function ($validator) use ($attribute, $value) {
            if (! is_string($value)) {
                return;
            }

            if (
                $this->mixedCase
                && ! preg_match(
                    '/(\p{Ll}+.*\p{Lu})|(\p{Lu}+.*\p{Ll})/u',
                    $value
                )
            ) {
                $validator->addFailure($attribute, 'password.mixed');
            }

            if ($this->letters && ! preg_match('/\pL/u', $value)) {
                $validator->addFailure($attribute, 'password.letters');
            }

            if (
                $this->symbols && ! preg_match('/\p{Z}|\p{S}|\p{P}/u', $value)
            ) {
                $validator->addFailure($attribute, 'password.symbols');
            }

            if ($this->numbers && ! preg_match('/\pN/u', $value)) {
                $validator->addFailure($attribute, 'password.numbers');
            }
        });

        if ($validator->fails()) {
            return $this->fail($validator->messages()->all());
        }

        if (
            $this->uncompromised
            && ! Container::getInstance()
                ->make(UncompromisedVerifier::class)
                ->verify([
                    'value' => $value,
                    'threshold' => $this->compromisedThreshold,
            ])
        ) {
            $validator->addFailure($attribute, 'password.uncompromised');

            return $this->fail($validator->messages()->all());
        }

        return true;
    }
}
