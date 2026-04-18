<?php

namespace YousefKadah\LaravelMagika\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use YousefKadah\LaravelMagika\MagikaServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            MagikaServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Magika' => \YousefKadah\LaravelMagika\Facades\Magika::class,
        ];
    }
}
