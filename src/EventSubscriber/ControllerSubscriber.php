<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Component\Security\Core\Security;

class ControllerSubscriber implements EventSubscriberInterface
{

    /**
     * @var Security
     */
    private $security;
    /**
     * @var Profiler
     */
    private $profiler;

    public function __construct(Security $security, Profiler $profiler)
    {
        $this->security = $security;
        $this->profiler = $profiler;
    }

    public function onKernelController(ControllerEvent $event)
    {
        if(!$this->security->isGranted('ROLE_ADMIN') && $this->profiler){
            $this->profiler->disable();
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.controller' => 'onKernelController',
        ];
    }
}
