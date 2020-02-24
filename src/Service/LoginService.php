<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class LoginService
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function checkCredentials(array $credentials)
    {
        $email = $credentials['email'];
        $passwordhash = $credentials['passwordhash'];

        $query = $this->em->createQuery("SELECT u.externId,u.email,u.status,u.firstname,u.emailConfirmed,
                                             u.nickname,u.roles,u.isSuperadmin,u.uuid,u.id 
                                             FROM \App\Entity\User u WHERE u.email = :email AND u.password = :password");

        $query->setParameter('email', $email);
        $query->setParameter('password', $passwordhash);
        $user = $query->getOneOrNullResult();

        if ($user) {
            //Fetch the UserObject from DB
            return $user;

        } else {
            // User or Password false
            return false;
        }



    }
}
