<?php namespace Models;

use Zephyrus\Application\Form;
use Zephyrus\Application\Rule;

class Validator
{
    public function validateAllForm(Form $form)
    {
        $form->validate('firstname', Rule::notEmpty("Le prénom est requis"));
        $form->validate('lastname', Rule::notEmpty("Le nom est requis"));
        $form->validate('username', Rule::notEmpty("Le nom d'utilisateur est requis"));
        $form->validate('email', Rule::email("Le email doit être valide"));
        $form->validate('phone', Rule::phone("Le numéro de téléphone doit etre valide"));
        if (!str_contains($form->getValue('password'), "*")) {
            $form->validate('password', Rule::notEmpty("Le mot de passe est requis"));
            $form->validate('password', Rule::passwordCompliant("Le mot de passe doit être valide (8 caractères, 1 chiffre, 1 minuscule, 1 majuscule minimum)"));
        }
    }
}