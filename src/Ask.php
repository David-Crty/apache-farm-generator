<?php
/**
 * Created by PhpStorm.
 * User: David-Laptop
 * Date: 03/09/2015
 * Time: 22:54
 */

namespace App;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;

class Ask {

    /**
     * @param $dialog DialogHelper
     * @param $output OutputInterface
     * @param $question string
     * @return mixed
     */
    public static function askYesOrNo($dialog,OutputInterface $output, $question){
        return $dialog->askAndValidate($output, $question,
            function ($answer) {
                if ((strcasecmp($answer, "y") == 0) OR (strcasecmp($answer, "n") == 0))
                    return $answer;
                throw new \RunTimeException('Cette valeur n\'est pas valide.');
            }
        );
    }

    /**
     * @param $dialog DialogHelper
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
     * @param $dialog DialogHelper
     * @param OutputInterface $output
     * @param $question
     * @param $regex
     * @return mixed
     */
    public static function askNotEmpyAndRegex($dialog,OutputInterface $output, $question, $regex){
        return $dialog->askAndValidate($output, $question,
            function ($answer) use ($regex) {
                if (empty($answer) OR !preg_match($regex, $answer))
                    throw new \RunTimeException('Cette valeur n\'est pas valide.');
                return $answer;
            }
        );
    }

    /**
     * @param $dialog DialogHelper
     * @param $output OutputInterface
     * @param $question string
     * @return mixed
     */
    public static function askPhp($dialog,OutputInterface $output, $question){
        $version_php_availabled = Ask::getPhpVersionAvailable();

        $response = $dialog->select(
            $output,
            $question,
            $version_php_availabled,
            false,
            false,
            "Cette version de php n'est pas disponible"
        );
        return $version_php_availabled[$response];
    }

    /**
     * Retourne la liste des version de php installé
     * @return array
     * @throws \Exception
     */
    protected static function getPhpVersionAvailable(){
        $scandir = scandir('/var/www/cgi-bin/');
        $return = array();
        var_dump($scandir);
        if(!$scandir){
            throw new \Exception('Aucune version de php disponible...');
        }
        foreach($scandir as $filename) {
            if(strpos($filename, 'php-cgi-')){
                $return[] = str_replace("php-cgi-", "", $filename);
            }
        }
        return $return;
    }

}