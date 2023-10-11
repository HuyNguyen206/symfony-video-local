<?php

namespace App\EventSubscriber;

use App\Services\Cache\CacheInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AdminChangeDataSubscriber implements EventSubscriberInterface
{
    protected $routeNameThatMustCLearCache = [
//        'videos.delete_post',
        'categories.update_put',
        'categories.store_post',
        'categories.delete_delete'
    ];

    public function __construct(protected CacheInterface $cache)
    {
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $routeName = $event->getRequest()->attributes->get('_route');
        $method = mb_strtolower($event->getRequest()->getMethod());

        $fullRouteName = "{$routeName}_$method";
        if (in_array($fullRouteName, $this->routeNameThatMustCLearCache)) {
            $this->cache->cache->invalidateTags(['video']);
        }
    }

    public static function getSubscribedEvents(): array
    {

        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}
