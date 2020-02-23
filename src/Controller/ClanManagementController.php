<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ClanManagementController extends AbstractController
{
    /**
     * @Route("/clan/management", name="clan_management")
     */
    public function index()
    {
        return $this->render('clan_management/index.html.twig', [
            'controller_name' => 'ClanManagementController',
        ]);
    }
}
