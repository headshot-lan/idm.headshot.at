<?php

namespace App\Service;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoginService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $entityManager;
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function checkCredentials(array $credentials)
    {
        $email = $credentials['email'];
        $password = $credentials['password'];

        $user = $this->userRepository->findOneBy(['email' => $email]);
        $valid = $this->passwordEncoder->isPasswordValid($user, $password);
        if ($this->passwordEncoder->needsRehash($user)) {
            //Rehash legacy Password if needed
            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    $password
                )
            );
            $this->em->flush();
        }

        if ($valid) {
            //Fetch the UserObject from DB
            $query = $this->em->createQuery("SELECT u.email,u.status,u.firstname, u.emailConfirmed,
                                             u.nickname, u.roles, u.isSuperadmin, u.uuid, u.id 
                                             FROM \App\Entity\User u WHERE u.email = :email");

            $query->setParameter('email', $email);
            $user = $query->getOneOrNullResult();

            return $user;
        } else {
            // User or Password false
            return false;
        }
    }
}
