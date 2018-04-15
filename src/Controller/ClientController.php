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
use Symfony\Component\HttpFoundation\Request;
use App\Service\EntityManager\UserManager;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security as SEC;

class ClientController extends BaseController
{

    /**
     * Show one client : required [ROLE_SUPER_ADMIN]
     *
     * @SWG\Response(
     *     response=200,
     *     description="Return one client",
     * @Model(type=Client::class)
     * )
     * @SWG\Response(
     *     response=404,
     *     description="return not found"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="return message : authorization is required"
     * )
     * @SWG\Response(
     *     response=403,
     *     description="return access denied"
     * )
     * @SWG\Response(
     *     response=500,
     *     description="returned on any others errors"
     * )
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The unique id of the client to show"
     * )
     * @SWG\Tag(name="Clients")
     * @SEC(name="Bearer")
     *
     * @Rest\Get(
     *     path="/api/clients/{id}",
     *     name="client_show",
     *     requirements={"id"="\d+"}
     * )
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     *
     * @param Client $client
     *
     * @return \FOS\RestBundle\View\View $view
     */
    public function showAction(Client $client)
    {
        return $this->generateApiResponse($client, 200, 'client_show', ['id' => $client->getId()]);
    }

    /**
     * List all clients : required [ROLE_SUPER_ADMIN]
     *
     * @SWG\Response(
     *     response=200,
     *     description="Return the list of all clients"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="return not found"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="return message : authorization is required"
     * )
     * @SWG\Response(
     *     response=403,
     *     description="return access denied"
     * )
     * @SWG\Response(
     *     response=500,
     *     description="returned on any others errors"
     * )
     * @SWG\Tag(name="Clients")
     * @SEC(name="Bearer")
     *
     * @Rest\Get(
     *     path="/api/clients",
     *     name="client_list"
     *     )
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     * @return                                     \FOS\RestBundle\View\View
     */
    public function listAction()
    {
        $clients = $this->getDoctrine()->getRepository(Client::class)->findAll();

        return $this->generateApiResponse($clients, 200, 'client_list');
    }

    /**
     * Create a new Client resource : required [ROLE_SUPER_ADMIN]
     *
     * @SWG\Response(
     *     response=201,
     *     description="Return the created client resource",
     * @Model(type=Client::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="return validation error(s) or message that Invalid Json format was sent"
     * )
     * @SWG\Response(
     *     response=403,
     *     description="return access denied"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="return not found"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="return message : authorization is required"
     * )
     * @SWG\Response(
     *     response=500,
     *     description="returned on any others errors"
     * )
     * @SWG\Parameter(
     *     name="name",
     *     in="formData",
     *     type="string",
     *     description="The name of the client"
     * )
     *
     * @SWG\Tag(name="Clients")
     *
     * @SEC(name="Bearer")
     *
     * @Rest\Post(
     *      path="/api/clients",
     *      name="client_add"
     * )
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     *
     * @param Request       $request
     * @param ClientManager $clientManager
     *
     * @return \FOS\RestBundle\View\View
     */
    public function createAction(Request $request, ClientManager $clientManager)
    {
        $data = $clientManager->registerClient($request);
        if (is_array($data)) {
            return $this->throwApiProblemValidationException($data);
        }
        return $this->generateApiResponse($data, 201, 'client_add');
    }

    /**
     * Create a new client Admin resource : required [ROLE_SUPER_ADMIN]
     *
     * @SWG\Response(
     *     response=201,
     *     description="Return the created client resource",
     * @Model(type=App\Entity\User::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="return validation error(s) or message that Invalid Json format was sent"
     * )
     * @SWG\Response(
     *     response=403,
     *     description="return access denied"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="return not found"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="return message : authorization is required"
     * )
     * @SWG\Response(
     *     response=500,
     *     description="returned on any others errors"
     * )
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The unique id of the client"
     * )
     * @SWG\Parameter(
     *     name="username",
     *     in="formData",
     *     type="string",
     *     description="The name of the admin"
     * )
     * @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     type="string",
     *     description="The email of the admin"
     * )
     * @SWG\Parameter(
     *     name="plainPassword",
     *     in="formData",
     *     type="string",
     *     description="The password of the admin"
     * )
     * @SWG\Tag(name="Clients")
     * @SEC(name="Bearer")
     *
     * @Rest\Post(
     *     path="/api/clients/{id}/admin",
     *     name="admin_add",
     *     requirements={"id"="\d+"}
     * )
     *
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     *
     * @param Request     $request
     * @param UserManager $userManager
     * @param $id
     *
     * @return \FOS\RestBundle\View\View
     */
    public function createAdminAction(Request $request, UserManager $userManager, $id)
    {
        $data = $userManager->registerAdmin($request, $id);
        if (is_array($data)) {
            return $this->throwApiProblemValidationException($data);
        }
        return $this->generateApiResponse($data, 201, 'admin_add', ['id' => $id]);
    }
}
