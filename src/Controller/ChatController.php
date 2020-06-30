<?php

namespace App\Controller;

use App\Entity\ChatMessage;
use App\Repository\ChatMessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ChatController extends AbstractController
{
    /**
     * @Route("/chat/send", name="chat_send", methods={"POST"})
     */
    public function postMessage(Request $request, EntityManagerInterface $em, SerializerInterface $serializer, PublisherInterface $publisher)
    {
        $messageJSON = $request->getContent();
        $chatMessage = $serializer->deserialize($messageJSON, ChatMessage::class, 'json');

        $em->persist($chatMessage);
        $em->flush();

        $update = new Update('chatMessages', $messageJSON);

        $publisher($update);

        return new JsonResponse(['status' => 'succes']);
    }

    /**
     * @Route("/chat/get", name="chat_get", methods={"GET"})
     */
    public function getMessages(ChatMessageRepository $chatMessageRepository, SerializerInterface $serializer)
    {
        $messages = $chatMessageRepository->findAll();



        return $this->json($serializer->serialize($messages, 'json'), 200, [
            "Content-Type" => "application/json",
        ]);
    }
}
