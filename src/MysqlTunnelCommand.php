<?php namespace DeltaSolutions\MysqlTools;

use DeltaSolutions\MysqlTools\Services\Configurator;
use DeltaSolutions\MysqlTools\Services\Laravel;
use DeltaSolutions\MysqlTools\Services\Server;
use DeltaSolutions\MysqlTools\Traits\HasServer;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use function Termwind\{ask, render};


class MysqlTunnelCommand extends BaseCommand
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
            ->setName('mysql:tunnel')
            ->setDescription('Create a ssh tunnel to a given mysql server on a given port')
            ->setAliases(['mt']);
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
            $helper = $this->getHelper('question');
            $server = $configurator->askFor($helper, $input, $output, $choices, 'Which server do you want the create a tunnel for?');
            render('');
            $config = $configurator->getConfig()['servers'][$server];
            $port = ask("<span class='ml-1 mr-1'>Which port do you want your tunnel on: ");

            $command = 'ssh -i ' . $config['keyfile'] . ' -N  -f -L ' . $port . ':127.0.0.1:'.$config['mysql_port'] . ' '.$config['username'].'@'.$config['host'];
            exec($command);
            render('<div class="m-1">You can now connect to your mysql server via port '.$port.' using your credentials as if you would connect on the server itself.  If you want to see all open tunnels run \'ps -ef | grep ssh\'</div>');
            render('');
        } else {
            render('<div class="m-1">You have no saved servers, you can create one with the \'server:create\' command.</div>');
        }

        return 0;
    }

}

