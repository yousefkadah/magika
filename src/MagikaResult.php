<?php

namespace YousefKadah\LaravelMagika;

class MagikaResult
{
    public function __construct(
        public readonly string $path,
        public readonly string $label,
        public readonly string $mimeType,
        public readonly string $group,
        public readonly string $description,
        public readonly float $score,
        public readonly bool $isText,
        public readonly array $extensions,
        public readonly string $status,
    ) {}

    public static function fromJson(array $data): self
    {
        $result = $data['result'] ?? [];
        $status = $result['status'] ?? 'unknown';
        $value = $result['value']['output'] ?? $result['value']['dl'] ?? [];

        return new self(
            path: $data['path'] ?? '',
            label: $value['label'] ?? 'unknown',
            mimeType: $value['mime_type'] ?? 'application/octet-stream',
            group: $value['group'] ?? 'unknown',
            description: $value['description'] ?? 'Unknown',
            score: $result['value']['score'] ?? 0.0,
            isText: $value['is_text'] ?? false,
            extensions: $value['extensions'] ?? [],
            status: $status,
        );
    }

    public function isOk(): bool
    {
        return $this->status === 'ok';
    }

    public function matchesLabel(string|array $labels): bool
    {
        $labels = is_array($labels) ? $labels : [$labels];

        return in_array($this->label, $labels, true);
    }

    public function matchesMimeType(string|array $mimeTypes): bool
    {
        $mimeTypes = is_array($mimeTypes) ? $mimeTypes : [$mimeTypes];

        return in_array($this->mimeType, $mimeTypes, true);
    }

    public function matchesGroup(string|array $groups): bool
    {
        $groups = is_array($groups) ? $groups : [$groups];

        return in_array($this->group, $groups, true);
    }
}
