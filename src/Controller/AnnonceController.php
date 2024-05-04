<?php

namespace App\Controller;

use App\Entity\User;

use App\Entity\Annonce;
use App\Entity\SuperPower;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/annonce')]
class AnnonceController extends AbstractController
{
    #[Route('/', name: 'app_annonce_index', methods: ['GET'])]
    public function index(AnnonceRepository $annonceRepository): Response
    {
        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonceRepository->findAll(),
        ]);
    }

    #[Route('/{id}/retirer-annonce', name: 'app_delete_annonce')]
    public function accountDeletePower(Request $request, Annonce $annonce, EntityManagerInterface $entityManager): Response
    {
        $token = $request->request->get('token');
        $user = $entityManager->getRepository(User::class)->find($this->getUser());
        $data = $request->request;

        if ($this->isCsrfTokenValid('deleteAnnonce', $token)) {
            $entityManager->remove($annonce);
            $entityManager->flush();
            $this->addFlash('success', 'Annonce supprimé avec succès');
        }

        return $this->redirectToRoute('app_annonce_index');
    }

    #[Route('/new', name: 'app_annonce_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $annonce = new Annonce();
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($annonce);
            $entityManager->flush();

            return $this->redirectToRoute('app_annonce_index', [], Response::HTTP_SEE_OTHER);
        }

        $user = $entityManager->getRepository(User::class)->find($this->getUser());

        $powers = $entityManager->getRepository(SuperPower::class)->findBy(["owner"=>$user]);
        return $this->render('annonce/new.html.twig', [
            'annonce' => $annonce,
            'form' => $form,
            'powers' => $powers,
        ]);
    }

    #[Route('/mes-annonces', name: 'app_annonces')]
    public function superPowers(EntityManagerInterface $entityManager): Response
    {
        $account = $entityManager->getRepository(User::class)->find($this->getUser());
        dd($account);
        return $this->render('annonce/show.html.twig', [
            'account' => $account
        ]);
    }

    #[Route('/ajouter-annonce', name: 'app_annonce_add')]
    public function accountAddPower(Request $request, EntityManagerInterface $entityManager): Response
    {
        $token = $request->request->get('token');
        // $user = $entityManager->getRepository(User::class)->find($this->getUser());
        $data = $request->request;

        if ($this->isCsrfTokenValid('addAnnonce', $token)) {
            $annonce = new Annonce();

            dd($data->get("type"));
            $annonce
                // ->getOwner($user)
                // ->getPower($data->get('power'))
                ->setType($data->get('type'))
                ->setDescription($data->get('description'))
            ;

            $entityManager->persist($annonce);
            $entityManager->flush();
            $this->addFlash('success', 'Annonce ajouté avec succès');
        }

        return $this->redirectToRoute('app_annonce_index');
    }

    #[Route('/{id}', name: 'app_annonce_show', methods: ['GET'])]
    public function show(Annonce $annonce): Response
    {
        return $this->render('annonce/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_annonce_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Annonce $annonce, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_annonce_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('annonce/edit.html.twig', [
            'annonce' => $annonce,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_annonce_delete', methods: ['POST'])]
    public function delete(Request $request, Annonce $annonce, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$annonce->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($annonce);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_annonce_index', [], Response::HTTP_SEE_OTHER);
    }
}
