<?php

namespace Application\SiteBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SiteControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/Site/Fabien');

        $this->assertTrue($crawler->filter('html:contains("Hey Fabien")')->count() > 0);
    }
}
