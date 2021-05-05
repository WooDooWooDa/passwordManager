<?php namespace Controllers;

use Models\Brokers\AccountBroker;
use Models\SmsAuthentication;
use Zephyrus\Application\Flash;

class AuthenticationController extends SecurityController
{
    public function initializeRoutes()
    {
        $this->get("/authentication", "authentication");
        $this->get("/googleAuth", "googleAuth");
        $this->get("/authentication/smsAuth", "smsAuth");
        $this->post("/authentication/smsAuth/confirm", "smsConfirm");
        $this->put("/authentication/update", "update");
    }

    public function authentication()
    {
        $authType = $_SESSION["authType"];
        if (!$authType == 0) {
            if (($authType & 1) == 1 && !isset($_SESSION["sms"])) {
                return $this->redirect("/authentication/smsAuth");
            }
            if (($authType & 2) == 2 && !isset($_SESSION["email"])) {
                //email auth
            }
            if (($authType & 4) == 4 && !isset($_SESSION["google"])) {
                //google auth
            }
        }
        return $this->redirect("/home");
    }

    public function smsAuth()
    {
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
            unset($_SESSION["phone"]);
            unset($_SESSION["smsAuth"]);
            return $this->redirect("/login");
        }
    }

    public function googleAuth()
    {
        return $this->render('googleAuthentication', [
            'qrUrl' => $_SESSION["qrUrl"],
            'title' => "Comfirmation par SMS - Password Manager"
        ]);
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