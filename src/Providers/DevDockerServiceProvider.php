<?php

namespace VexerVox\DevDocker\Providers;

use Illuminate\Support\ServiceProvider;
use VexerVox\DevDocker\Console\InstallCommand;
use VexerVox\DevDocker\Console\UpdateCommand;

class DevDockerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                UpdateCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__ . '/../../docker/' => base_path('docker'),
        ], 'docker-folder');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {

    }
}
