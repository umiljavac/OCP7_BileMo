<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 28/03/2018
 * Time: 09:57
 */

namespace App\Controller;

use App\Entity\User;
use App\Security\JwtTokenAuthenticator;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ClientUserController extends FOSRestController
{
    /**
     * @Rest\Get(
     *     path="/api/clients/{id}",
     *     name="client_show",
     *     requirements={"id"="\d+"}
     * )
     * @Security("has_role('ROLE_ADMIN')")
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function showAction($id, Request $request, JwtTokenAuthenticator $authenticator)
    {
        $userData = $authenticator->getUserUtils($request);
        $client = $userData['client'];
        $repository = $this->getDoctrine()->getRepository(User::class);
        $searchedUser = $repository->findOneByClient($client, $id);
        if (!$searchedUser) {
            return new JsonResponse(['message' => 'This user doesn\'t exists']);
        }
        $view = $this->view($searchedUser, 200)
            ->setHeader('Location', $this->generateUrl('client_show', ['id' => $id]));
        return $this->handleView($view);
    }

    public function listAction()
    {

    }

    public function createAction()
    {

    }

    public function deleteAction()
    {

    }
}
