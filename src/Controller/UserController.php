<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 28/03/2018
 * Time: 09:57
 */

namespace App\Controller;

use App\Entity\User;
use App\Service\EntityManager\UserManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use Hateoas\Configuration\Route;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends BaseController
{
    /**
     * @param User $user
     * @param Request $request
     * @param UserManager $userManager
     *  * @Rest\Get(
     *     path="/api/users/{id}",
     *     name="user_show",
     *     requirements={"id"="\d+"}
     * )
     * @return JsonResponse|Response
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     */
    public function showAction(User $user, Request $request, UserManager $userManager)
    {
        if ($this->getUser()->getRoles() !== ['ROLE_SUPER_ADMIN']) {
            if ($user->getClient() !== $userManager->getClient($request)) {
                return new JsonResponse(
                    ['message' => 'you\'re not allowed to reach this user.'],
                    Response::HTTP_FORBIDDEN
                );
            }
        }
       return $this->generateCustomView($user, 200, 'user_show', ['id' => $user->getId()]);
    }

    /**
     * @Rest\Get(
     *     path="/api/users",
     *     name="user_list"
     * )
     * @Rest\QueryParam(
     *     name="keyword",
     *     requirements={"[a-zA-Z0-9]"},
     *     nullable=true,
     *     description="The keyword to search for."
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements={"asc|desc"},
     *     nullable=true,
     *     description="Sort order (asc or desc)."
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements={"\d+"},
     *     default="5",
     *     description="The pagination limit."
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements={"\d+"},
     *     default="0",
     *     description="The pagination offset."
     * )
     * @Rest\QueryParam(
     *     name="page",
     *     requirements={"\d+"},
     *     default="1",
     *     description="The current page."
     * )
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     */
    public function listAction(Request $request, UserManager $userManager)
    {
        $pagerPackage = $userManager->getPager($request);

        $pagerfantaFactory = new PagerfantaFactory();

        $paginatedCollection = $pagerfantaFactory->createRepresentation(
            $pagerPackage['pager'],
            new Route('user_list', array('keyword' => $pagerPackage['keyword'])),
            new CollectionRepresentation($pagerPackage['pager']->getCurrentPageResults(),
                'users',
                'users'
            )
        );
        return $this->generateCustomView($paginatedCollection, 200, 'user_list');
    }

    /**
     * @param Request $request
     * @param UserManager $userManager
     * @Rest\Post(
     *     path="/api/users",
     *     name="user_add"
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Security("is_granted('ROLE_ADMIN')")
     * @return JsonResponse|Response
     */
    public function createAction(Request $request, UserManager $userManager)
    {
        $data = $userManager->registerUser($request);
        if (is_array($data)) {
            return $this->throwApiProblemValidationException($data);
        }
        return $this->generateCustomView($data, 201, 'user_add');
    }

    /**
     * @param User $user
     * @Rest\Delete(
     *     path="/api/users/{id}",
     *     name="user_delete",
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
            return new JsonResponse(
                ['message' => 'you\'re not allowed to delete this user .'],
                Response::HTTP_FORBIDDEN
            );
        }
        $userManager->deleteUser($user);
        return;
    }

    /**
     * @Rest\Patch(
     *     path="/api/users/{id}",
     *     name="user_switch_active"
     * )
     * @param User $user
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     */
    public function disableEnableAccountAction(User $user)
    {
        if ($user === $this->getUser()) {
            return new JsonResponse(
                ['code' => 403, 'message' => 'Why do you want to disable your super admin account ? Forget it :)'],
                Response::HTTP_FORBIDDEN
            );
        }
        $user->isActive()? $user->setActive(false) : $user->setActive(true);
        $this->getDoctrine()->getManager()->flush();

        return $this->generateCustomView(
            $user, 200, 'user_switch_active',
            ['id' => $user->getId()]
        );
    }
}
