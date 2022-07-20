<?php

namespace VexerVox\DevDocker\Providers;

use Illuminate\Support\ServiceProvider;

class DevDockerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
//        $this->publishes([
//            __DIR__.'/../config/dev-docker.php' => config_path('dev-docker.php'),
//        ]);
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
