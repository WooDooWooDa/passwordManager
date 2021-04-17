<?php namespace Controllers;

use Models\Brokers\AccountBroker;
use Models\Validator;
use Zephyrus\Application\Flash;
use Zephyrus\Application\Rule;

class AccountController extends SecurityController
{

    public function initializeRoutes()
    {
        $this->post("/account/register", "registerAccount");
        $this->post("/account/login", "loginAccount");
        $this->get("/account/logout", "logout");
        $this->put("/account/update", "updateAccount");
        $this->get("/debug", "debug");
    }

    public function debug()
    {
        $broker = new AccountBroker();
        $account = $broker->findById(sess('user_id'));
        var_dump($account);
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

    public function updateAccount()
    {
        $form = $this->buildForm();
        $validator = new Validator();
        $validator->validateAllForm($form);
        if (!$form->getValue('comfirm')) {
            Flash::error('veuillez comfirmer les changements avant de les appliquer');
            return $this->redirect("/home/account");
        }
        if (!$form->verify()) {
            $errors = $form->getErrorMessages();
            Flash::error($errors);
            return $this->redirect("/home/account");
        }
        $accountBroker = new AccountBroker();
        $accountBroker->updateAccount($form->buildObject());
        Flash::success("Compte mis à jour!");
        return $this->redirect("/home/account");
    }

    public function registerAccount()
    {
        $form = $this->buildForm();
        $validator = new Validator();
        $validator->validateAllForm($form);
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
