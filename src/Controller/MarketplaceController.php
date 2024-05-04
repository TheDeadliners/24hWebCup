<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class MarketplaceController extends AbstractController
{
    #[Route('/marketplace', name: 'app_marketplace')]
    public function index(): Response
    {
        // requête dans la base de donnée User
        return $this->render('marketplace/index.html.twig', [
            'controller_name' => 'MarketplaceController',
            // retourne la requête vers la vue
        ]);
    }

    // #[Route('/marketplace/{id}/trade', name: 'app_marketplace')]
    // public function getSinglePower(): Response
    // {
    //     // récupérer l'id de l'user selectionnner
    //     return $this->render('marketplace/account.html.twig', [
    //         'controller_name' => 'MarketplaceController',
    //     ]);
    // }
}
