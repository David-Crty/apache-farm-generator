<?php
/**
 * Created by PhpStorm.
 * User: awstudio
 * Date: 03/09/15
 * Time: 10:09
 */

namespace App;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class Sql extends BaseCommand
{


    protected function configure()
    {
        $this
            ->setName('sql')
            ->setDescription('Génère l\'utilisateur et la BDD mysql')
            ->addArgument('username', InputArgument::REQUIRED, 'Username MySQL: ')
            ->addArgument('password', InputArgument::REQUIRED, 'Password de l\'user: ')
            ->addArgument('dbname', InputArgument::REQUIRED, 'Nom de la BDD: ');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelper('dialog');
        $bdd = Ask::askNotEmpy($dialog, $output, 'Veuillez entrer le nom de la bdd a créer: ');
        $username = Ask::askNotEmpy($dialog, $output, 'Veuillez entrer le username mysql a créer: ');
        $pwd = Ask::askNotEmpy($dialog, $output, 'Veuillez entrer le mot de passe du nouvel utilisateur: ');

        $input->setArgument('username', $username);
        $input->setArgument('password', $pwd);
        $input->setArgument('dbname', $bdd);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $pwd = $input->getArgument('password');
        $bdd = $input->getArgument('dbname');
        $parameters = $this->getSqlConfig();
        $host = $parameters['database_host'];
        try {
            $dbh = new \PDO("mysql:host=$host", $parameters['database_user'], $parameters['database_password']);
            $dbh->exec("CREATE DATABASE `$bdd`;
                CREATE USER '$username'@'localhost' IDENTIFIED BY '$pwd';
                GRANT ALL ON `$bdd`.* TO '$username'@'localhost';
                FLUSH PRIVILEGES;")
            or die(print_r($dbh->errorInfo(), true));

        } catch (\PDOException $e) {
            die("DB ERROR: ". $e->getMessage());
        }
        $output->writeln('[ok]');
    }

    protected function getSqlConfig(){
        $config = Yaml::parse(file_get_contents('../config/parameters.yml'));
        return $config['parameters'];
    }
}