<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class TradeController extends AbstractController
{
    #[Route('/trade', name: 'app_trade')]
    public function index(): Response
    {
        return $this->render('trade/index.html.twig', [
            'controller_name' => 'TradeController',
        ]);
    }
}
