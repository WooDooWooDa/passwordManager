<?php namespace Controllers;

use Models\Authentification2fa;
use Models\Brokers\AccountBroker;
use Models\Brokers\ServiceBroker;
use Models\Brokers\TokenBroker;
use Models\SmsAuthentication;
use Models\Validator;
use Zephyrus\Application\Flash;
use Zephyrus\Network\Cookie;
use Zephyrus\Utilities\Gravatar;

class AccountController extends SecurityController
{

    public function initializeRoutes()
    {
        $this->get("/home/account", "account");
        $this->post("/account/register", "registerAccount");
        $this->post("/account/login", "loginAccount");
        $this->get("/account/login", "loginAccountWithCookie");
        $this->get("/account/logout", "logout");
        $this->put("/account/update", "updateAccount");
        $this->post("/account/deleteToken", "deleteToken");
    }

    public function account()
    {
        if (!isset($_SESSION["is_logged"])) {
            return $this->redirect("/login");
        }
        $broker = new AccountBroker();
        $account = $broker->findById(sess('user_id'));

        $gravatar = new Gravatar($account->email);
        $imageUrl= "/assets/images/profil_pic_default.png";
        if ($gravatar->isAvailable()) {
            $imageUrl = $gravatar->getUrl();
        }

        $tokenBroker = new TokenBroker();
        $tokenList = $tokenBroker->getAllTokenByUserId(sess('user_id'));

        return $this->render('account', [
            'title' => "Compte - Password Manager",
            'account' => $account,
            'imageUrl' => $imageUrl,
            'tokenList' => $tokenList
        ]);
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
        $broker->unremember($_COOKIE[REMEMBERME]);                            //move this fn to TokenBroker

        unset($_COOKIE[REMEMBERME]);
        unset($_COOKIE[KEY]);
        setcookie(REMEMBERME, null, -1, '/');
        setcookie(KEY, null, -1, '/');
        unset($_SESSION["is_logged"]);
        unset($_SESSION["user_id"]);
        session_destroy();
        return $this->redirect("/login");
    }

    public function loginAccountWithCookie()
    {
        $broker = new AccountBroker();
        $user = $broker->findByToken($_COOKIE[REMEMBERME]);
        if ($user == null) {
            $this->logout();
        }
        $_SESSION["is_logged"] = true;
        $_SESSION["user_id"] = $user->user_id;
        $_SESSION["enKey"] = $_COOKIE[KEY];
        return $this->redirect("/home");
    }

    public function loginAccount()
    {
        $broker = new AccountBroker();
        $form = $this->buildForm()->buildObject();
        $user = $broker->findByUsername($form->username);

        //2fa AUTH
        $_SESSION["phone"] = $user->phone;
        return $this->redirect("/authentication/smsAuth");
        //2fa AUTH

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
            $cookie = $broker->remember($user->user_id);
            $cookie = new Cookie(REMEMBERME, $cookie);
            $cookie->setLifetime(Cookie::DURATION_MONTH);
            $cookie->send();
            $keyCookie = new Cookie(KEY, $broker->getKey($form->password, $user->user_id));
            $keyCookie->setLifetime(Cookie::DURATION_MONTH);
            $keyCookie->send();
        }
        $_SESSION["is_logged"] = true;
        $_SESSION["user_id"] = $user->user_id;
        $_SESSION["envKey"] = $broker->getKey($form->password, $user->user_id);
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
