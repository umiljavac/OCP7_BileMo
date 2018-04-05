<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 05/04/2018
 * Time: 16:22
 */

namespace App\Service\Helper;


use FOS\RestBundle\Controller\ControllerTrait;
use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use FOS\RestBundle\View\ViewHandlerInterface;

/**
 * Class ViewHelper
 * @package App\Service\Helper
 */
class ViewHelper
{
    use ControllerTrait;

    private $container;

    public function __construct(ContainerInterface $container, ViewHandlerInterface $viewHandler)
    {
        $this->container = $container;
        $this->viewhandler = $viewHandler;
    }

    /**
     * @param $data
     * @param $statusCode
     * @param $route
     * @param array $routeOption
     */
    public function generateCustomView($data, $statusCode, $route, $routeOption = [])
    {
        $view = $this->view($data, $statusCode)
            ->setHeader('Location', $this->generateUrl($route, $routeOption));
        return $this->handleView($view);
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @see UrlGeneratorInterface
     *
     * @final
     *
     */
    protected function generateUrl(string $route, array $parameters = array(), int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }
}
