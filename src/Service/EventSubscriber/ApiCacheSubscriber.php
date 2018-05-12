<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 23/04/2018
 * Time: 13:01
 */

namespace App\Service\EventSubscriber;

use App\Controller\CacheController;
use App\Security\JwtTokenAuthenticator;
use App\Service\Cache\CacheService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiCacheSubscriber implements EventSubscriberInterface
{
    private $cacheService;
    private $authenticator;

    const DOC = '/api/doc';

    public function __construct(CacheService $cacheService, JwtTokenAuthenticator $authenticator)
    {
        $this->cacheService = $cacheService;
        $this->authenticator = $authenticator;
    }

    /**
     * @param FilterControllerEvent $event
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if ($event->getRequest()->getRequestUri() !== self::DOC) {
            if ($event->getRequest()->getMethod() === 'GET') {
                $userUtils = $this->authenticator->getUserUtils($event->getRequest());
                $clientId = $userUtils['client'];
                $requestUri = $event->getRequest()->getRequestUri();
                if (preg_match('#api/users#', $requestUri)) {
                    $formatedUri = $this->cacheService->setCacheKey($requestUri . '/' . $clientId);
                } else {
                    $formatedUri = $this->cacheService->setCacheKey($requestUri);
                }
                if ($this->cacheService->isCached($formatedUri)) {
                    $data = $this->cacheService->getCachedResources($formatedUri);

                    $controller = new CacheController();
                    $controller->setData($data);
                    $controller->setKey($requestUri);

                    $event->setController(array($controller, 'generateCachedApiResponse'));
                }
            }
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => 'onKernelController'
        );
    }
}
