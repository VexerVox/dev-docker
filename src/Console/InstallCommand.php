<?php

namespace VexerVox\DevDocker\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'devdocker:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the DevDocker';

    public function handle(): int
    {
        $this->info('Installing DevDocker...');

        $this->info('Publishing configuration...');

        $this->installation();

        $this->info('Installed DevDocker');

        return 1;
    }

    /**
     * @return void
     */
    private function installation(): void
    {
        if (
            !$this->configurationExists('docker')
            && !$this->configurationExists('docker-compose.yaml')
        ) {
            $this->install();

            return;
        }

        if ($this->shouldOverwriteConfiguration()) {
            $this->info('Overwriting configuration file...');
            $this->install(true);

            return;
        }

        $this->info('Existing configuration was not overwritten');

    }

    /**
     * @param bool $force
     * @return void
     */
    private function install(bool $force = false): void
    {
        $this->publishConfiguration($force);
        $this->info('Published configuration');

        $services = $this->choice('Which services would you like to install?', [
            '-',
            'mysql',
            'mariadb',
            'mongo',
            'redis',
            'mailhog',
            'phpmyadmin',
        ], null, null, true);

        $this->info('Generating Docker Compose file...');

        $this->generateDockerCompose(
            !in_array('-', $services) ? $services : []
        );

        $this->info('Generated Docker Compose file');
    }

    /**
     * @param bool $forcePublish
     * @return void
     */
    private function publishConfiguration(bool $forcePublish = false): void
    {
        $params = [
            '--provider' => 'VexerVox\DevDocker\Providers\DevDockerServiceProvider',
            '--tag' => 'docker-folder'
        ];

        if ($forcePublish === true) {
            $params['--force'] = true;
        }

        $this->call('vendor:publish', $params);
    }

    /**
     * @param array $services
     * @return void
     */
    private function generateDockerCompose(array $services): void
    {
        $stubs = '';
        $depends = '';
        $volumes = '';

        $dependableServices = [
            'mysql',
            'mariadb',
            'mongo',
            'redis',
        ];

        $withVolumesServices = [
            'mysql',
            'mariadb',
            'mongo',
            'redis',
        ];

        foreach ($services as $service) {
            if (in_array($service, $dependableServices)) {
                $depends .= "      - $service\n";
            }

            if (in_array($service, $withVolumesServices)) {
                $volumes .= "  dbdata-$service:\n";
                $volumes .= "    driver: local\n";
            }

            $stubs .= file_get_contents(__DIR__ . "/../../stubs/{$service}.stub");
        }

        if (!empty($depends)) {
            $depends = "    depends_on:\n" . $depends;
        }

        if (!empty($volumes)) {
            $volumes = "volumes:\n" . $volumes;
        }

        $dockerCompose = file_get_contents(__DIR__ . '/../../stubs/docker-compose.stub');

        $dockerCompose = str_replace('{{depends}}', $depends, $dockerCompose);
        $dockerCompose = str_replace('{{services}}', $stubs, $dockerCompose);
        $dockerCompose = str_replace('{{volumes}}', $volumes, $dockerCompose);

        // Remove empty lines
        $dockerCompose = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $dockerCompose);

        file_put_contents($this->laravel->basePath('docker-compose.yml'), $dockerCompose);
    }

    /**
     * @param string $fileName
     * @return bool
     */
    private function configurationExists(string $fileName): bool
    {
        return File::exists(base_path($fileName));
    }

    /**
     * @return bool
     */
    private function shouldOverwriteConfiguration(): bool
    {
        return $this->confirm(
            'Config file already exists. Do you want to overwrite it?',
            false
        );
    }
}
