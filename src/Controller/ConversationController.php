<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ConversationController extends AbstractController
{
    #[Route('/conversation', name: 'app_conversation')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $conversations = $user->getConversations();

        return $this->render('conversation/index.html.twig', [
            'user' => $user,
            'conversations' => $conversations,
        ]);
    }
    #[Route('/conversation/{id}/messages', name: 'app_conversation_messages')]
    public function showMessages(Conversation $conversation, EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($request->isMethod('POST')) {

        }
        $messages = $conversation->getMessages();
        return $this->render('conversation/messages.html.twig', [
            'messages' => $messages,
            'conversation' => $conversation,
        ]);
    }
}
