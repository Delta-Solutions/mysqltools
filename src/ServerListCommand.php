<?php namespace DeltaSolutions\MysqlTools;

use DeltaSolutions\MysqlTools\Services\Configurator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function Termwind\{render};


class ServerListCommand extends BaseCommand
{
    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('server:list')
            ->setDescription('Get a list of all defined servers')
            ->setAliases(['sl']);
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute( InputInterface $input, OutputInterface $output): int
    {
        $this->title();

        (new Configurator())->list();

        return 0;
    }

}

