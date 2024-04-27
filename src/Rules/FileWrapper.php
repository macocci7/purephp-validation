<?php

namespace Macocci7\PurephpValidation\Rules;

use Illuminate\Validation\Rules\File;
use Macocci7\PurephpValidation\ValidatorFactory;

class FileWrapper extends File
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string   $attribute
     * @param  mixed    $value
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function passes($attribute, $value) // @phpstan-ignore-line
    {
        $this->messages = [];

        $validator = ValidatorFactory::make(
            $this->data,
            [$attribute => $this->buildValidationRules()], // @phpstan-ignore-line
            $this->validator->customMessages,
            $this->validator->customAttributes
        );

        if ($validator->fails()) {
            return $this->fail($validator->messages()->all());
        }

        return true;
    }

    /**
     * Limit the uploaded file to only image types.
     *
     * @return ImageFileWrapper
     */
    public static function image()
    {
        return new ImageFileWrapper();
    }
}
