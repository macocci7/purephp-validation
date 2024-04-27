<?php

namespace Macocci7\PurephpValidation\Rules;

use Macocci7\PurephpValidation\Rules\FileWrapper;

class ImageFileWrapper extends FileWrapper
{
    /**
     * Create a new image file rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->rules('image');
    }

    /**
     * The dimension constraints for the uploaded file.
     *
     * @param  \Illuminate\Validation\Rules\Dimensions  $dimensions
     */
    public function dimensions($dimensions)
    {
        $this->rules($dimensions);

        return $this;
    }
}
