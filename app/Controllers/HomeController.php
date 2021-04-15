<?php namespace Controllers;

class HomeController extends SecurityController
{

    public function initializeRoutes()
    {
        $this->get("/home", "home");
        $this->get("/home/account", "account");
    }

    public function account()
    {
        return $this->render('account', [
           'title' => "Compte - Password Manager"
        ]);
    }

    public function home()
    {
        if (!isset($_SESSION["is_logged"])) {
            return $this->redirect("/login");
        }
        return $this->render('homepage', [
            'title' => "Accueil - Password Manager"
        ]);
    }
}
