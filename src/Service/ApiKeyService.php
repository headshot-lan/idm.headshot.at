<?php

namespace App\Service;

use App\Entity\ApiUser;
use App\Repository\ApiUserRepository;
use Doctrine\ORM\EntityManagerInterface;

class ApiKeyService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function listApiKeys()
    {
        $query = $this->em->createQuery("SELECT u FROM \App\Entity\ApiUser u");
        $apiuser = $query->getResult();
        return $apiuser;
    }

    public function createApiKey(array $credentials)
    {
        $name = $credentials['name'];
        $apikey = $credentials['apikey'];

        $apiuser = new ApiUser();
        $apiuser->setApiToken($apikey);
        $apiuser->setName($name);

        try {
            $this->em->persist($apiuser);
            $this->em->flush();

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function deleteApiKey($name)
    {
        $query = $this->em->createQuery("SELECT u FROM \App\Entity\ApiUser u WHERE u.name = :name");
        $query->setParameter('name', $name);
        $apiuser = $query->getOneOrNullResult();

        try {
            $this->em->remove($apiuser);
            $this->em->flush();

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
