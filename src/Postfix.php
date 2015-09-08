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

class Postfix extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('postfix')
            ->setDescription('Génère les droits postfix pour utiliser jetmail')
            ->addArgument('email', InputArgument::REQUIRED, 'Username MySQL: ');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelper('dialog');
        $email = $dialog->ask(
            $output,
            'Veuillez l\'adresse email: ',
            'email@example.com'
        );

        $input->setArgument('email', $email);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mailjetpwd = $this->getMailJetPwd();
        if(empty($mailjetpwd)){
            throw new \Exception('Vous devez definir le pwd mailjet.');
        }
        $email = $input->getArgument('email');
        $to_add = "$email in.mailjet.com\n";
        file_put_contents("/etc/postfix/sender_relay", $to_add, FILE_APPEND | LOCK_EX);
        $to_add = "$email $mailjetpwd\n";
        file_put_contents("/etc/postfix/sasl_passwd", $to_add, FILE_APPEND | LOCK_EX);
        exec('cd /etc/postfix && postmap sasl_passwd sender_relay && postfix reload', $output, $status);
        $output->writeln('<info>Ajout du mail dans postfix [ok]</info>');
    }

    protected function getMailJetPwd(){
        $config = Yaml::parse(file_get_contents(dirname( __FILE__ ) . '/../config/parameters.yml'));
        return $config['pareparameters']['mailjet_utilisateur'].':'.$config['pareparameters']['mailjet_passwd'];
    }
}