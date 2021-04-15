<?php namespace Controllers;

use Models\Brokers\AccountBroker;
use Zephyrus\Application\Flash;
use Zephyrus\Application\Rule;

class AccountController extends SecurityController
{

    public function initializeRoutes()
    {
        $this->post("/account/register", "registerAccount");
        $this->post("/account/login", "loginAccount");
        $this->get("/account/logout", "logout");
        $this->get("/debug", "debug");
    }

    public function debug()
    {
        $broker = new AccountBroker();
        $user = $broker->findByUsername("je");
        var_dump($user);
    }

    public function logout()
    {
        unset($_SESSION["is_logged"]);
        unset($_SESSION["user_id"]);
        return $this->redirect("/login");
    }

    public function loginAccount()
    {
        $broker = new AccountBroker();
        $form = $this->buildForm()->buildObject();
        $user = $broker->findByUsername($form->username);
        if (is_null($user)) {
            sleep(2);
            Flash::error("information de connexion invalide");
            return $this->redirect("/login");
        }
        $hashPassword = $user->password;
        if (!password_verify($form->password . PASSWORD_PEPPER, $hashPassword)) {
            sleep(2);
            Flash::error("information de connexion invalide");
            return $this->redirect("/login");
        }
        $_SESSION["is_logged"] = true;
        $_SESSION["user_id"] = $user->user_id;
        return $this->redirect("/home");
    }

    public function registerAccount()
    {
        $form = $this->buildForm();
        $form->validate('firstname', Rule::notEmpty("Le prénom est requis"));
        $form->validate('lastname', Rule::notEmpty("Le nom est requis"));
        $form->validate('username', Rule::notEmpty("Le nom d'utilisateur est requis"));
        //validate username doesnt not exist
        $form->validate('password', Rule::notEmpty("Le mot de passe est requis"));               //afficher un ou lautre
        $form->validate('password', Rule::passwordCompliant("Le mot de passe doit être valide"));
        if (!$form->verify()) {
            $errors = $form->getErrorMessages();
            Flash::error($errors);
            return $this->redirect("/signUp");
        }
        $accountBroker = new AccountBroker();
        $accountBroker->registerNew($form->buildObject());
        Flash::success("Compte créé!");
        return $this->redirect("/login");
    }
}
