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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
        $username = $dialog->ask(
            $output,
            'Veuillez entrer le username mysql a créer: ',
            'example'
        );
        $pwd = $dialog->ask(
            $output,
            'Veuillez entrer sont mot de pase: ',
            'example'
        );
        $bdd = $dialog->ask(
            $output,
            'Veuillez entrer le nom de la bdd: ',
            'exemple'
        );

        $input->setArgument('username', $username);
        $input->setArgument('password', $pwd);
        $input->setArgument('dbname', $bdd);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $pwd = $input->getArgument('password');
        $bdd = $input->getArgument('dbname');
        mysql_connect('localhost','root', '');
        mysql_query("create database $bdd;");
        mysql_query("grant usage on *.* to $username@localhost identified by '$pwd';");
        mysql_query("grant all privileges on $bdd.* to $username@localhost;");
        mysql_query("FLUSH PRIVILEGES;");
        mysql_close();
        $output->writeln('[ok]');
    }
}