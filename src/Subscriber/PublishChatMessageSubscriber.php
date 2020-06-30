<?php

namespace App\Subscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;

class PublishChatMessageSubscriber implements EventSubscriberInterface
{

  private PublisherInterface $publisher;

  public function __construct(PublisherInterface $publisher)
  {
    $this->publisher = $publisher;
  }

  public static function getSubscribedEvents()
  {
    return [
      // KernelEvents::VIEW => ['publishChatMessage', EventPriorities::POST_SERIALIZE]
    ];
  }

  public function publishChatMessage(ViewEvent $event)
  {
    $routeName = $event->getRequest()->attributes->get('_route');
    if ($routeName !== 'api_chat_messages_post_collection') {
      return;
    }
    $json = $event->getControllerResult();

    $update = new Update('chatMessages', $json);

    $this->publisher->__invoke($update);
  }
}
