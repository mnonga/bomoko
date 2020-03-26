<?php

namespace UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testInscription()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/inscription');
    }

    public function testConnexion()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/connexion');
    }

    public function testProfile()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/profile');
    }

    public function testActivation()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/activation');
    }

}
