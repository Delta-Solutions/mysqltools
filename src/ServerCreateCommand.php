<?php namespace DeltaSolutions\MysqlTools;

use DeltaSolutions\MysqlTools\Services\Configurator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Process\Process;
use function Termwind\{ask, render};


class ServerCreateCommand extends BaseCommand
{
    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('server:create')
            ->setDescription('Create and save a new server configuration')
            ->setAliases(['sc']);
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->title();

        $helper       = $this->getHelper('question');
        $configurator = (new Configurator());

        $name = ask("<span class='ml-1 mr-1'>What name do you want for the server: </span>");
        $host = ask("<span class='ml-1 mr-1'>What is the ip/hostname of the server: ");
        if (!in_array($host, ['localhost', '127.0.0.1'])) {
            $username = ask("<span class='ml-1 mr-1'>What is your server username: ");
            $question = new ConfirmationQuestion(' Do you want to connect to your server with an ssh key (y/n) ? ', true, '/^(y|j)/i');

            if ($helper->ask($input, $output, $question)) {
                $keyfile = '~/.ssh/' . $configurator->askFor($helper, $input, $output, $this->getPossibleSshKeys(), 'Please select the ssh key you want to use');
            } else {
                $keyfile = null;
            }
        } else {
            $username = $keyfile = "";
        }
        $configurator->store($name, $host, $username, $keyfile);
        $configurator->validateServer($name);

        render("<span class='m-1'>Server with name {$name} is saved!</span>");

        return 0;
    }

    /**
     * @return array
     */
    private function getPossibleSshKeys(): array
    {
        $possibleKeys = Process::fromShellCommandline('ls ~/.ssh')->mustRun()->getOutput();
        $keys = array_filter(explode("\n", $possibleKeys), function ($key) {
            return strstr($key, '.pub');
        });
        return array_values(array_map(function ($key) {
            return str_replace('.pub', '', $key);
        }, $keys));
    }

}

