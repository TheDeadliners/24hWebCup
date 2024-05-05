<?php

namespace App\Controller;

use App\Entity\SuperPower;
use App\Entity\User;
use App\Service\JwtService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class AccountController extends AbstractController
{
    #[Route('/compte', name: 'app_account')]
    public function account(Request $request, EntityManagerInterface $entityManager, JwtService $jwtService): Response
    {
        $account = $entityManager->getRepository(User::class)->find($this->getUser());

        // Création du header du JWT
        $header = [
            'typ' => 'JWT',
            'alg' => 'H256'
        ];

        // Création du payload du JWT
        $payload = [
            'uid' => $account->getId()
        ];

        // Génération du token
        $token = $jwtService->generate($header, $payload);

        setCookie('auth-token', $token);

        return $this->render('account/edit.html.twig', [
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

        return $this->redirectToRoute('app_account');
<<<<<<< HEAD
=======
    }
    #[Route('/mes-pouvoirs', name: 'app_superpowers')]
    public function superPowers(Request $request, EntityManagerInterface $entityManager): Response
    {
        $account = $entityManager->getRepository(User::class)->find($this->getUser());
        return $this->render('account/powers/list.html.twig', [
            'account' => $account
        ]);
    }
    #[Route('/ajouter-pouvoirs', name: 'app_superpowers_create')]
    public function superpowersCreate(EntityManagerInterface $entityManager): Response
    {
        $account = $entityManager->getRepository(User::class)->find($this->getUser());
        return $this->render('account/powers/create.html.twig', [
            'account' => $account
        ]);
>>>>>>> ebbb2d4ffaae6e95d38cc3e3f6265847422084e7
    }

    #[Route('/{id}/modifier-pouvoir', name: 'app_superpowers_edition')]
    public function superPowersEdition(SuperPower $superPower): Response
    {
        return $this->render('account/powers/edit.html.twig', [
            'power' => $superPower
        ]);
    }

    #[Route('/ajouter-pouvoir', name: 'app_superpowers_save')]
    public function accountSuperpowersSave(Request $request, EntityManagerInterface $entityManager): Response
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
