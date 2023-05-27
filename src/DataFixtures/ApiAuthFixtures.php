<?php

namespace App\DataFixtures;

use App\Entity\ApiUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ApiAuthFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // create one API-Key
        $apiuser = new ApiUser();
        $apiuser->setName('Example');
        $apiuser->setApiToken('1234');
        $manager->persist($apiuser);
        $manager->flush();
    }
}
