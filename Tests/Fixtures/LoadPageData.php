<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\PageBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Societo\PageBundle\Entity\Page;

class LoadPageData implements FixtureInterface
{
    public function load($manager)
    {
        $manager->persist(new Page('example_page', 'example', ''));
        $manager->persist(new Page('insecure_default', 'login', ''));

        $page = new Page('example_page_2', 'example2', '');
        $page->setTitle('Example Page');
        $manager->persist($page);

        $page = new Page('example_page_3', 'example3', '');
        $page->setPublished(false);
        $manager->persist($page);

        $manager->flush();
    }
}
