<?php namespace Controllers;

use Models\Brokers\ServiceBroker;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;

class LoginController extends SecurityController
{

    public function initializeRoutes()
    {
        $this->get("/", "index");
        $this->get("/login", "login");
        $this->get("/signUp", "signup");
        $this->get("/home", "home");
        $this->get("/debug", "debug");
    }

    public function index()
    {
        if (isset($_COOKIE[REMEMBERME])) {
            return $this->redirect("/account/login");
        }
        return $this->redirect("/login");
    }

    public function login()
    {
        if (isset($_SESSION["is_logged"])) {
            return $this->redirect("/home");
        }
        if (isset($_COOKIE[REMEMBERME])) {
            return $this->redirect("/account/login");
        }
        return $this->render('login', [
            'title' => "Connexion - Password Manager"
        ]);
    }

    public function signup()
    {
        return $this->render('signup', [
            'title' => "Inscription - Password Manager"
        ]);
    }

    public function home()
    {
        if (!isset($_SESSION["is_logged"])) {
            return $this->redirect("/login");
        }
        $broker = new ServiceBroker();
        $services = $broker->getAllService();
        return $this->render('homepage', [
            'title' => "Accueil - Password Manager",
            'services' => $services
        ]);
    }

    public function debug()
    {
        $username = "jé";
        $g = new GoogleAuthenticator();
        $salt = '7WAO342QFANY6IKBF7L7SWEUU79WL3VMT920VB5NQMW';
        $secret = $username . $salt;
        echo '<img src="'.$g->getURL($username, 'example.com', $secret).'" />';
    }
}
