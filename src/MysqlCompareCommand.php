<?php namespace DeltaSolutions\MysqlTools;

use DeltaSolutions\MysqlTools\Services\Configurator;
use DeltaSolutions\MysqlTools\Services\DatabaseManager;
use DeltaSolutions\MysqlTools\Traits\HasServer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use function Termwind\render;

class MysqlCompareCommand extends BaseCommand
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
            ->setName('mysql:compare')
            ->setDescription('Compare two mysql database structures and get the differences in sql statements')
            ->setAliases(['mc']);
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
        $choices      = $this->getServers($configurator);

        if (count($choices) == 0) {
            render('<div class="m-1">You have no saved servers, please create one first with the \'server:create\' command.</div>');
            return 0;
        }

        $source = $this->getMysqlServer('source', $input, $output, $configurator, $choices, $helper);
        $target = $this->getMysqlServer('target', $input, $output, $configurator, $choices, $helper);
        $outputTo = $configurator->askFor($helper, $input, $output, [1 => 'screen', 2 => 'file'], 'Do you want to output the result to screen or to a file?');
        render('');

        $databaseManager = new DatabaseManager();
        $databaseManager->setConfigFor('source',$source);
        $databaseManager->setConfigFor('target',$target);

        $sourceDatabases = $databaseManager->getDatabases('source');
        $targetDatabases = $databaseManager->getDatabases('target');

        render('');

        $question = new Question(' Which database do you want as source database? ');
        $question->setAutocompleterValues($sourceDatabases);
        $sourceDatabase = $helper->ask($input, $output, $question);

        $question = new Question(' Which database do you want as target database? ');
        $question->setAutocompleterValues($targetDatabases);
        $targetDatabase = $helper->ask($input, $output, $question);

        render('<div class="mt-1 ml-1">Ok i\'ll compare ' . $sourceDatabase . ' on ' . $source['host'] . ' with ' . $targetDatabase . ' on ' . $target['host'] . '</div>');
        render('<div class="mt-1 ml-1">Getting differences</div>');

        $changes = $databaseManager->compare($sourceDatabase, $targetDatabase);

        render('<div class="mt-1 ml-1 bg-green-800 text-white">Here are the resulting structure differences</div>');
        render('');

        if($outputTo == "screen"){
        foreach ($changes as $key => $change) {
            if (substr($change, 0, 4) == 'DROP') {
                $color = 'text-red-600';
            }elseif (substr($change, 0, 5) == 'ALTER') {
                    $color = 'text-orange-600';
            }else{
                $color = 'text-green-600';
            }
            render('<span class="ml-1 '.$color.'">' . $change . ';</span>');
        }
        }else{
            $databaseManager->saveToFile($changes,$targetDatabase);
        }


        return 0;
    }

    private function getMysqlServer($type, $input, $output, Configurator $configurator, $choices, $helper): array
    {

        if (count($choices) > 0) {
            $server = $configurator->askFor($helper, $input, $output, $choices, 'Which server do you want to use for you ' . $type . ' database?');
            return $configurator->validateServer($server, 'mysql');
        } else {
            render('<div class="m-1">You have no saved servers, you can create one with the \'server:create\' command.</div>');
        }

    }
}