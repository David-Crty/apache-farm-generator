<?php

namespace App;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends BaseCommand
{


    protected function configure()
    {
        $this
            ->setName('vhost')
            ->setDescription('Génère le fichier vhost')
            ->addArgument('servername', InputArgument::REQUIRED, 'Nom du server example.com')
            ->addArgument('foldername', InputArgument::REQUIRED, 'Nom du dossier dans /var/www')
            ->addArgument('phpversion', InputArgument::REQUIRED, 'Version de php')
        ;
    }
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelper('dialog');
        $serverName = $dialog->ask(
            $output,
            'Veuillez entrer le nom du server: ',
            'example.com'
        );
        $folderName = $dialog->ask(
            $output,
            'Veuillez entrer le nom du dossier dans /var/www: ',
            'example.com'
        );
        $phpVersion = $dialog->ask(
            $output,
            'Veuillez entrer la version de php: ',
            '5.6.2'
        );

        $input->setArgument('servername', $serverName);
        $input->setArgument('foldername', $folderName);
        $input->setArgument('phpversion', $phpVersion);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serverName = $input->getArgument('servername');
        $folderName = $input->getArgument('foldername');
        $phpVersion = $input->getArgument('phpversion');
        $generator = new GenerateVhostFile();
        $generator->exec($serverName, $folderName, $phpVersion);
        $output->writeln('Génération du fichier vhost [ok]');
        exec('mv '.$serverName.' /etc/apache2/sites-available/');
        $output->writeln('Déplacement du fichier [ok]');
        exec('ln -s /etc/apache2/sites-available/'.$serverName.' /etc/apache2/sites-enabled/'.$serverName.'');
        $output->writeln('Lien symbolique [ok]');
        $output->writeln('[ok]');
    }
}
