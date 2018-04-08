<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 06/04/2018
 * Time: 13:41
 */

namespace App\Exception;

use App\Service\Helper\ApiProblem;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiProblemException extends HttpException
{
    private $apiProblem;

    public function __construct(ApiProblem $apiProblem, \Exception $previous = null, array $headers = array(), $code = 0)
    {
        $this->apiProblem = $apiProblem;
        $statusCode = $apiProblem->getStatusCode();
        $message = $apiProblem->getTitle();

        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }

    /**
     * @return ApiProblem
     */
    public function getApiProblem(): ApiProblem
    {
        return $this->apiProblem;
    }
}
