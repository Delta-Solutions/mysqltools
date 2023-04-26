<?php namespace DeltaSolutions\MysqlTools\Services;

use Spatie\Ssh\Ssh;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use function Termwind\{render};

class Server
{

    protected $username, $host, $keyfile, $rootpath, $maxdepth, $configPath;

    public function init($config)
    {

        $this->username   = $config['username'];
        $this->host       = $config['host'];
        $this->keyfile    = $config['keyfile'];
        $this->rootpath   = $config['rootpath'];
        $this->configPath = $config['configPath'];
        $this->maxdepth = substr_count($this->rootpath, '/') + 2;
        return $this;
    }

    protected function getName()
    {
        return $this->host;
    }

    protected function connect()
    {
        return Ssh::create($this->username, $this->host)->usePrivateKey($this->keyfile)->useMultiplexing('~/.ssh/sockets/%r@%h-%p', '15m');
    }


    protected function getIp($hostname)
    {
        $exec   = $this->connect()->execute(["sudo su", "host " . $hostname]);
        $output = $exec->getOutput();
        preg_match_all('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $output, $ip_matches);
        if (is_array($ip_matches[0])) {
            return implode(',', $ip_matches[0]);
        }
    }

    protected function getConfig(string $dirname)
    {
        $command = "cd {$this->configPath} && grep -Ril '" . $dirname . "\b'";
        $exec    = $this->connect()->execute(["sudo su", $command]);
        return array_filter(explode("\n", $exec->getOutput()));
    }

    protected function getHostname(string $config, $applicationPath)
    {
        $vhosts   = $this->connect()->execute(["sudo su", "cat {$this->configPath}/{$config}"])->getOutput();
        $vhost    = collect(array_filter(explode("<VirtualHost", $vhosts)))->filter(function ($vhost) use ($applicationPath) {
            return strstr($vhost, $applicationPath);
        })->first();
        $hostname = collect(explode("\n", $vhost))->filter(function ($configline) {
            return strstr(strtolower($configline), 'servername');
        })->first();

        return trim(str_replace("servername", "", strtolower($hostname)));
    }


}
