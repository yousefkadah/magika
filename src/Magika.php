<?php

namespace YousefKadah\LaravelMagika;

use Illuminate\Support\Collection;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use YousefKadah\LaravelMagika\Exceptions\MagikaException;
use YousefKadah\LaravelMagika\Exceptions\MagikaNotFoundException;

class Magika
{
    protected string $binaryPath;
    protected string $predictionMode;
    protected int $timeout;

    public function __construct(?string $binaryPath = null, ?string $predictionMode = null, ?int $timeout = null)
    {
        $this->binaryPath = $binaryPath ?? config('magika.binary_path') ?? 'magika';
        $this->predictionMode = $predictionMode ?? config('magika.prediction_mode', 'high-confidence');
        $this->timeout = $timeout ?? config('magika.timeout', 30);
    }

    public function detect(string $path): MagikaResult
    {
        $results = $this->detectMany([$path]);

        return $results->first();
    }

    public function detectMany(array $paths): Collection
    {
        foreach ($paths as $path) {
            if (! file_exists($path)) {
                throw new MagikaException("File not found: {$path}");
            }
        }

        $command = array_merge(
            [$this->binaryPath],
            ['--json'],
            $paths,
        );

        $process = new Process($command);
        $process->setTimeout($this->timeout);

        try {
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            if (str_contains($e->getMessage(), 'not found') || str_contains($e->getMessage(), 'No such file')) {
                throw new MagikaNotFoundException(
                    'Magika CLI not found. Install it via: brew install magika, pipx install magika, or cargo install --locked magika-cli'
                );
            }

            throw new MagikaException("Magika process failed: {$e->getMessage()}");
        }

        $output = $process->getOutput();
        $data = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new MagikaException('Failed to parse Magika output: ' . json_last_error_msg());
        }

        return collect($data)->map(fn (array $item) => MagikaResult::fromJson($item));
    }

    public function detectContent(string $content): MagikaResult
    {
        $command = [$this->binaryPath, '--json', '-'];

        $process = new Process($command);
        $process->setTimeout($this->timeout);
        $process->setInput($content);

        try {
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            if (str_contains($e->getMessage(), 'not found') || str_contains($e->getMessage(), 'No such file')) {
                throw new MagikaNotFoundException(
                    'Magika CLI not found. Install it via: brew install magika, pipx install magika, or cargo install --locked magika-cli'
                );
            }

            throw new MagikaException("Magika process failed: {$e->getMessage()}");
        }

        $output = $process->getOutput();
        $data = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new MagikaException('Failed to parse Magika output: ' . json_last_error_msg());
        }

        $items = is_array($data) ? $data : [$data];

        return MagikaResult::fromJson($items[0]);
    }

    public function detectUploadedFile(\Illuminate\Http\UploadedFile $file): MagikaResult
    {
        return $this->detect($file->getRealPath());
    }

    public function isInstalled(): bool
    {
        try {
            $process = new Process([$this->binaryPath, '--version']);
            $process->setTimeout(5);
            $process->run();

            return $process->isSuccessful();
        } catch (\Throwable) {
            return false;
        }
    }

    public function version(): ?string
    {
        try {
            $process = new Process([$this->binaryPath, '--version']);
            $process->setTimeout(5);
            $process->mustRun();

            return trim($process->getOutput());
        } catch (\Throwable) {
            return null;
        }
    }
}
