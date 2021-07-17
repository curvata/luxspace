<?php

namespace App\Validator;

use App\Validator\Adult;
use DateTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AdultValidator extends ConstraintValidator
{
    
    /**
     * Vérifie l'âge de l'utilisateur
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Adult) {
            throw new UnexpectedTypeException($constraint, AdultConstraint::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof DateTime) {
            throw new UnexpectedValueException($value, 'datetime');
        }

        if (date_diff($value, new DateTime())->y < 18) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
