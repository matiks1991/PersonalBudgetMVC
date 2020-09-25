<?php
namespace App\Controllers;

use \Core\View;

class Home extends \Core\Controller{

    //Show the index page
    public function indexAction(){
        // \App\Mail::send('mateusz@gmail.com', 'Test', 'This is a test', '<h1>This is a test</h1>');

        View::renderTemplate('Home/index.html');
    }
}