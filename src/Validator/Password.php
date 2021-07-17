<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class Password extends Constraint
{
    public $message = 'Votre mot de passe doit contenir au minimum 8 caractères dont un symbole, un chiffre et une lettre';
}
