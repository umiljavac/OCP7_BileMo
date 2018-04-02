<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 30/03/2018
 * Time: 15:52
 */

namespace App\Controller;

use App\Entity\Client;
use App\Service\EntityManager\ClientManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ClientController extends FOSRestController
{

    /**
     * @param $clientId
     * @param Request $request
     * @Rest\Get(
     *     path="/api/clients/{id}",
     *     name="client_show",
     *     requirements={"id"="\d+"}
 *     )
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     * @return $view
     */
    public function getClientAction(Client $client)
    {
        return $this->generateCustomView($client, 200, 'client_show', ['id' => $client->getId()]) ;
    }

    /**
     * @Rest\Get(
     *     path="/api/clients",
     *     name="client_show_all"
     *     )
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     * @return $view
     */
    public function listAllClientsAction()
    {
        $clients = $this->getDoctrine()->getRepository(Client::class)->findAll();
        return $this->generateCustomView($clients, 200, 'client_show_all');
    }

    /**
     * @param Request $request
     * @param ClientManager $clientManager
     * @Rest\Post(
     *      path="/api/clients",
     *      name="client_add"
     * )
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     * @return JsonResponse|Response
     */
    public function createAction(Request $request, ClientManager $clientManager)
    {
        $data = $clientManager->registerClient($request);
        if (is_array($data)) {
            return new JsonResponse($data, 400);
        }
        return $this->generateCustomView($data, 201, 'client_add');
    }

    /**
     * @param $data
     * @param $statusCode
     * @param $route
     * @param array $routeOption
     * @return Response
     */
    private function generateCustomView($data, $statusCode, $route, $routeOption = [])
    {
        $view = $this->view($data, $statusCode)
            ->setHeader('Location', $this->generateUrl($route, $routeOption));
        return $this->handleView($view);
    }
}
