<?php namespace Controllers;

use Models\SmsAuthentification;
use Zephyrus\Application\Flash;

class AuthentificationController extends SecurityController
{
    public function initializeRoutes()
    {
        $this->get("/googleAuth", "googleAuth");
        $this->get("/authentification/smsAuth", "smsAuth");
        $this->post("/authentification/smsAuth/confirm", "smsConfirm");
    }

    public function smsAuth()
    {
        if (!isset($_SESSION["smsAuth"])) {
            $smsAuth = new SmsAuthentification();
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
        return $this->render('googleAuthentification', [
            'qrUrl' => $_SESSION["qrUrl"],
            'title' => "Comfirmation par SMS - Password Manager"
        ]);
    }
}