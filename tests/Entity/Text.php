<?php 

namespace App\Tests\Entity;

trait Text
{       
    /**
     * Retourne une chaine de caractère de x longueur
     *
     * @param  string $length
     * @return string
     */
    public function getText(int $length): string
    {
        $string = '';

        for ($i = 0; $i < $length; $i++) {
            $string .= 'A';
        }

        return $string;
    }
}