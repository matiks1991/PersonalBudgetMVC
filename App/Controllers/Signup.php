<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

class Signup extends \Core\Controller
{
    //Show the signup page

    public function newAction()
    {
        View::renderTemplate('Signup/new.html');
    }

    public function createAction()
    {
        $user = new User($_POST);

        if($user->save()){
            // header('Location: http://'.$_SERVER['HTTP_HOST'].'/signup/success', true, 303);
            // exit();

            $user->sendActivationEmail();

            $this->redirect('/signup/success');
        } else {
            // var_dump($user->errors);
            View::renderTemplate('Signup/new.html', [
                'user' => $user
            ]);
        }
    }

    //Show the signup success page
    public function successAction(){
        View::renderTemplate('Signup/success.html');
    }

    //Activate a new account
    public function activateAction()
    {
        User::activate($this->route_params['token']);

        $this->redirect('/signup/activated');
    }

    //AShow the activation success page
    public function activatedAction()
    {
        View::renderTemplate('Signup/activated.html');
    }
}