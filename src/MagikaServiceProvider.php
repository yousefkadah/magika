<?php

namespace YousefKadah\LaravelMagika;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rule;
use YousefKadah\LaravelMagika\Rules\MagikaFileType;
use YousefKadah\LaravelMagika\Rules\MagikaGroup;
use YousefKadah\LaravelMagika\Rules\MagikaMimeType;

class MagikaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/magika.php', 'magika');

        $this->app->singleton(Magika::class, function ($app) {
            return new Magika(
                binaryPath: config('magika.binary_path'),
                predictionMode: config('magika.prediction_mode'),
                timeout: config('magika.timeout'),
            );
        });

        $this->app->alias(Magika::class, 'magika');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/magika.php' => config_path('magika.php'),
            ], 'magika-config');
        }

        $this->registerValidationRules();
    }

    protected function registerValidationRules(): void
    {
        $this->app['validator']->extend('magika', function ($attribute, $value, $parameters, $validator) {
            if (! $value instanceof \Illuminate\Http\UploadedFile) {
                return false;
            }

            try {
                $magika = app(Magika::class);
                $result = $magika->detectUploadedFile($value);

                return $result->isOk() && $result->matchesLabel($parameters);
            } catch (\Throwable) {
                return false;
            }
        });

        $this->app['validator']->replacer('magika', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':types', implode(', ', $parameters), $message);
        });

        $this->app['validator']->extend('magika_mime', function ($attribute, $value, $parameters, $validator) {
            if (! $value instanceof \Illuminate\Http\UploadedFile) {
                return false;
            }

            try {
                $magika = app(Magika::class);
                $result = $magika->detectUploadedFile($value);

                return $result->isOk() && $result->matchesMimeType($parameters);
            } catch (\Throwable) {
                return false;
            }
        });

        $this->app['validator']->replacer('magika_mime', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':types', implode(', ', $parameters), $message);
        });

        $this->app['validator']->extend('magika_group', function ($attribute, $value, $parameters, $validator) {
            if (! $value instanceof \Illuminate\Http\UploadedFile) {
                return false;
            }

            try {
                $magika = app(Magika::class);
                $result = $magika->detectUploadedFile($value);

                return $result->isOk() && $result->matchesGroup($parameters);
            } catch (\Throwable) {
                return false;
            }
        });

        $this->app['validator']->replacer('magika_group', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':types', implode(', ', $parameters), $message);
        });
    }
}
