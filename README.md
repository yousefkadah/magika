<p align="center">
  <img src="art/logo.svg" alt="Laravel Magika" width="480">
</p>

<p align="center">
  <a href="https://packagist.org/packages/yousefkadah/laravel-magika"><img src="https://img.shields.io/packagist/v/yousefkadah/laravel-magika.svg?style=flat-square" alt="Latest Version"></a>
  <a href="https://packagist.org/packages/yousefkadah/laravel-magika"><img src="https://img.shields.io/packagist/dt/yousefkadah/laravel-magika.svg?style=flat-square" alt="Total Downloads"></a>
  <a href="https://github.com/yousefkadah/laravel-magika/blob/main/LICENSE"><img src="https://img.shields.io/packagist/l/yousefkadah/laravel-magika.svg?style=flat-square" alt="License"></a>
</p>

---

AI-powered file type validation for Laravel using [Google's Magika](https://github.com/google/magika) — a deep learning model (~1 MB) that achieves ~99% accuracy across 200+ content types.

Unlike traditional MIME-type checking (which relies on file extensions or magic bytes), Magika uses a trained neural network to accurately identify file content, making it significantly harder to bypass with spoofed extensions or headers.

## Requirements

- PHP 8.1+
- Laravel 10, 11, or 12
- [Magika CLI](https://github.com/google/magika) installed on your system

### Installing the Magika CLI

Choose one:

```bash
# macOS / Linux (Homebrew)
brew install magika

# Python (pipx)
pipx install magika

# Rust (Cargo)
cargo install --locked magika-cli
```

## Installation

```bash
composer require yousefkadah/laravel-magika
```

Publish the configuration (optional):

```bash
php artisan vendor:publish --tag=magika-config
```

## Configuration

```php
// config/magika.php
return [
    'binary_path'     => env('MAGIKA_BINARY_PATH', null),     // null = use system PATH
    'prediction_mode' => env('MAGIKA_PREDICTION_MODE', 'high-confidence'),
    'timeout'         => env('MAGIKA_TIMEOUT', 30),
];
```

## Usage

### Detecting File Types

```php
use YousefKadah\LaravelMagika\Facades\Magika;

// Detect a file by path
$result = Magika::detect('/path/to/file.pdf');

$result->label;       // "pdf"
$result->mimeType;    // "application/pdf"
$result->group;       // "document"
$result->description; // "PDF document"
$result->score;       // 0.99
$result->isText;      // false
$result->extensions;  // ["pdf"]

// Detect from raw content
$result = Magika::detectContent('<?php echo "hello";');
$result->label; // "php"

// Detect an uploaded file
$result = Magika::detectUploadedFile($request->file('document'));

// Batch detection
$results = Magika::detectMany(['/path/to/file1.pdf', '/path/to/file2.jpg']);
```

### Validation Rules

#### By Magika Label

Validate that an uploaded file matches specific Magika content type labels:

```php
// Using string rule (in controller or form request)
$request->validate([
    'document' => 'required|file|magika:pdf,docx,xlsx',
]);

// Using invokable rule
use YousefKadah\LaravelMagika\Rules\MagikaFileType;

$request->validate([
    'document' => ['required', 'file', new MagikaFileType(['pdf', 'docx', 'xlsx'])],
]);
```

#### By MIME Type

```php
// Using string rule
$request->validate([
    'image' => 'required|file|magika_mime:image/png,image/jpeg',
]);

// Using invokable rule
use YousefKadah\LaravelMagika\Rules\MagikaMimeType;

$request->validate([
    'image' => ['required', 'file', new MagikaMimeType(['image/png', 'image/jpeg'])],
]);
```

#### By Group

```php
// Using string rule
$request->validate([
    'upload' => 'required|file|magika_group:document,image',
]);

// Using invokable rule
use YousefKadah\LaravelMagika\Rules\MagikaGroup;

$request->validate([
    'upload' => ['required', 'file', new MagikaGroup(['document', 'image'])],
]);
```

### Checking Installation

```php
use YousefKadah\LaravelMagika\Facades\Magika;

Magika::isInstalled(); // true/false
Magika::version();     // "magika 0.1.0-rc.3 (model standard_v3_3)"
```

### MagikaResult API

| Property      | Type     | Description                                  |
|---------------|----------|----------------------------------------------|
| `path`        | string   | File path that was analyzed                  |
| `label`       | string   | Content type label (e.g. `pdf`, `python`)    |
| `mimeType`    | string   | MIME type (e.g. `application/pdf`)           |
| `group`       | string   | Group category (e.g. `document`, `code`)     |
| `description` | string   | Human-readable description                   |
| `score`       | float    | Confidence score (0.0 - 1.0)                |
| `isText`      | bool     | Whether the content is text-based            |
| `extensions`  | array    | Possible file extensions                     |
| `status`      | string   | Detection status (`ok` or error)             |

| Method                          | Returns | Description                       |
|---------------------------------|---------|-----------------------------------|
| `isOk()`                        | bool    | Whether detection succeeded       |
| `matchesLabel(string\|array)`   | bool    | Check against label(s)            |
| `matchesMimeType(string\|array)`| bool    | Check against MIME type(s)        |
| `matchesGroup(string\|array)`   | bool    | Check against group(s)            |

### Common Magika Labels

| Label    | MIME Type                | Group    |
|----------|--------------------------|----------|
| `pdf`    | `application/pdf`        | document |
| `png`    | `image/png`              | image    |
| `jpeg`   | `image/jpeg`             | image    |
| `gif`    | `image/gif`              | image    |
| `docx`   | `application/vnd.openxmlformats-officedocument.wordprocessingml.document` | document |
| `xlsx`   | `application/vnd.openxmlformats-officedocument.spreadsheetml.sheet` | document |
| `zip`    | `application/zip`        | archive  |
| `python` | `text/x-python`          | code     |
| `javascript` | `text/javascript`    | code     |
| `html`   | `text/html`              | code     |
| `csv`    | `text/csv`               | code     |
| `json`   | `application/json`       | code     |
| `xml`    | `text/xml`               | code     |
| `mp3`    | `audio/mpeg`             | audio    |
| `mp4`    | `video/mp4`              | video    |

For the full list of 200+ supported types, see the [Magika content types documentation](https://github.com/google/magika/blob/main/assets/models/standard_v3_3/README.md).

## Why Use Magika Over Standard Validation?

Laravel's built-in `mimes` and `mimetypes` rules rely on file extensions and PHP's `finfo` (which uses libmagic). These can be easily tricked:

- Renaming `malware.exe` to `malware.pdf` bypasses extension checks
- Crafted files can fool magic-byte detection

Magika uses a deep learning model trained on ~100M files, analyzing actual file content patterns — making it far more robust against evasion.

## License

MIT License. See [LICENSE](LICENSE) for details.
