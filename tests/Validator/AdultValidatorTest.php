<?php

namespace App\Tests\Twig;

use App\Validator\Adult;
use App\Validator\AdultValidator;
use App\Validator\Password;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class AdultValidatorTest extends TestCase
{
    public function testNotValidDateTime()
    {
        $constraint = new Adult();
        $validator = new AdultValidator();
        $this->expectExceptionMessage('Expected argument of type "datetime", "string" given');
        $validator->validate("14-07-1988", $constraint);
    }

    public function testNotValidAdultClass()
    {
        $constraint = new Password();
        $validator = new AdultValidator();
        $this->expectExceptionMessage('App\Validator\AdultConstraint", "App\Validator\Password" given');
        $validator->validate(new DateTime(), $constraint);
    }

    public function testMessage()
    {
        $constraint = new Adult();
        $this->assertStringContainsString(
            "Vous devez avoir minimum 18 ans pour vous inscrire",
            $constraint->message);
    }

    public function testEmptyValue()
    {
        $constraint = new Adult();
        $validator = new AdultValidator();
        $this->assertNull($validator->validate('', $constraint));
    }

    public function testValidDate()
    {
        $constraint = new Adult();
        $validator = new AdultValidator();
        $context = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();
        $violation = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMock();
        $validator->initialize($context);
        
        $context->expects($this->never())->method("buildViolation")->willReturn($this->returnValue($violation));
        $violation->expects($this->never())->method("addViolation");
        $validator->validate(new DateTime("14-07-1988"), $constraint);
    }

    public function testNotValidAge()
    {
        $constraint = new Adult();
        $validator = new AdultValidator();
        $context = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();
        $violation = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMock();
        $validator->initialize($context);
        
        
        $context->expects($this->once())->method("buildViolation")->willReturn($this->returnValue($violation));
        $violation->expects($this->once())->method("addViolation");
        $validator->validate(new DateTime(), $constraint);

    }

   
}