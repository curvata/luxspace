<?php

namespace App\Tests\Entity;

use App\Entity\PasswordReset;
use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PasswordResetTest extends KernelTestCase
{    
    public function testPasswordReset()
    {
        $user = new User();
        $passwordReset = new PasswordReset();
        $passwordReset2 = new PasswordReset();
        $user->setPasswordReset($passwordReset2);
        $passwordReset->setToken('_TOKEN')->setCreatedAt(new DateTime())->setUser($user);

        $this->assertThat($user->getPasswordReset(),  $this->equalTo($passwordReset));
        $this->assertStringContainsString('_TOKEN', $passwordReset->getToken());
        $this->assertThat($user, $this->equalTo($passwordReset->getUser()));
        $this->assertStringContainsString((new DateTime())->format('d-m-y'), $passwordReset->getCreatedAt()->format('d-m-y'));
    }
}