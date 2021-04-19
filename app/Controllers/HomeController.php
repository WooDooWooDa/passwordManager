<?php namespace Controllers;

use Models\Brokers\AccountBroker;
use Models\Brokers\ServiceBroker;

class HomeController extends SecurityController
{

    public function initializeRoutes()
    {
        $this->get("/home", "home");
        $this->get("/home/account", "account");
        $this->get("/home/service", "service");
    }

    public function service()
    {
        if (!isset($_SESSION["is_logged"])) {
            return $this->redirect("/login");
        }
        if (!isset($_SESSION['showMdp'])) {
            $_SESSION['showMdp'] = [false, false, false, false];
        }
        $broker = new ServiceBroker();
        $services = $broker->getAllServiceWithInfo(sess('user_id'));
        return $this->render('service', [
            'title' => "Services - Password Manager",
            'services' => $services,
            'show' => sess('showMdp')
        ]);
    }

    public function account()
    {
        if (!isset($_SESSION["is_logged"])) {
            return $this->redirect("/login");
        }
        $broker = new AccountBroker();
        $account = $broker->findById(sess('user_id'));
        return $this->render('account', [
           'title' => "Compte - Password Manager",
            'account' => $account
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

}
