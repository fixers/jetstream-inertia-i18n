<?php

namespace Fixers\JetstreamI18n\Providers;

use Fixers\JetstreamI18n\Console\Commands\PublishComponents;
use Illuminate\Support\ServiceProvider;

class TranslatableServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PublishComponents::class,
            ]);
        }

        $this->publishes([
            __DIR__ . '/../../stubs/js/Components' => resource_path('js/Components'),
            __DIR__ . '/../../stubs/js/Layouts' => resource_path('js/Layouts'),
            __DIR__ . '/../../stubs/js/Pages' => resource_path('js/Pages'),
        ], 'fixers-jetstream-i18n');

    }
}

