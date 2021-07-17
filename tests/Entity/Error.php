<?php 

namespace App\Tests\Entity;

trait Error
{ 
    /**
     * @param  mixed $obj
     * @return Array[]|null
     */
    public function getErrors(mixed $value): ?array
    {
        $errors = $this->getValidator()->validate($value);
        $messages = [];

        foreach ($errors as $violation) {
            $messages[$violation->getPropertyPath()] = $violation->getMessage();
        }
        return $messages;
    }
}