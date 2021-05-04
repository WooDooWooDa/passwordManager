<?php namespace Models;

use Google\Authenticator\GoogleAuthenticator;

class Authentification2fa
{
    public function authenticate($username): string
    {
        $g = new GoogleAuthenticator();
        $salt = SERVERKEY;
        $secret = $username . $salt;
        return $g->getUrl($username, 'password.local', $secret);
    }
}