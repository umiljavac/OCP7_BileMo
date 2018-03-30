<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 28/03/2018
 * Time: 09:57
 */

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Form\Type\UserRegistrationType;
use App\Security\JwtTokenAuthenticator;
use App\Service\EntityManager\UserManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\ConstraintViolationList;

class ClientUserController extends FOSRestController
{
    /**
     * @Rest\Get(
     *     path="/api/clients/{id}",
     *     name="client_show",
     *     requirements={"id"="\d+"}
     * )
     * @Security("is_granted('ROLE_ADMIN')")
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function showAction($id, Request $request, UserManager $userManager)
    {
        $searchedUser = $userManager->getOneUserByClient($request, $id);
        if (!$searchedUser) {
            return new JsonResponse(['message' => 'This user doesn\'t exists']);
        }
       return $this->generateCustomView($searchedUser, 200, 'client_show', ['id' => $id]);
    }

    /**
     * @Rest\Get(
     *     path="/api/clients/all",
     *     name="client_list_all"
     * )
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function listAction(Request $request, UserManager $userManager)
    {
        $clientUsers = $userManager->getAllUsersByClient($request);
        return $this->generateCustomView($clientUsers, 200, 'client_list_all');
    }

    /**
     * @param Request $request
     * @param JwtTokenAuthenticator $authenticator
     * @Rest\Post(
     *     path="/api/clients",
     *     name="client_add_user"
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function createAction(Request $request, UserManager $userManager)
    {
        $data = $userManager->registerUser($request);
        if (is_array($data)) {
            return new JsonResponse($data, 400);
        }
        return $this->generateCustomView($data, 201, 'client_add_user');
    }

    /**
     * @param User $user
     * @Rest\Delete(
     *     path="/api/clients/{id}",
     *     name="client_delete_user",
     *     requirements={"id"="\d+"}
     * )
     * @Rest\View(statusCode=204)
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function deleteAction(User $user, Request $request, UserManager $userManager)
    {
        if(!$user) {
            throw new NotFoundHttpException('This user does\'t exist');
        }
        if ($user->getClient()->getId() !== $userManager->getClientId($request)) {
            return new JsonResponse(['message' => 'you\'re not allowed to delete this user .'], Response::HTTP_FORBIDDEN);
        }
        $userManager->deleteUser($user);
        return;
    }

    private function generateCustomView($data, $statusCode, $route, $routeOption = [])
    {
        $view = $this->view($data, $statusCode)
            ->setHeader('Location', $this->generateUrl($route, $routeOption));
        return $this->handleView($view);
    }
}
