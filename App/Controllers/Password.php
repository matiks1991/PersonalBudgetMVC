<?php
namespace App\Controllers;

use App\Models\User;
use \Core\View;

//Password controller

class Password extends \Core\Controller
{
    //Show the forgotten password page
    public function forgotAction()
    {
        View::renderTemplate('Password/forgot.html');
    }

    //Send the password reset link to the supplied email
    public function requestResetAction()
    {
        User::sendPasswordReset($_POST['email']);

        View::renderTemplate('Password/reset_requested.html');
    }

    //Show the reset password from
    public function resetAction()
    {
        $token = $this->route_params['token'];

        // echo $token;

        $user = $this->getUserOrExit($token);

        // var_dump($user);

        View::renderTemplate('Password/reset.html', [
            'token' =>$token
        ]);
        
    }

    //Reset the user's password
    public function resetPasswordAction()
    {
        $token = $_POST['token'];

        $user = $this->getUserOrExit($token);

        if ($user->resetPassword($_POST['password'])) {
            View::renderTemplate('Password/reset_success.html');
        } else {
            View::renderTemplate('Password/reset.html', [
                'token' => $token,
                'user' => $user
            ]);
        }

    }

    //Find the user model associated with the password reset token, or end the request with a message
    protected function getUserOrExit($token)
    {
        $user = User::findByPasswordReset($token);

        if ($user) {

            return $user;

        } else {
            View::renderTemplate('Password/token_expired.html');
            exit;
        }
    }
}