<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\Validator\ValidatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

class DtoSerializerEventSubscriber implements EventSubscriberInterface
{

    private $serializer;
    private $validator;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function serializeToDto(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (false === $request->attributes->has('dto')
            || false === in_array($request->getMethod(), [Request::METHOD_POST, Request::METHOD_PUT, Request::METHOD_PATCH], true)
        ) {
            return;
        }

        $dto = $this->serializer->deserialize($request->getContent(), $request->attributes->get('dto'), 'json');

        $this->validator->validate($dto);

        $request->attributes->set('dto', $dto);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['serializeToDto', EventPriorities::PRE_READ],
        ];
    }
}
