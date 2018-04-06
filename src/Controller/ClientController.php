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
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\EntityManager\UserManager;

class ClientController extends BaseController
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
           // return new JsonResponse($data, 400);
            return $this->generateValidationErrorResponse($data, 'client_add');
        }
        return $this->generateCustomView($data, 201, 'client_add');
    }

    /**
     * @param Request $request
     * @param UserManager $userManager
     * @Rest\Post(
     *     path="/api/clients/{id}/admin",
     *     name="admin_add",
     *     requirements={"id"="\d+"}
     * )
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     */
    public function createAdminAction(Request $request, UserManager $userManager, $id)
    {
        $data = $userManager->registerAdmin($request, $id);
        if (is_array($data)) {
           // return new JsonResponse($data, 400);
            return $this->generateValidationErrorResponse($data, 'admin_add');
        }
        return $this->generateCustomView($data, 201, 'admin_add', ['id' => $id]);
    }
}
