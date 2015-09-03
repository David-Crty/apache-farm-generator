<?php
/**
 * Created by PhpStorm.
 * User: David-Laptop
 * Date: 02/09/2015
 * Time: 21:55
 */

namespace App;
use App\Model\Vhost as VhostModel;

class GenerateVhostFile {

    public function getRefFile($is_redirection){
        $add_www = ($is_redirection)?"www.":"";
        $return = <<<EOT
<VirtualHost *:80>
    ServerName $add_www{{ serverName }}
    DocumentRoot /var/www/{{ folderName }}
    <Directory "/var/www/{{ folderName }}">
        AllowOverride All
        Allow from all
        AddHandler php-cgi .php
        Action php-cgi /cgi-bin-php/php-cgi-{{ phpVersion }}
    </Directory>

    ErrorLog /var/log/apache2/{{ serverName }}-error.log
    CustomLog /var/log/apache2/{{ serverName }}-access.log combined
</VirtualHost>

EOT;
        if($is_redirection){
            $return .= <<<EOT
<VirtualHost *:80>
    ServerName  {{ serverName }}
    Redirect permanent / http://www.{{ serverName }}/
</VirtualHost>
EOT;
        }
        return $return;
    }

    //TODO
    private function checkPhpVersion($phpversion){
        return true;
    }

    private function generateVhost(VhostModel $vhost, $is_redirection){
        $ref_file = $this->getRefFile($is_redirection);
        $ref_file = str_replace("{{ serverName }}", $vhost->getServerName(), $ref_file);
        $ref_file = str_replace("{{ folderName }}", $vhost->getFolderName(), $ref_file);
        $ref_file = str_replace("{{ phpVersion }}", $vhost->getPhpVersion(), $ref_file);
        return $ref_file;
    }

    public function exec($serverName, $folderName, $phpVersion, $is_redirection){
        if($this->checkPhpVersion($phpVersion)){
            $vhost = new VhostModel();
            $vhost->setServerName($serverName);
            $vhost->setFolderName($folderName);
            $vhost->setPhpVersion($phpVersion);
            file_put_contents($serverName, $this->generateVhost($vhost, $is_redirection));
        }
    }
}