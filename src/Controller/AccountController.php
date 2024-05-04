<?php

namespace App\Controller;

use App\Entity\SuperPower;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class AccountController extends AbstractController
{
    #[Route('/compte', name: 'app_account')]
    public function account(EntityManagerInterface $entityManager): Response
    {
        $account = $entityManager->getRepository(User::class)->find($this->getUser());
        return $this->render('account/edit.html.twig', [
            'account' => $account
        ]);
    }
    #[Route('/mes-pouvoirs', name: 'app_superpowers')]
    public function superPowers(EntityManagerInterface $entityManager): Response
    {
        $account = $entityManager->getRepository(User::class)->find($this->getUser());
        return $this->render('account/powers/list.html.twig', [
            'account' => $account
        ]);
    }
    #[Route('/ajouter-pouvoirs', name: 'app_superpowers_add')]
    public function superPowersAdd(EntityManagerInterface $entityManager): Response
    {
        $account = $entityManager->getRepository(User::class)->find($this->getUser());
        return $this->render('account/powers/create.html.twig', [
            'account' => $account
        ]);
    }

    #[Route('/{id}/modifier-pouvoir', name: 'app_power_edition')]
    public function superPowersEdition(SuperPower $superPower): Response
    {
        return $this->render('account/powers/edit.html.twig', [
            'power' => $superPower
        ]);
    }

    #[Route('/ajouter-pouvoir', name: 'app_add_power')]
    public function accountAddPower(Request $request, EntityManagerInterface $entityManager): Response
    {
        $token = $request->request->get('token');
        $user = $entityManager->getRepository(User::class)->find($this->getUser());
        $data = $request->request;

        if ($this->isCsrfTokenValid('addPower', $token)) {
            $superPower = new SuperPower();

            $superPower
                ->setOwner($user)
                ->setName($data->get('ability'))
                ->setType($data->get('type'))
                ->setDescription($data->get('description'))
            ;

            $entityManager->persist($superPower);
            $entityManager->flush();
            $this->addFlash('success', 'Pouvoir ajouté avec succès');
        }

        return $this->redirectToRoute('app_superpowers');
    }

    #[Route('/{id}/sauvegarder-pouvoir', name: 'app_save_power')]
    public function accountEditPower(Request $request, SuperPower $superPower, EntityManagerInterface $entityManager): Response
    {
        $token = $request->request->get('token');
        $data = $request->request;

        if ($this->isCsrfTokenValid('editPower', $token)) {
            $superPower
                ->setName($data->get('ability'))
                ->setType($data->get('type'))
                ->setDescription($data->get('description'))
            ;

            $entityManager->persist($superPower);
            $entityManager->flush();
            $this->addFlash('success', 'Pouvoir modifié avec succès');
        }

        return $this->redirectToRoute('app_superpowers');
    }

    #[Route('/{id}/retirer-pouvoir', name: 'app_delete_power')]
    public function accountDeletePower(Request $request, SuperPower $superPower, EntityManagerInterface $entityManager): Response
    {
        $token = $request->request->get('token');
        $user = $entityManager->getRepository(User::class)->find($this->getUser());
        $data = $request->request;

        if ($this->isCsrfTokenValid('deletePower', $token)) {
            $entityManager->remove($superPower);
            $entityManager->flush();
            $this->addFlash('success', 'Pouvoir supprimé avec succès');
        }

        return $this->redirectToRoute('app_superpowers');
    }
}
