<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\TradeRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Timezone;

class ConversationController extends AbstractController
{
    #[Route('/conversation', name: 'app_conversation')]
    public function index(EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $conversations = $userRepository->getConversationsByUser($user);

        return $this->render('conversation/index.html.twig', [
            'user' => $user,
            'conversations' => $conversations,
        ]);
    }
    #[Route('/nouvelle-conversation/{id}', name: 'app_conversation_new')]
    public function newConversation(TradeRequest $tradeRequest,EntityManagerInterface $entityManager, UserRepository $userRepository, Request $request): Response
    {
        $user = $this->getUser();
        $conversations = $userRepository->getConversationsByUser($user);

        if ($request->isMethod('POST')) {
            // Récupérer les données du formulaire soumis
            $authorId = $request->request->get('author');
            $userId = $request->request->get('user');

            // Récupérer les utilisateurs par leur ID
            $author = $userRepository->find($authorId);
            $user = $userRepository->find($userId);

            // Créer une nouvelle conversation
            $conversation = new Conversation();
            $conversation->setUser1($author);
            $conversation->setUser2($user);
            $conversation->setTrade($tradeRequest);

            // Enregistrer la conversation dans la base de données
            $entityManager->persist($conversation);
            $entityManager->flush();

            $this->addFlash('success','Conversation créée avec succès');
            return $this->redirectToRoute('app_conversation');
        }

        return $this->render('conversation/messages.html.twig', [
            'user' => $user,
            'conversations' => $conversations,
        ]);
    }
    #[Route('/conversation/{id}/messages', name: 'app_conversation_messages')]
    public function showMessages(Conversation $conversation, EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $content = $request->request->get('content');
            $emitterId = $request->request->get('emitterId');

            dd($entityManager->getRepository(User::class)->find($this->getUser()));
            // Récupération de l'ID du destinataire à partir de la relation de commerce de la conversation
//            $receiverId = ;
            // Création d'une nouvelle instance de Message
            $message = new Message();
            $message->setConversation($conversation);
            $message->setEmitter($entityManager->getRepository(User::class)->find($this->getUser()));
            $message->setReceiver($conversation->getTrade()->getAuthor());

            $message->setContent($content);
            $message->setDatetime(new \DateTime('now', new Timezone('Indian/Reunion'))); // Assurez-vous de définir la date et l'heure correctement

            // Persistez le message dans la base de données
            $entityManager->persist($message);
            $entityManager->flush();
        }

        // Récupération de tous les messages de la conversation
        $messages = $conversation->getMessages();
        // Rendu de la vue Twig avec les messages et la conversation
        return $this->render('conversation/messages.html.twig', [
            'messages' => $messages,
            'conversation' => $conversation,
        ]);
    }
}
