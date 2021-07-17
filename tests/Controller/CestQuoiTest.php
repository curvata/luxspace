<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CestQuoiTest extends WebTestCase
{    
    public function testAbout()
    {
        $client = static::createClient();
        $client->request('GET', '/a-propos');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', "C'est quoi Luxspace ?");
    }
}