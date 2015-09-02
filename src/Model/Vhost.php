<?php
/**
 * Created by PhpStorm.
 * User: David-Laptop
 * Date: 02/09/2015
 * Time: 22:23
 */

namespace App\Model;


class Vhost {

    /** @var string */
    private $serverName;

    /** @var string */
    private $folderName;

    /** @var string */
    private $phpVersion;

    /**
     * @return string
     */
    public function getServerName()
    {
        return $this->serverName;
    }

    /**
     * @param string $serverName
     */
    public function setServerName($serverName)
    {
        $this->serverName = $serverName;
    }

    /**
     * @return string
     */
    public function getFolderName()
    {
        return $this->folderName;
    }

    /**
     * @param string $folderName
     */
    public function setFolderName($folderName)
    {
        $this->folderName = $folderName;
    }

    /**
     * @return string
     */
    public function getPhpVersion()
    {
        return $this->phpVersion;
    }

    /**
     * @param string $phpVersion
     */
    public function setPhpVersion($phpVersion)
    {
        $this->phpVersion = $phpVersion;
    }
}