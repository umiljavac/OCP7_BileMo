<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 06/04/2018
 * Time: 09:28
 */

namespace App\Controller;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class BaseController extends FOSRestController
{
    /**
     * @param $data
     * @param $statusCode
     * @param $route
     * @param array $routeOption
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function generateCustomView($data, $statusCode, $route, $routeOption = [])
    {
        $view = $this->view($data, $statusCode)
            ->setHeader('Location', $this->generateUrl($route, $routeOption));
        return $this->handleView($view);
    }

    /**
     * @param $data
     * @return JsonResponse
     */
    protected function generateValidationErrorResponse($data, $route)
    {
            $response = new JsonResponse($data, 400);
            $response->headers->set('Content-Type', 'application/problem+json');
            $response->headers->set('Location', $this->generateUrl($route));
            return $response;
    }
}
