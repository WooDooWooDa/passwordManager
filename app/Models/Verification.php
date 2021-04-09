<?php namespace Models;


use Models\Brokers\AccountBroker;
use Zephyrus\Utilities\Validation;

class Verification
{

    public function verify($firstname, $lastname, $username, $password): bool
    {
        $valid = true;
        if (!$this->verifyNames($firstname, $lastname)) {
            $valid = false;
        }
        if ($this->verifyPassword($password)) {
            $valid = false;
        }
        if ($this->verifyUsername($username)) {
            $valid = false;
        }
        return $valid;
    }

    private function verifyNames($firstname, $lastname): bool
    {
        $valid = true;
        if (!$this->verifyFirstname($firstname)) {
            $valid = false;
        }
        if (!$this->verifyLastname($lastname)) {
            $valid = false;
        }
        return $valid;
    }

    private function verifyFirstname($firstname): bool
    {
        if (!Validation::isNotEmpty($firstname)) {
            $_SESSION["firstNameError"] = "Le prénom est requis";
            return false;
        } elseif (!Validation::isMaxLength($firstname, 100)) {
            $_SESSION["usernameError"] = "Le prénom ne peut dépasser 100 caractères";
            return false;
        }
        return true;
    }

    private function verifyLastname($lastname): bool
    {
        if (!Validation::isNotEmpty($lastname)) {
            $_SESSION["lastNameError"] = "Le nom est requis";
            return false;
        } elseif (!Validation::isMaxLength($lastname, 100)) {
            $_SESSION["usernameError"] = "Le nom ne peut dépasser 100 caractères";
            return false;
        }
        return true;
    }

    private function verifyPassword($password): bool
    {
        if ($password == "e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855") {
            $_SESSION["passwordError"] = "Le mot de passe est requis";
            return false;
        } elseif (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/", $password)) {
            $_SESSION["passwordError"] = "Le mot de passe doit contenir 8 caractères minimum, 1 lettres, 1 chiffres et un caractère spécial";
            return false;
        }
        return true;
    }

    private function verifyUsername($username): bool
    {
        $broker = new AccountBroker();
        $user = null; //$broker->findByUsername($username);
        if (!is_null($user)) {
            $_SESSION["userExist"] = "Un compte est déjà associer à ce nom d'utilisateur";
            return false;
        } elseif (!Validation::isNotEmpty($username)) {
            $_SESSION["usernameError"] = "Le nom d'utilisateur est requis";
            return false;
        } elseif (!Validation::isMaxLength($username, 100)) {
            $_SESSION["usernameError"] = "Le nom d'utilisateur ne peut dépasser 100 caractères";
            return false;
        }
        return true;
    }
}
