<?php

namespace App\Controller;

use App\Entity\SuperPower;
use App\Entity\TradeRequest;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class TradeRequestController extends AbstractController
{
    #[Route('/echanges', name: 'app_trade_requests')]
    public function tradeRequests(EntityManagerInterface $entityManager): Response
    {
        return $this->render('trade/index.html.twig', [
            'tradeRequests' => $entityManager->getRepository(TradeRequest::class)->findBy(['status' => false]),
        ]);
    }

    #[Route('/creer-echange', name: 'app_trade_requests_create')]
    public function tradeRequestsCreate(EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($this->getUser());
        $powers = $entityManager->getRepository(SuperPower::class)->findBy(['owner' => $user]);
        $powersEmpty = [];
        foreach ($powers as $power) {
            if ($power->getTradeRequests()->isEmpty()) {
                $powersEmpty[] = $power;
            } else {
                foreach ($power->getTradeRequests() as $tradeRequest) {
                    if ($tradeRequest->isStatus() === true) {
                        $powersEmpty[] = $powers;
                    }
                }
            }
        }

        return $this->render('trade/create.html.twig', [
            'powers' => $powersEmpty
        ]);
    }

    #[Route('/ajouter-echange', name: 'app_trade_requests_save')]
    public function tradeRequestsSave(Request $request, EntityManagerInterface $entityManager): Response
    {
        $token = $request->request->get('token');
        $user = $entityManager->getRepository(User::class)->find($this->getUser());
        $data = $request->request;

        if ($this->isCsrfTokenValid('addTradeRequest', $token)) {
            if ($data->get('power') != "") {
                $tradeRequest = new TradeRequest();

                $tradeRequest
                    ->setAuthor($user)
                    ->setPower($entityManager->getRepository(SuperPower::class)->find($data->get('power')))
                ;

                $entityManager->persist($tradeRequest);
                $entityManager->flush();
                $this->addFlash('success', 'Échange publié avec succès');
            } else {
                $this->addFlash('fail', 'Pas de pouvoir sélectionné');
            }
        }

        return $this->redirectToRoute('app_superpowers');
    }
}
