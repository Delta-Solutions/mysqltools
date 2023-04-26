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
use function Termwind\{render};


class ServerInfoCommand extends BaseCommand
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
            ->setName('server:info')
            ->setDescription('Get the detailed information of a saved server')
            ->setAliases(['si']);
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
            $server = $configurator->askFor($helper, $input, $output, $choices, 'Which server do you want the info for?');
            render('');
            $config = $configurator->getConfig()['servers'][$server];
            foreach ($config as $key => $value) {
                if($key ===  "mysql_password"){
                    $value = '***********';
                }
                render("<div class='ml-1'>{$key}: {$value}</div>");
            }
            render('');
        } else {
            render('<div class="m-1">You have no saved servers, you can create one with the \'server:create\' command.</div>');
        }

        return 0;
    }

}

