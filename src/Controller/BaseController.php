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
        $view = $this->view($data, $statusCode, array(
            'Content-Type' => 'application/hal+json',
            'Location' => $this->generateUrl($route, $routeOption)
        ));
        return $this->handleView($view);
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

    // ajout samedi
    protected function throwApiProblemCredentialsException()
    {
        $apiProblem = new ApiProblem(422, ApiProblem::TYPE_BAD_CREDENTIALS);
        throw new ApiProblemException($apiProblem);
    }
}
