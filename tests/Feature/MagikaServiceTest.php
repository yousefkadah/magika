<?php

namespace YousefKadah\LaravelMagika\Tests\Feature;

use YousefKadah\LaravelMagika\Magika;
use YousefKadah\LaravelMagika\Tests\TestCase;

class MagikaServiceTest extends TestCase
{
    public function test_magika_is_bound_in_container(): void
    {
        $magika = app(Magika::class);

        $this->assertInstanceOf(Magika::class, $magika);
    }

    public function test_magika_is_singleton(): void
    {
        $magika1 = app(Magika::class);
        $magika2 = app(Magika::class);

        $this->assertSame($magika1, $magika2);
    }

    public function test_config_is_merged(): void
    {
        $this->assertNotNull(config('magika'));
        $this->assertSame('high-confidence', config('magika.prediction_mode'));
        $this->assertSame(30, config('magika.timeout'));
    }

    public function test_validation_rule_is_registered(): void
    {
        $validator = app('validator');
        $rules = $validator->make([], ['test' => 'magika:pdf']);

        $this->assertNotNull($rules);
    }
}
