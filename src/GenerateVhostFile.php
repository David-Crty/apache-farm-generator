<?php
/**
 * Created by PhpStorm.
 * User: David-Laptop
 * Date: 02/09/2015
 * Time: 21:55
 */

namespace App;
use App\Model\Vhost;

class GenerateVhostFile {

    public function getRefFile(){
        $return = <<<EOT
<VirtualHost *:80>
    ServerName {{ serverName }}
    Redirect permanent / http://www.{{ serverName }}/
</VirtualHost>
<VirtualHost *:80>
    ServerName www.{{ serverName }}
    DocumentRoot /var/www/{{ folderName }}
    <Directory "/var/www/{{ folderName }}">
        AddHandler php-cgi .php
        Action php-cgi /php-fcgi/php-cgi-{{ phpVersion }}
    </Directory>

    ErrorLog /var/log/apache2/{{ serverName }}-error.log
    CustomLog /var/log/apache2/{{ serverName }}-access.log combined
</VirtualHost
EOT;
        return $return;
        //return file_get_contents('./vhost.ref');
    }

    //TODO
    private function checkPhpVersion($phpversion){
        return true;
    }

    private function generateVhost(Vhost $vhost){
        $ref_file = $this->getRefFile();
        $ref_file = str_replace("{{ serverName }}", $vhost->getServerName(), $ref_file);
        $ref_file = str_replace("{{ folderName }}", $vhost->getFolderName(), $ref_file);
        $ref_file = str_replace("{{ phpVersion }}", $vhost->getPhpVersion(), $ref_file);
        return $ref_file;
    }

    public function exec($serverName, $folderName, $phpVersion){
        if($this->checkPhpVersion($phpVersion)){
            $vhost = new Vhost();
            $vhost->setServerName($serverName);
            $vhost->setFolderName($folderName);
            $vhost->setPhpVersion($phpVersion);
            file_put_contents($serverName, $this->generateVhost($vhost));
        }
    }
}