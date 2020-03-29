<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\ApiUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;
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
            $user->setUuid(Uuid::fromInteger(strval($i)));
            $user->setStatus(1);
            $user->setPassword($this->encoder->encodePassword($user, 'user'.$i));
            $user->setEmailConfirmed(1 == mt_rand(0, 1));
            $user->setInfoMails(1 == mt_rand(0, 1));
            $user->setIsSuperadmin(false);

            $manager->persist($user);
        }

        // create admin user
        $admin = new User();
        $admin->setNickname('Admin');
        $admin->setEmail('admin@localhost.local');
        $admin->setStatus(1);
        $admin->setPassword($this->encoder->encodePassword($admin, 'admin'));
        $admin->setEmailConfirmed(1);
        $admin->setInfoMails(false);
        $admin->setIsSuperadmin(true);

        $manager->persist($admin);


        // create one API-Key
        $apiuser = new ApiUser();
        $apiuser->setName('Example');
        $apiuser->setApiToken('1234');
        $apiuser->setHost('localhost');

        $manager->persist($apiuser);

        $manager->flush();
    }
}
