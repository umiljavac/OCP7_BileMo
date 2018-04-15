<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 06/04/2018
 * Time: 15:04
 */

namespace App\Service\EventSubscriber;

use App\Exception\ApiProblemException;
use App\Service\Problem\ApiProblem;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ApiExceptionSubscriber implements EventSubscriberInterface
{

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $e = $event->getException();
        if ($e instanceof ApiProblemException) {
            $apiProblem = $e->getApiProblem();
        } elseif ($e instanceof BadRequestHttpException) {
            $apiProblem = new ApiProblem(400, ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT);
        } else {
            $statusCode = $e instanceof HttpException ? $e->getStatusCode() : 500;
            $apiProblem = new ApiProblem ($statusCode);
            $apiProblem->set('detail', $e->getMessage());
        }

        $response = new JsonResponse(
            $apiProblem->toArray(),
            $apiProblem->getStatusCode()
        );
        $response->headers->set('Content-Type', 'application/problem+json');

        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => 'onKernelException'
        );
    }
}
