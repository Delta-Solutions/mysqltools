<?php

namespace DeltaSolutions\MysqlTools;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use function Termwind\{render, style};

style('title')->apply('bg-green-800 m-1 p-1 mb-0 text-white w-100');
style('success')->apply('ml-1 text-green');

class BaseCommand extends Command
{
    /**
     * @param OutputInterface $output
     * @param ProgressBar $progressBar
     * @return Process
     */
    public function runProcess($command, OutputInterface $output, $progressBar = null, $progressvalue = null, $withoutput = true): Process
    {
        $process = Process::fromShellCommandline($command);
        $process->setTimeout(3600);
        $process->mustRun(function ($type, $buffer) use ($output, $withoutput) {
            if ($withoutput) {
                $output->write($buffer);
            }
        });

        if ($progressBar) {
            $progressBar->advance($progressvalue);
        }

        return $process;
    }

    protected function source()
    {
        $srccommand = 'if [ "$SHELL" == "/bin/zsh" ]; then
echo source ~/.bash_profile >~/.zshenv
source ~/.zshenv
exec zsh -l
else
source ~/.bash_profile
exec bash -l
fi';
        Process::fromShellCommandline($srccommand)->mustRun();
    }

    protected function title()
    {
        render("<span class='title'>" . $this->getDescription() . "</span>");
        render('');
    }
}
