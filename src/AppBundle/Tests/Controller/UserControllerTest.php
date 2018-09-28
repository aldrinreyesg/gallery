<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testShow()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/user');
        $this->assertTrue($crawler->filter('html:contains("Editor")')->count() > 0);
    }
}
