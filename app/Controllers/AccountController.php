<?php namespace Controllers;

use Models\Brokers\AccountBroker;
use Models\Verification;
use Zephyrus\Application\Flash;

class AccountController extends SecurityController
{

    public function initializeRoutes()
    {
        $this->post("/account/register", "registerAccount");
        $this->post("/account/login", "loginAccount");
        $this->get("/debug", "debug");
    }

    public function debug()
    {
        $broker = new AccountBroker();
        $user = $broker->findByUsername("admin");
        var_dump($user);
    }

    public function loginAccount()
    {
        $broker = new AccountBroker();
        $user = $broker->findByUsername($_POST["username"]); //user est toujours null!
        if (is_null($user)) {
            sleep(2);
            Flash::error("information de connexion invalide");
            return $this->redirect("/login");
        }
        $hashPassword = $user["password"];
        if (!password_verify($_POST["password"] . PASSWORD_PEPPER, $hashPassword)) {
            $_SESSION["error"] = "Informations fournises incorrects";
            sleep(2);
            Flash::error("information de connexion invalide");
            return $this->redirect("/login");
        }
        $_SESSION["is_logged"] = true;
        $_SESSION["user_id"] = $user["user_id"];
        return $this->redirect("/home");
    }

    public function registerAccount()
    {
        //SANITIZE!!!!!!!!
        $_SESSION["lastname"] = $_POST["lastname"];
        $_SESSION["firstname"] = $_POST["firstname"];
        $verification = new Verification();
        if (!$verification->verify($_SESSION["firstname"], $_SESSION["lastname"], $_POST["username"], $_POST["password"])) {
            return $this->redirect("/signUp");
        }
        $accountBroker = new AccountBroker();
        $accountBroker->registerNew($_POST["username"], $_POST["password"]);
        return $this->redirect("/login");
    }
}
