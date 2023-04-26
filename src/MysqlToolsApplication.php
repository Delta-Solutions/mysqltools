<?php

namespace DeltaSolutions\MysqlTools;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Process\Process;

class MysqlToolsApplication extends \Symfony\Component\Console\Application
{
    public function __construct(string $name, string $version, $args)
    {
        $output = new ConsoleOutput();
        if (count($args) == 1) {
            $this->brand($output);
        }

        $newVersion = $this->checkOutdated();
        if ($newVersion) {
            $output->writeln('');
            $output->writeln('<bg=red>Your version of mysqltools is outdated, version <bg=red;options=bold>'.$newVersion.'</> available , please update via the command : mysqltools self-update</>');
            $output->writeln('');
        }
        parent::__construct($name, $version);
    }

    private function brand(ConsoleOutput $output)
    {
        $output->writeln($this->getBrand());
    }

    private function getBrand()
    {

        $brand = '<fg=yellow>             


█▀▄▀█ █▄█ █▀ █▀█ █░░ ▀█▀ █▀█ █▀█ █░░ █▀
█░▀░█ ░█░ ▄█ ▀▀█ █▄▄ ░█░ █▄█ █▄█ █▄▄ ▄█


</><fg=green>Mysqltools</>, a commandline tool for mysql database management 
';

        return $brand;
    }

    private function checkOutdated()
    {
        try {
            $connected = @fsockopen('www.google.com', 80);
            if ($connected) {
                $version = strstr(Process::fromShellCommandline('composer global outdated --direct | grep mysqltools')->mustRun()->getOutput(), 'delta-solutions/mysqltools');
                return explode(' ', explode(' ! ', $version)[1])[0];
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}
