<?php

namespace YousefKadah\LaravelMagika\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \YousefKadah\LaravelMagika\MagikaResult detect(string $path)
 * @method static \Illuminate\Support\Collection detectMany(array $paths)
 * @method static \YousefKadah\LaravelMagika\MagikaResult detectContent(string $content)
 * @method static \YousefKadah\LaravelMagika\MagikaResult detectUploadedFile(\Illuminate\Http\UploadedFile $file)
 * @method static bool isInstalled()
 * @method static string|null version()
 *
 * @see \YousefKadah\LaravelMagika\Magika
 */
class Magika extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \YousefKadah\LaravelMagika\Magika::class;
    }
}
