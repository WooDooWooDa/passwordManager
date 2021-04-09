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
            //return $this->redirect("/account/login");s
        }
        return $this->redirect("/login");
    }

    public function login() {
        $password = password_hash("admin" . PASSWORD_PEPPER, PASSWORD_DEFAULT);
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
