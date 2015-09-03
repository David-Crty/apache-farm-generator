<?php
/**
 * Created by PhpStorm.
 * User: David-Laptop
 * Date: 03/09/2015
 * Time: 22:54
 */

namespace App;
use Symfony\Component\Console\Output\OutputInterface;

class Ask {

    /**
     * @param $dialog mixed
     * @param $output OutputInterface
     * @param $question string
     * @return mixed
     */
    public static function askYesOrNo($dialog,OutputInterface $output, $question){
        return $dialog->askAndValidate($output, $question,
            function ($answer) {
                if (strcasecmp($answer, "y") == 0 or strcasecmp($answer, "n"))
                    throw new \RunTimeException('Cette valeur n\'est pas valide.');
                return $answer;
            }
        );
    }

    /**
     * @param $dialog mixed
     * @param $output OutputInterface
     * @param $question string
     * @return mixed
     */
    public static function askNotEmpy($dialog,OutputInterface $output, $question){
        return $dialog->askAndValidate($output, $question,
            function ($answer) {
                if (empty($answer))
                    throw new \RunTimeException('Cette valeur ne peut etre vide.');
                return $answer;
            }
        );
    }

    /**
     * @param $dialog mixed
     * @param $output OutputInterface
     * @param $question string
     * @return mixed
     */
    public static function askPhp($dialog,OutputInterface $output, $question){
        return $dialog->select(
            $output,
            $question,
            Ask::getPhpVersionAvailable(),
            false,
            false,
            "Cette version de php n'est pas disponible"
        );
    }

    /**
     * Retourne la liste des version de php installé
     * @return array
     * @throws \Exception
     */
    protected static function getPhpVersionAvailable(){
        $scandir = scandir('/var/www/cgi-bin-php/');
        $return = array();
        if(!$scandir){
            throw new \Exception('Aucune version de php disponible...');
        }
        foreach($scandir as $filename) {
            if (preg_match('/\d+(?:\.\d+)+/', $filename, $matches)) {
                $return[] = $matches;
            }
        }
        return $return;
    }

}