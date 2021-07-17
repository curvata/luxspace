<?php

namespace App\Tests\Twig;

use App\Validator\Adult;
use App\Validator\AdultValidator;
use App\Validator\Password;
use App\Validator\PasswordValidator;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class PasswordValidatorTest extends TestCase
{
    public function testNotValidPasswordClass()
    {
        $constraint = new Adult();
        $validator = new PasswordValidator();
        $this->expectExceptionMessage('App\Validator\PasswordConstraint", "App\Validator\Adult" given');
        $validator->validate("toto", $constraint);
    }

    public function testMessage()
    {
        $constraint = new Password();
        $this->assertStringContainsString(
            "Votre mot de passe doit contenir au minimum 8 caractères dont un symbole, un chiffre et une lettre",
            $constraint->message);
    }

    public function testEmptyValue()
    {
        $constraint = new Password();
        $validator = new PasswordValidator();
        $this->assertNull($validator->validate('', $constraint));
    }

    public function testValidPassword()
    {
        $constraint = new Password();
        $validator = new PasswordValidator();
        $context = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();
        $violation = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMock();
        $validator->initialize($context);
        
        $context->expects($this->never())->method("buildViolation")->willReturn($this->returnValue($violation));
        $violation->expects($this->never())->method("addViolation");
        $validator->validate("%Password2000", $constraint);
    }

    public function testNotValidPassword()
    {
        $constraint = new Password();
        $validator = new PasswordValidator();
        $context = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();
        $violation = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMock();
        $validator->initialize($context);
        
        $context->expects($this->once())->method("buildViolation")->willReturn($this->returnValue($violation));
        $violation->expects($this->once())->method("addViolation");
        $validator->validate("password", $constraint);
    }

    public function testNoStringPassword()
    {
        $constraint = new Password();
        $validator = new PasswordValidator();
        $this->expectExceptionMessage('Expected argument of type "string", "int" given');
        $validator->validate(5165165, $constraint);
    }
}