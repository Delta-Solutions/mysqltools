<?php namespace DeltaSolutions\MysqlTools\Services;

use DeltaSolutions\MysqlTools\Traits\HasServer;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Process\Process;
use function Termwind\{ask, render};

class Configurator
{

    use HasServer;

    const KEY = '7DFC491492EBA563FF0F2A3EEACE6A095EE628816290BC6D9B9034C2AF63B541';

    public function store($name, $host,$username,$keyfile)
    {
        $server                   = get_defined_vars();
        $config                   = $this->getConfig();
        $config['servers'][$name] = $server;

        $this->save($config);

        render("<span class='m-1'>Server with name {$name} is saved!</span>");

    }

    public function getConfig()
    {

        $path = $this->getPath();
        if (file_exists($path) && filesize($path) > 0) {
            return json_decode(file_get_contents($path), true);
        } else {
            return [];
        }
    }

    public function list()
    {
        $counter = 1;
        if (isset($this->getConfig()['servers'])) {
            render('<div class="ml-1 mb-1">Here\'s a list of all saved servers.</div>');

            collect($this->getConfig()['servers'])->each(function ($server) use (&$counter) {
                render("<span class='ml-1'>{$counter}. {$server['name']} ({$server['host']})</span>");
                $counter++;
            });
        }
        if ($counter == 1) {
            render('<div class="m-1">You have no saved servers, you can create one with the \'server:create\' command.</div>');
        }
        render('');

    }

    public function deleteServer($server)
    {
        $config = $this->getConfig();
        unset($config['servers'][$server]);
        $this->save($config);
        render("<span class='ml-1 mt-1'>{$server} is deleted!</span>");
        render('');
    }

    private function save($config)
    {
        $file = fopen($this->getPath(), "w") or die("Unable to open file!");
        fwrite($file, json_encode($config));
        fclose($file);
    }

    public function validateServer($servername, $type = "apache")
    {

        $config       = $this->getConfig();
        $serverConfig = $config['servers'][$servername];

        if ($type == "apache") {
            if (!isset($serverConfig['rootpath'])) {
                $serverConfig['rootpath'] = ask("<span class='ml-1 mr-1'>What is the root path of your server ( the place where your applications live ) ? </span>");
            }
            if (!isset($serverConfig['configPath'])) {
                $serverConfig['configPath'] = ask("<span class='ml-1 mr-1'>What is the apache config path of your server ? </span>");
            }
        }
        if ($type == "mysql") {
            if (!isset($serverConfig['mysql_port'])) {
                $serverConfig['mysql_port'] = ask("<span class='ml-1 mr-1'>What is the mysql port for your server ? </span>");
            }
            if (!isset($serverConfig['mysql_user'])) {
                $serverConfig['mysql_user'] = ask("<span class='ml-1 mr-1'>What is the mysql user for your server ? </span>");
            }
            if (!isset($serverConfig['mysql_password']) || $this->decrypt($serverConfig['mysql_password']) === false) {
                if(isset($serverConfig['mysql_password'])){
                    render("<span class='ml-1 mr-1 text-orange-400'>You might have given your mysql password earlier, but we re-ask so we can encrypt it 🔐 </span>");
                }
                $serverConfig['mysql_password'] = $this->encrypt(ask("<span class='ml-1 mr-1'>What is the mysql password for your server ? </span>") ?? "");
            }
            if (!isset($serverConfig['mysql_ssh'])) {
                $question                  = ask(' Do you want to connect to this mysql server over ssh (y/n) ? ', ['y', 'n']);
                $serverConfig['mysql_ssh'] = $question;
            }
        }
        $config['servers'][$servername] = $serverConfig;
        $this->save($config);


        return $config['servers'][$servername];
    }


    public function askFor($helper, $input, $output, $choices, $question)
    {
        $question = new ChoiceQuestion(
            ' ' . $question,
            $choices,
            0
        );
        $question->setErrorMessage('Input %s is invalid.');
        render('');
        return $helper->ask($input, $output, $question);
    }

    private function getPath()
    {
        $homeDir = trim(Process::fromShellCommandline("cd ~ && pwd")->mustRun()->getOutput());
        return $homeDir . "/.mysqltools";
    }

    public function encrypt($plaintext)
    {
        $cipher = "aes-128-gcm";
        if (in_array($cipher, openssl_get_cipher_methods())) {
            $ivlen         = openssl_cipher_iv_length($cipher);
            $iv            = openssl_random_pseudo_bytes($ivlen);
            $encryptedText = openssl_encrypt($plaintext, $cipher, self::KEY, $options = 0, $iv, $tag);
        }
        return base64_encode(json_encode([base64_encode($encryptedText), base64_encode($iv), base64_encode($tag)]));
    }

    public function decrypt($encrypted)
    {
        $object = json_decode(base64_decode($encrypted));
        if (is_null($object)) {
            return false;
        }
        $ciphertext = base64_decode($object[0]);
        $iv         = base64_decode($object[1]);
        $tag        = base64_decode($object[2]);
        $cipher     = "aes-128-gcm";
        if (in_array($cipher, openssl_get_cipher_methods())) {
            $original_plaintext = openssl_decrypt($ciphertext, $cipher, self::KEY, $options = 0, $iv, $tag);
        }
        return $original_plaintext;
    }
}
