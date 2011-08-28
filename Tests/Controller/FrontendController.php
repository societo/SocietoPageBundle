<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\PageBundle\Tests\Controller;

use Societo\BaseBundle\Test\WebTestCase;

class FrontendController extends WebTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures(array(
            'Societo\PageBundle\Tests\Fixtures\LoadPageData',
            'Societo\PageBundle\Tests\Fixtures\LoadAccountData',
        ));
    }

    public function testHandleAction()
    {
        $client = static::createClient(array('root_config' => __DIR__.'/../config/config.php'));
        $account = $client->getContainer()->get('doctrine.orm.entity_manager')->find('SocietoAuthenticationBundle:Account', 1);
        $this->login($client, $account);

        $crawler = $client->request('GET', '/example');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('title:contains("example_page")')->count());

        $crawler = $client->request('GET', '/example2');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('title:contains("Example Page")')->count());

        $crawler = $client->request('GET', '/example3');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        $client = static::createClient(array('root_config' => __DIR__.'/../config/config.php'));
        $account = $client->getContainer()->get('doctrine.orm.entity_manager')->find('SocietoAuthenticationBundle:Account', 2);
        $this->login($client, $account);
        $crawler = $client->request('GET', '/example3');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
