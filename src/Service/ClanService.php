<?php


namespace App\Service;

use App\Entity\Clan;
use App\Repository\ClanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class ClanService
{
    private EntityManagerInterface $em;
    private ClanRepository $clanRepository;
    private EncoderFactoryInterface $encoderFactory;

    public function __construct(EntityManagerInterface $entityManager, ClanRepository $clanRepository, EncoderFactoryInterface $encoderFactory)
    {
        $this->clanRepository = $clanRepository;
        $this->encoderFactory = $encoderFactory;
        $this->em = $entityManager;
    }

    public function checkCredentials(string $name, string $password)
    {
        if (empty($name) || empty($password)) {
            return false;
        }

        $clan = $this->clanRepository->findOneBy(['name' => $name]);

        if (empty($clan)) {
            return false;
        }

        $encoder = $this->encoderFactory->getEncoder(Clan::class);

        $valid = $encoder->isPasswordValid($clan->getJoinPassword(), $password, null);
        if ($encoder->needsRehash($clan->getJoinPassword())) {
            //Rehash legacy Password if needed
            $clan->setJoinPassword($encoder->encodePassword($password, null));
            $this->em->flush();
        }

        if ($valid) {
            return $clan;
        } else {
            // User or Password false
            return false;
        }
    }
}