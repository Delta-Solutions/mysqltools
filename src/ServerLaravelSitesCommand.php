<?php namespace DeltaSolutions\MysqlTools;

use DeltaSolutions\MysqlTools\Services\Configurator;
use DeltaSolutions\MysqlTools\Services\Laravel;
use DeltaSolutions\MysqlTools\Services\Server;
use DeltaSolutions\MysqlTools\Traits\HasServer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use function Termwind\{render};


class ServerLaravelSitesCommand extends BaseCommand
{

    use HasServer;

    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('server:laravelsites')
            ->setDescription('Get Laravel applications with their version on a server')
            ->setAliases(['sla']);
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

        $configurator = (new Configurator());
        $choices      = $this->getServers($configurator);

        if (count($choices) > 0) {
            $helper     = $this->getHelper('question');
            $servername = $configurator->askFor($helper, $input, $output, $choices, 'Please select the server you want to run this command for');
        } else {
            render('<div class="m-1">You have no saved servers, you can create one with the \'server:create\' command.</div>');
            return 0;
        }

        $config = $configurator->validateServer($servername);
        $server = (new Laravel())->init($config);
        $server->getLaravelApplications($servername);

        return 0;
    }

}

