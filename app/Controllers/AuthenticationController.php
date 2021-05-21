<?php namespace Controllers;

use Models\Brokers\AccountBroker;
use Models\Authentication2Fa;
use PHPGangsta_GoogleAuthenticator;
use Zephyrus\Application\Flash;

class AuthenticationController extends SecurityController
{
    const WEBSITE = "password.local - Password Manager";
    const TITLE = "Password Manager";

    public function initializeRoutes()
    {
        $this->get("/authentication", "authentication");
        $this->get("/authentication/googleAuth", "googleAuth");
        $this->get("/authentication/smsAuth", "smsAuth");
        $this->get("/authentication/emailAuth", "emailAuth");
        $this->post("/authentication/smsAuth/confirm", "smsConfirm");
        $this->post("/authentication/googleAuth/confirm", "googleConfirm");
        $this->post("/authentication/emailAuth/confirm", "emailConfirm");
        $this->put("/authentication/update", "update");
    }

    public function authentication()
    {
        if (isset($_SESSION["is_logged"])) {
           return $this->redirect("/home");
        }
        $authType = $_SESSION["authType"];
        if (!$authType == 0) {
            if (($authType & 1) == 1 && !isset($_SESSION["sms"])) {
                return $this->redirect("/authentication/smsAuth");
            }
            if (($authType & 2) == 2 && !isset($_SESSION["email"])) {
                return $this->redirect("/authentication/emailAuth");
            }
            if (($authType & 4) == 4 && !isset($_SESSION["google"])) {
                return $this->redirect("/authentication/googleAuth");
            }
        }
        $_SESSION["is_logged"] = true;
        Flash::success("Authentification complétée, bienvenue!");
        return $this->redirect("/home");
    }

    public function smsAuth()
    {
        if (!isset($_SESSION["smsAuth"])) {
            $smsAuth = new Authentication2Fa();
            $broker = new AccountBroker();
            $user = $broker->findById($_SESSION["user_id"]);
            $_SESSION["smsAuth"] = $smsAuth->createSms($user->phone);
            Flash::info("Code envoyer au ". $user->phone);
        }
        return $this->render('smsAuth', [
            'title' => "Comfirmation par SMS - Password Manager"
        ]);
    }

    public function smsConfirm()
    {
        $form = $this->buildForm()->buildObject();
        if ($form->code == $_SESSION["smsAuth"]) {
            $_SESSION["sms"] = true;
            return $this->redirect("/authentication");
        } else {
            Flash::error("Code de comfirmation invalide");
            return $this->redirect("/authentication/smsAuth");
        }
    }

    public function emailAuth()
    {
        if (!isset($_SESSION["emailAuth"])) {
            $auth = new Authentication2Fa();
            $broker = new AccountBroker();
            $user = $broker->findById($_SESSION["user_id"]);
            $_SESSION["emailAuth"] = $auth->createEmail($user->email);
            Flash::info("Code envoyer à " . $user->email);
        }
        return $this->render('emailAuth', [
            'title' => "Comfirmation par Email - Password Manager"
        ]);
    }

    public function emailConfirm()
    {
        $form = $this->buildForm()->buildObject();
        if ($form->code == $_SESSION["emailAuth"]) {
            $_SESSION["email"] = true;
            return $this->redirect("/authentication");
        } else {
            Flash::error("Code de comfirmation invalide");
            return $this->redirect("/authentication/smsAuth");
        }
    }

    public function googleAuth()
    {
        $authenticator = new PHPGangsta_GoogleAuthenticator();
        $broker = new AccountBroker();
        $secret = $broker->getSecret($_SESSION["user_id"]);
        $qrCodeUrl = $authenticator->getQRCodeGoogleUrl(self::TITLE, $secret, self::WEBSITE);
        return $this->render('googleAuthentication', [
            'qrUrl' => $qrCodeUrl,
            'title' => "Comfirmation par Google Authenticator - Password Manager"
        ]);
    }

    public function googleConfirm()
    {
        $authenticator = new PHPGangsta_GoogleAuthenticator();

        $broker = new AccountBroker();
        $secret = $broker->getSecret($_SESSION["user_id"]);
        $otp = $this->buildForm()->buildObject()->otp;
        $tolerance = 1;
        if ($authenticator->verifyCode($secret, $otp, $tolerance)) {
            $_SESSION["google"] = true;
            return $this->redirect("/authentication");
        } else {
            Flash::error("Code de comfirmation invalide");
            return $this->redirect("/authentication/googleAuth");
        }
    }

    public function update()
    {
        $form = $this->buildForm()->buildObject();
        $newAuthType = 0;
        if (!isset($form->comfirm)) {
            Flash::error('Veuillez comfirmer les changements avant de les appliquer');
            return $this->redirect("/home/account");
        }
        if (isset($form->sms)) {
            $newAuthType += 1;
        }
        if (isset($form->email)) {
            $newAuthType += 2;
        }
        if (isset($form->google)) {
            $newAuthType += 4;
        }
        if (isset($form->none)) {
            $newAuthType = 0;
        }
        $broker = new AccountBroker();
        $broker->updateAuth($newAuthType, $_SESSION["user_id"]);
        Flash::success("Mode d'authentification mis à jour!");
        return $this->redirect("/home/account");
    }
}