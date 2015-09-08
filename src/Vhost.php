<?php

namespace App;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Vhost extends BaseCommand
{


    protected function configure()
    {
        $this
            ->setName('vhost')
            ->setDescription('Génère le fichier vhost')
            ->addArgument('servername', InputArgument::REQUIRED, 'Nom du server example.com')
            ->addArgument('foldername', InputArgument::REQUIRED, 'Nom du dossier dans /var/www')
            ->addArgument('phpversion', InputArgument::REQUIRED, 'Version de php')
            ->addArgument('redirection', InputArgument::REQUIRED, 'Effectuer la redirection vers www')
        ;
    }
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelper('dialog');
        $serverName = Ask::askNotEmpyAndRegex($dialog, $output, 'Veuillez entrer le nom de domaine: ', '#^(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,6}$#');
        $folderName = Ask::askNotEmpy($dialog, $output, 'Veuillez entrer le nom du dossier dans /var/www: ');
        $phpVersion = Ask::askPhp($dialog, $output, 'Veuillez entrer la version de php: ');
        $redirection = Ask::askYesOrNo($dialog, $output, 'Effectuer la redirection vers www? [Y/n] ');

        $input->setArgument('servername', $serverName);
        $input->setArgument('foldername', $folderName);
        $input->setArgument('phpversion', $phpVersion);
        $input->setArgument('redirection', $redirection);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serverName = $input->getArgument('servername');
        $folderName = $input->getArgument('foldername');
        $phpVersion = $input->getArgument('phpversion');
        $redirection = $input->getArgument('redirection');
        $generator = new GenerateVhostFile();
        $isRedirection = ($redirection == "Y" or $redirection == "y")?true:false;
        $generator->exec($serverName, $folderName, $phpVersion, $isRedirection);
        $output->writeln('<info>Génération du fichier vhost [ok]</info>');
        //if(!exec('mv '.$serverName.' /etc/apache2/sites-available/')){
            //unlink($serverName);
            //throw new \Exception('Impossible de déplacer le fichier dans /etc/apache2/sites-available/');
        //}
        $output->writeln('<info>Déplacement du fichier [ok]</info>');
        //if(!exec('ln -s /etc/apache2/sites-available/'.$serverName.' /etc/apache2/sites-enabled/'.$serverName.'')){
            //unlink('/etc/apache2/sites-available/'.$serverName);
            //throw new \Exception('Impossible de créer le lien symbolique');
        //}
        exec('mv '.$serverName.' /etc/apache2/sites-available/');
        exec('ln -s /etc/apache2/sites-available/'.$serverName.' /etc/apache2/sites-enabled/'.$serverName.'');
        $output->writeln('<info>Lien symbolique [ok]</info>');
    }
}
