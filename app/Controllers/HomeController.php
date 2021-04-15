<?php namespace Controllers;

class HomeController extends SecurityController
{

    public function initializeRoutes()
    {
        $this->get("/home", "home");
    }

    public function home()
    {
        if (!isset($_SESSION["is_logged"])) {
            return $this->redirect("/login");
        }
        return $this->render('homepage', [
            'title'=> "Accueil - Password Manager"
        ]);
    }
}
