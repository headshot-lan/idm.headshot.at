<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\ApiUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // create 20 Users
        for ($i = 0; $i < 20; ++$i) {
            $user = new User();
            $user->setNickname('User '.$i);
            $user->setEmail('user'.$i.'@localhost.local');
            $user->setStatus(1);
            $user->setPassword($this->encoder->encodePassword($user, 'user'.$i));
            $user->setEmailConfirmed(1 == mt_rand(0, 1));
            $user->setInfoMails(1 == mt_rand(0, 1));
            $user->setIsSuperadmin(1 == mt_rand(0, 1));

            $manager->persist($user);
        }

        // create one API-Key
        $apiuser = new ApiUser();
        $apiuser->setName('Example');
        $apiuser->setApiToken('1234');

        $manager->persist($apiuser);

        $manager->flush();
    }
}
