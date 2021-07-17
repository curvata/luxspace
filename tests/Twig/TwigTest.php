<?php

namespace App\Tests\Twig;

use App\Twig\ActiveLinkMenuExtension;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class TwigTest extends TestCase
{
    public function testValidLevel1()
    {
        $request = new Request([], [], [], [], [], ['REQUEST_URI' => '/admin']);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $container = new Container();
        $container->set('request_stack', $requestStack);
        $activeLink = new ActiveLinkMenuExtension($container);

        $this->assertTrue($activeLink->isActive('admin'));
    }
    public function testGetFunction()
    {
        $request = new Request([], [], [], [], [], ['REQUEST_URI' => '/admin']);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $container = new Container();
        $container->set('request_stack', $requestStack);
        $activeLink = new ActiveLinkMenuExtension($container);

        $this->assertCount(1, $activeLink->getFunctions());
    }

    public function testValidLevel2()
    {
        $request = new Request([], [], [], [], [], ['REQUEST_URI' => '/admin/messages']);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $container = new Container();
        $container->set('request_stack', $requestStack);
        $activeLink = new ActiveLinkMenuExtension($container);

        $this->assertTrue($activeLink->isActive('messages'));
    }

    public function testValidLevel2WithSlash()
    {
        $request = new Request([], [], [], [], [], ['REQUEST_URI' => '/admin/messages/']);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $container = new Container();
        $container->set('request_stack', $requestStack);
        $activeLink = new ActiveLinkMenuExtension($container);

        $this->assertTrue($activeLink->isActive('messages'));
    }

    public function testNotValidLevel1()
    {
        $request = new Request([], [], [], [], [], ['REQUEST_URI' => '/admin']);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $container = new Container();
        $container->set('request_stack', $requestStack);
        $activeLink = new ActiveLinkMenuExtension($container);

        $this->assertFalse($activeLink->isActive('messages'));
    }


    public function testNotValidLevel2()
    {
        $request = new Request([], [], [], [], [], ['REQUEST_URI' => '/admin/messages']);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $container = new Container();
        $container->set('request_stack', $requestStack);
        $activeLink = new ActiveLinkMenuExtension($container);

        $this->assertFalse($activeLink->isActive('factures'));
    }
}