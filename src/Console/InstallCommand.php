<?php

namespace AdminKit\Articles\Console;

use AdminKit\Articles\ArticleServiceProvider;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'admin-kit:install-articles';
    protected $description = 'Install all of the AdminKit Articles files';

    public function handle()
    {
        $this->comment('Installing AdminKit Articles...');
        $this->info('Publishing configuration...');

        $this
            ->executeCommand('vendor:publish', [
                '--provider' => ArticleServiceProvider::class,
                '--tag' => [
//                    'config',
                    'migrations',
                ],
            ])
            ->executeCommand('migrate')
            ->executeCommand('storage:link');

        $this->info('Installed AdminKit Articles');
    }

    private function executeCommand(string $command, array $parameters = []): self
    {
        try {
            $result = $this->callSilent($command, $parameters);
        } catch (\Exception $exception) {
            $result = 1;
            $this->alert($exception->getMessage());
        }

        if ($result) {
            $parameters = http_build_query($parameters, '', ' ');
            $parameters = str_replace('%5C', '/', $parameters);
            $this->alert("An error has occurred. The '{$command} {$parameters}' command was not executed");
        }

        return $this;
    }
}
