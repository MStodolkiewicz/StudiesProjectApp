<?php

namespace App\EventSubscriber;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTCreatedSubscriber
{
    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        $data = array(
            'id' => $user->getUuid(),
            'roles' => $user->getRoles(),
            'username' => $user->getUserIdentifier(),
        );

        $event->setData($data);
    }
}
