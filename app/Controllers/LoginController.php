<?php namespace Controllers;

class LoginController extends SecurityController
{

    public function initializeRoutes()
    {
        $this->get("/", "index");
        $this->get("/login", "login");
        $this->get("/signUp", "signup");
    }

    public function index()
    {
        if (isset($_COOKIE[REMEMBERME])) {
            return $this->redirect("/account/login");
        }
        return $this->redirect("/login");
    }

    public function login() {
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

    public function signup() {
        return $this->render('signup', [
            'title' => "Inscription - Password Manager"
        ]);
    }
}
