<?php

namespace YousefKadah\LaravelMagika\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use YousefKadah\LaravelMagika\Magika;

class MagikaFileType implements ValidationRule
{
    protected array $allowedLabels;

    public function __construct(string|array $labels)
    {
        $this->allowedLabels = is_array($labels) ? $labels : [$labels];
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

            if (! $result->isOk() || ! $result->matchesLabel($this->allowedLabels)) {
                $fail('The :attribute must be a file of type: ' . implode(', ', $this->allowedLabels) . '.');
            }
        } catch (\Throwable $e) {
            $fail('Unable to validate the file type for :attribute.');
        }
    }
}
