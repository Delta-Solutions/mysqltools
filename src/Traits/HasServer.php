<?php namespace DeltaSolutions\MysqlTools\Traits;

use Spatie\Ssh\Ssh;
use function Termwind\{render};

trait HasServer
{
    public function getServers($configurator)
    {
        $ret = [];
        if (isset($configurator->getConfig()['servers'])) {
            $counter = 1;
            foreach ($configurator->getConfig()['servers'] as $key => $server) {
                $ret[$counter] = $server['name'];
                $counter++;
            }
        }
        return $ret;
    }
}
