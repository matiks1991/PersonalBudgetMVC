<?php

namespace Core;

class View
{
    //Render a view file
    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP); //konwersja do tablicy asocjacyjnej

        $file = "../App/Views/$view"; //relative to Core directory

        if  (is_readable($file)){
            require $file;
        }   else {
            // echo "$file not found";
            throw new \Exception("$file not found");

        }
    }

    //Render a view template using Twig
    public static function renderTemplate($template, $args = [])
    {
        echo static::getTemplate($template, $args);
    }

    //Get the contents of a view template using Twig
    public static function getTemplate($template, $args = [])
    {
        static $twig = null;

        if($twig === null){
            $loader = new \Twig\Loader\FilesystemLoader('../App/Views');
            $twig = new \Twig\Environment($loader);
            $twig->addGlobal('session', $_SESSION);
            // $twig->addGlobal('is_logged_in', \App\Auth::isLoggedIn());
            $twig->addGlobal('current_user', \App\Auth::getUser());
            $twig->addGlobal('flash_messages', \App\Flash::getMessages());
        }
        
        return $twig->render($template, $args);
    }
}