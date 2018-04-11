<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 06/04/2018
 * Time: 09:28
 */

namespace App\Controller;

use App\Exception\ApiProblemException;
use App\Service\Helper\ApiProblem;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends FOSRestController
{
    /**
     * @param $data
     * @param $statusCode
     * @param $route
     * @param array $routeOption
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function generateCustomView($data, $statusCode, $route, array $routeOption = [])
    {
        $view = $this->view($data, $statusCode, array(
            'Content-Type' => 'application/hal+json',
            'Location' => $this->generateUrl($route, $routeOption)
        ));

        return $this->handleView($view);
    }

    protected function generateApiView($data, $statusCode, $route, array $routeOption = [])
    {
        return new View($data, $statusCode, array(
            'Content-Type' => 'application/hal+json',
            'Location' => $this->generateUrl($route, $routeOption)
        ));
    }

    /**
     * @param $data
     * @param $statusCode
     * @param $route
     * @param array $routeParameter
     * @return Response
     */
    protected function generateCustomResponse($data, $statusCode, $route, array $routeParameter = [])
    {
        $response = new Response(
            $data,
            $statusCode,
            [
                'Content-Type' => 'application/hal+json',
                'Location' => $this->generateUrl($route, $routeParameter)
            ]
        );
        return $response;
    }

    /**
     * @param $data
     * @return JsonResponse
     */
    protected function throwApiProblemValidationException($data)
    {
        $apiProblem = new ApiProblem(400, ApiProblem::TYPE_VALIDATION_ERROR);
        $apiProblem->set('errors', $data);

        throw new ApiProblemException($apiProblem);
    }

    protected function throwApiProblemCredentialsException()
    {
        $apiProblem = new ApiProblem(422, ApiProblem::TYPE_BAD_CREDENTIALS);
        throw new ApiProblemException($apiProblem);
    }
}
