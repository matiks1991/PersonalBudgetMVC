<?php

namespace App\Controllers;

//Authenticated

abstract class Authenticated extends \Core\Controller
{
    //Require the user to be authenticated befor giving access to all methods in the controller
    protected function before()
    {
        $this->requireLogin();
    }
}