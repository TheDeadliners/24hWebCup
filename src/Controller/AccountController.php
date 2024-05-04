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
        return $this->render('account/powers.html.twig', [
            'account' => $account
        ]);
    }

    #[Route('/editer-compte', name: 'app_account_edition')]
    public function accountEdition(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $token = $request->request->get('token');
        $user = $entityManager->getRepository(User::class)->find($this->getUser());
        $data = $request->request;

        if ($this->isCsrfTokenValid('editAccount', $token)) {
            if ($passwordHasher->isPasswordValid($user, $data->get('currentPassword'))) {
                $user
                    ->setFirstName($data->get('firstname'))
                    ->setLastName($data->get('lastname'))
                ;

                if ($data->get('profilePicture') != ""){
                    $user->setProfilePicture($data->get('profilePicture'));
                };

                if ($data->get('newPassword1') != "" && $data->get('newPassword2') != ""){
                    if ($data->get('newPassword1') == $data->get('newPassword2')) {
                        $user->setPassword($data->get('newPassword1'));
                    } else {
                        $this->addFlash('fail', 'Les nouveaux mot de passe ne correspondent pas');
                    }
                };

                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Compte mis à jour');
            } else {
                $this->addFlash('fail', 'Votre mot de passe est incorrect');
            }
        }

        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/ajouter-pouvoir', name: 'app_account_add_power')]
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

    #[Route('/{id}/retirer-pouvoir', name: 'app_account_delete_power')]
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
