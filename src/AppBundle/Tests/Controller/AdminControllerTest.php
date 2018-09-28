<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    public function testShow()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin');
        $this->assertTrue($crawler->filter('html:contains("Export")')->count() > 0);
    }
}
