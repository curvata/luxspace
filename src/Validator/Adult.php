<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class Adult extends Constraint
{
    public $message = 'Vous devez avoir minimum 18 ans pour vous inscrire';
}
