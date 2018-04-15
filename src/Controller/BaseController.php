<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 06/04/2018
 * Time: 09:28
 */

namespace App\Controller;

use App\Exception\ApiProblemException;
use App\Service\Problem\ApiProblem;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;

abstract class BaseController extends FOSRestController
{
    /**
     * @param $data
     * @param $statusCode
     * @param $route
     * @param array $routeOption
     * @return View
     */
    protected function generateApiResponse($data, $statusCode, $route, array $routeOption = [])
    {
        return new View($data, $statusCode, array(
            'Content-Type' => 'application/hal+json',
            'Location' => $this->generateUrl($route, $routeOption)
        ));
    }

    /**
     * @param $data
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
