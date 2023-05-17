<?php namespace DeltaSolutions\MysqlTools;

use DeltaSolutions\MysqlTools\Services\Configurator;
use DeltaSolutions\MysqlTools\Services\DatabaseManager;
use DeltaSolutions\MysqlTools\Traits\HasServer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use function Termwind\render;

class MysqlBackupCommand extends BaseCommand
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
            ->setName('mysql:backup')
            ->setDescription('Get a structure and data backup from a database ( add option --nodata if you only want the structure )')
            ->addOption('nodata', null, InputOption::VALUE_OPTIONAL, 'Only create a structure backup for this database',false)
            ->setAliases(['mb']);
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

        $source   = $this->getMysqlServer('source', $input, $output, $configurator, $choices, $helper);
        render('');

        $databaseManager = new DatabaseManager();
        $databaseManager->setConfigFor('source', $source);
        $sourceDatabases = $databaseManager->getDatabases('source');

        render('');

        $question = new Question(' Which database do you want to get the backup from? ');
        $question->setAutocompleterValues($sourceDatabases);
        $sourceDatabase = $helper->ask($input, $output, $question);
        $changes        = $databaseManager->getFullSchema($sourceDatabase);

        $databaseManager->saveToFile($changes, $sourceDatabase, 'backup');

        if($input->getOption('nodata') === false) {
            $databaseManager->exportTables($sourceDatabase);
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