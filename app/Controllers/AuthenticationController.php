<?php namespace Controllers;

use Models\Brokers\AccountBroker;
use Models\SmsAuthentication;
use PHPGangsta_GoogleAuthenticator;
use Zephyrus\Application\Flash;

class AuthenticationController extends SecurityController
{
    public function initializeRoutes()
    {
        $this->get("/authentication", "authentication");
        $this->get("/authentication/googleAuth", "googleAuth");
        $this->get("/authentication/smsAuth", "smsAuth");
        $this->post("/authentication/smsAuth/confirm", "smsConfirm");
        $this->post("/authentication/googleAuth/confirm", "googleConfirm");
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
                //email auth
            }
            if (($authType & 4) == 4 && !isset($_SESSION["google"])) {
                return $this->redirect("/authentication/googleAuth");
            }
        }
        return $this->redirect("/home");
    }

    public function smsAuth()
    {
        //get le phone avec broker et non session
        if (!isset($_SESSION["smsAuth"])) {
            $smsAuth = new SmsAuthentication();
            $_SESSION["smsAuth"] = $smsAuth->createSms($_SESSION["phone"]);
            Flash::info("Code envoyer au ". $_SESSION["phone"]);
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

    public function googleAuth()
    {
        $website = 'https://password.local';
        $title= 'Password Manager';

        $authenticator = new PHPGangsta_GoogleAuthenticator();
        $secret = $authenticator->createSecret();
        $_SESSION["googleSecret"] = $secret;
        $qrCodeUrl = $authenticator->getQRCodeGoogleUrl($title, $secret, $website);
        return $this->render('googleAuthentication', [
            'qrUrl' => $qrCodeUrl,
            'title' => "Comfirmation par Google Authenticator - Password Manager"
        ]);
    }

    public function googleConfirm()
    {
        $authenticator = new PHPGangsta_GoogleAuthenticator();

        $secret = $_SESSION["googleSecret"];
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