<?php namespace Controllers;

use Models\SmsAuthentication;
use Zephyrus\Application\Flash;

class AuthenticationController extends SecurityController
{
    public function initializeRoutes()
    {
        $this->get("/googleAuth", "googleAuth");
        $this->get("/authentication/smsAuth", "smsAuth");
        $this->post("/authentication/smsAuth/confirm", "smsConfirm");
    }

    public function smsAuth()
    {
        if (!isset($_SESSION["smsAuth"])) {
            $smsAuth = new SmsAuthentication();
            $_SESSION["smsAuth"] = $smsAuth->createSms($_SESSION["phone"]);
            Flash::info("Code envoyer au ". $_SESSION["phone"]);
        }
        //var_dump($_SESSION["smsAuth"]);
        return $this->render('smsAuth', [
            'title' => "Comfirmation par SMS - Password Manager"
        ]);
    }

    public function smsConfirm()
    {
        $form = $this->buildForm()->buildObject();
        if ($form->code == $_SESSION["smsAuth"]) {
            //return to login with
            return true;
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
}