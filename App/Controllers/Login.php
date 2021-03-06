<?php
    
namespace App\Controllers;

use App\Auth;
use App\Flash;
use \Core\View;
use \App\Models\User;

class Login extends \Core\Controller
{
    //Show the login page

    public function newAction()
    {
        View::renderTemplate('Login/new.html');
    }

    //Log in a user
    public function createAction()
    {
        // echo($_REQUEST['email'].', '.$_REQUEST['password']);
        $user = User::authenticate($_POST['email'], $_POST['password']);

        // var_dump($_POST);
        // var_dump($user);

        $remember_me = isset($_POST['remember_me']);

        if($user){
            // header('Location: http://'.$_SERVER['HTTP_HOST'].'/signup/success', true, 303);
            // exit;

            Auth::login($user, $remember_me);

            //Remember the login here

            Flash::addMessage('Logowanie pomyślne!');

            $this->redirect(Auth::getReturnToPage());

        } else {
            Flash::addMessage('Błędny login lub hasło, spróbuj ponownie.', Flash::WARNING);

            View::renderTemplate('Login/new.html', [
                'email' => $_POST['email'],
                'remember_me' => $remember_me
            ]);
        }
    }

    //Log out a user
    public function destroyAction()
    {
        Auth::logout();

        $this->redirect('/login/show-logout-message');
    }

    //Show a "logged out" flash message and redirect to the homepage. Necessary to use the flash messages as they use the session and at the end of the logout method (destroyAction) the session is destroyed so a new action needs to be called in order to use the session
    public function showLogoutMessageAction()
    {
        Flash::addMessage('Wylogowanie pomyślne!');

        $this->redirect('/');
    }
}