<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testHome()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertTrue($crawler->filter('html:contains("Listado")')->count() > 0);
    }
    public function testImage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/image');
        $this->assertTrue($crawler->filter('html:contains("Designed")')->count() > 0);
    }
}
