<?php

namespace YousefKadah\LaravelMagika\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use YousefKadah\LaravelMagika\Magika;

class MagikaMimeType implements ValidationRule
{
    protected array $allowedMimeTypes;

    public function __construct(string|array $mimeTypes)
    {
        $this->allowedMimeTypes = is_array($mimeTypes) ? $mimeTypes : [$mimeTypes];
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile) {
            $fail('The :attribute must be a file.');
            return;
        }

        try {
            $magika = app(Magika::class);
            $result = $magika->detectUploadedFile($value);

            if (! $result->isOk() || ! $result->matchesMimeType($this->allowedMimeTypes)) {
                $fail('The :attribute must be a file of MIME type: ' . implode(', ', $this->allowedMimeTypes) . '.');
            }
        } catch (\Throwable $e) {
            $fail('Unable to validate the file type for :attribute.');
        }
    }
}
