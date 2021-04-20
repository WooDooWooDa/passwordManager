<?php namespace Controllers;

use Models\Brokers\AccountBroker;
use Models\Brokers\ServiceBroker;
use Models\Brokers\TokenBroker;
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
        $this->post("/account/deleteToken", "deleteToken");
        $this->get("/debug", "debug");
    }

    public function debug()
    {
        $broker = new ServiceBroker();
        $services = $broker->getAllServiceWithInfo(sess('user_id'));
        var_dump($services);
    }

    public function deleteToken()
    {
        $form = $this->buildForm();
        $broker = new TokenBroker();
        $broker->deleteToken($form->getValue('delete'));
        Flash::success("Ordinateur retiré avec succès!");
        return $this->redirect("/home/account");
    }

    public function logout()
    {
        $broker = new AccountBroker();
        $broker->unremember(sess('user_id'));                                   //move this fn to TokenBroker
        unset($_COOKIE[REMEMBERME]);
        setcookie(REMEMBERME, null, -1, '/');
        unset($_SESSION["is_logged"]);
        unset($_SESSION["user_id"]);
        session_destroy();
        return $this->redirect("/login");
    }

    public function loginAccount()
    {
        $broker = new AccountBroker();
        $form = $this->buildForm()->buildObject();
        if (isset($_COOKIE[REMEMBERME])) {
            $user = $broker->findByToken($_COOKIE[REMEMBERME]);                     //move this fn to TokenBroker
        } else {
            $user = $broker->findByUsername($form->username);
        }
        if (is_null($user)) {
            sleep(2);
            Flash::error("Information de connexion invalide");
            return $this->redirect("/login");
        }
        $hashPassword = $user->password;
        if (!password_verify($form->password . PASSWORD_PEPPER, $hashPassword)) {
            sleep(2);
            Flash::error("Information de connexion invalide");
            return $this->redirect("/login");
        }
        if ($form->rememberMe == 'on') {
            $cookie = $broker->remember($user->user_id);                            //move this fn to TokenBroker
            setcookie(REMEMBERME, $cookie, time()+(60*60*24*30), '/', true, true);  //set cookie ne fonctionne pas !
            Flash::info($cookie);
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
            Flash::error('Veuillez comfirmer les changements avant de les appliquer');
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
