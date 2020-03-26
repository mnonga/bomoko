<?php

namespace UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PubControllerTest extends WebTestCase
{
    public function testNewpub()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/newpub');
    }

    public function testModifypub()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/modifypub');
    }

    public function testViewpub()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/viewpub');
    }

    public function testDeletepub()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/deletepub');
    }

    public function testPubs()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/pubs');
    }

}
