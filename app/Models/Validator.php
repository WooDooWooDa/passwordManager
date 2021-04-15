<?php namespace Models;

use Zephyrus\Application\Rule;

class Validator
{
    public function validateAllForm($form)
    {
        $form->validate('firstname', Rule::notEmpty("Le prénom est requis"));
        $form->validate('lastname', Rule::notEmpty("Le nom est requis"));
        $form->validate('username', Rule::notEmpty("Le nom d'utilisateur est requis"));
        //validate username doesnt not exist
        $form->validate('password', Rule::notEmpty("Le mot de passe est requis"));               //afficher un ou lautre
        $form->validate('password', Rule::passwordCompliant("Le mot de passe doit être valide"));
    }
}