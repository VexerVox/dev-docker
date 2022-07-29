<?php

namespace VexerVox\DevDocker\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'devdocker:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update DevDocker published files';

    /**
     * composer.json file data
     *
     * @var array
     */
    protected array $composerFile;

    /**
     * @var string
     */
    protected string $packageVersion;

    /**
     * @var string
     */
    protected string $phpVersion;

    public function handle(): int
    {
        $this->info('Updating docker-compose.yaml...');
        $this->updateDockerCompose();
        $this->info('Updated docker-compose.yaml');

        $this->info('Publishing configuration...');
        $this->publishConfiguration();
        $this->info('Published configuration');

        return 1;
    }

    private function updateDockerCompose()
    {
        $composer = json_decode(File::get(base_path('composer.json')), true);
        $dockerCompose = File::get(base_path('docker-compose.yml'));

        $packageVersion = $this->getPackageVersion($composer);
        $phpVersion = $this->getPhpVersion($dockerCompose);

        $newDockerCompose = preg_replace(
            '/\"vexervox\/php:.*\"/',
            "\"vexervox/php:$phpVersion-$packageVersion\"",
            $dockerCompose
        );

        File::put(base_path('docker-compose.yml'), $newDockerCompose);
    }

    /**
     * Get DevDocker version from composer.json
     *
     * @param array $composer
     * @return string
     */
    private function getPackageVersion(array $composer = []): string
    {
        if (empty($composer)) {
            $composer = json_decode(File::get(base_path('composer.json')), true);
        }

        if (empty($composer)) {
            return 'unknown';
        }

        $version = $composer['require']['vexervox/dev-docker'] ?? $composer['require-dev']['vexervox/dev-docker'] ?? 'unknown';

        return preg_replace('/[^0-9a-zA-Z.\-]/', '',  $version);
    }

    /**
     * @param string $dockerCompose
     * @return string
     */
    private function getPhpVersion(string $dockerCompose): string
    {
        $matches = [];

        preg_match(
            '/DEVDOCKER_PHP_VERSION=[\"\'].*[\"\']/',
            $dockerCompose,
            $matches
        );

        if (empty($matches)) {
            return 'unknown';
        }

        preg_match(
            '/[\"\'].*[\"\']/',
            $matches[0],
            $matches
        );

        if (empty($matches)) {
            return 'unknown';
        }

        $match = preg_replace('/[\"\']/', '', $matches[0]);

        return str_replace('/php:', '', $match);
    }

    /**
     * @return void
     */
    private function publishConfiguration(): void
    {
        $params = [
            '--provider' => 'VexerVox\DevDocker\Providers\DevDockerServiceProvider',
            '--tag' => 'docker-folder',
            '--force' => true
        ];

        $this->call('vendor:publish', $params);
    }
}
