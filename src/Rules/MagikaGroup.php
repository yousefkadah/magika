<?php

namespace YousefKadah\LaravelMagika\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use YousefKadah\LaravelMagika\Magika;

class MagikaGroup implements ValidationRule
{
    protected array $allowedGroups;

    public function __construct(string|array $groups)
    {
        $this->allowedGroups = is_array($groups) ? $groups : [$groups];
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

            if (! $result->isOk() || ! $result->matchesGroup($this->allowedGroups)) {
                $fail('The :attribute must be a file in group: ' . implode(', ', $this->allowedGroups) . '.');
            }
        } catch (\Throwable $e) {
            $fail('Unable to validate the file type for :attribute.');
        }
    }
}
