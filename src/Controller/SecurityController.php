<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('app_dashboard');
         }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    #[Route(path: '/inscription', name: 'app_user_new')]
    public function newUser(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Vérifier si le formulaire a été soumis et que les champs requis sont remplis
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');

            if (!$email || !$password) {
                return $this->redirectToRoute('app_user_new');
            }

            // Vérifier si l'utilisateur existe déjà
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existingUser) {
                $this->addFlash('fail','Un compte existe déjà');
                return $this->redirectToRoute('app_user_new');
            }
            $user = new User();
            $lastName = $request->request->get('lastname');
            $firstName = $request->request->get('firstname');
            $birthdate = $request->request->get('birthdate');
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $hashpassword = $passwordHasher->hashPassword($user ,$password);

            $user->setEmail($email);
            $user->setLastName($lastName);
            $user->setFirstName($firstName);
            $user->setFirstName($birthdate);
            $user->setPassword($hashpassword);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', ' Compte crée avec succès');
            return $this->redirectToRoute('app_login');
        }
        // Si le formulaire n'est pas encore soumis ou n'est pas valide, affichez la page avec le formulaire
        return $this->render('security/register.html.twig');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


}
