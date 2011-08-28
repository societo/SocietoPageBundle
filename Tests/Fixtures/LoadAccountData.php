<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\PageBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Societo\AuthenticationBundle\Entity\Account;
use Societo\BaseBundle\Entity\Member;

class LoadAccountData implements FixtureInterface
{
    public function load($manager)
    {
        $this->createAccount($manager, $this->createMember('example'));
        $this->createAccount($manager, $this->createMember('admin', true));

        $manager->flush();
    }

    private function createAccount($manager, $member)
    {
        $manager->persist($member);

        $account = new Account('societo.page.test.user_provider', $member->getDisplayName(), $member);
        $manager->persist($account);
    }

    private function createMember($name, $isAdmin = false)
    {
        $member = new Member();
        $member->setDisplayName($name);
        $member->setIsAdmin($isAdmin);

        return $member;
    }
}
