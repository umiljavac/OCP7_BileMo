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
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security as SEC;

class UserController extends BaseController
{
    /**
     * Show one user of your team : required [ROLE_ADMIN] or higher
     *
     * @SWG\Response(
     *     response=200,
     *     description="Return one user resource",
     * @Model(type=User::class)
     * )
     * @SWG\Response(
     *     response=404,
     *     description="return not found"
     * )
     * @SWG\Response(
     *     response=403,
     *     description="return message : access denied"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="return message : authorization is required"
     * )
     * @SWG\Response(
     *     response=500,
     *     description="returned on any others errors"
     * )
     * @SWG\Tag(name="Users")
     * @SEC(name="Bearer")
     *
     * @Rest\Get(
     *     path="/api/users/{id}",
     *     name="user_show",
     *     requirements={"id"="\d+"}
     * )
     *
     * @param User $user
     * @param Request $request
     * @param UserManager $userManager
     *
     * @return \FOS\RestBundle\View\View|JsonResponse
     *
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
        return $this->generateApiResponse($user, 200, 'user_show', ['id' => $user->getId()]);
    }

    /**
     * List all users of your team : required [ROLE_ADMIN] or higher
     *
     * @SWG\Response(
     *     response=200,
     *     description="Return a paginated list of all users of your team",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="return not found"
     * )
     * @SWG\Response(
     *     response=403,
     *     description="return message : access denied"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="return message : authorization is required"
     * )
     * @SWG\Response(
     *     response=500,
     *     description="returned on any others errors"
     * )
     * @SWG\Tag(name="Users")
     * @SEC(name="Bearer")
     *
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
     *
     * @param Request $request
     * @param UserManager $userManager
     *
     * @return \FOS\RestBundle\View\View
     *
     * @Security("is_granted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])")
     */
    public function listAction(Request $request, UserManager $userManager)
    {
        $pagerPackage = $userManager->getPager($request);

        $pagerfantaFactory = new PagerfantaFactory();

        $paginatedCollection = $pagerfantaFactory->createRepresentation(
            $pagerPackage['pager'],
            new Route('user_list', array('keyword' => $pagerPackage['keyword'])),
            new CollectionRepresentation(
                $pagerPackage['pager']->getCurrentPageResults(),
                'users',
                'users'
            )
        );
        return $this->generateApiResponse($paginatedCollection, 200, 'user_list');
    }

    /**
     * Create a new user resource : required [ROLE_ADMIN]
     *
     * @SWG\Response(
     *     response=201,
     *     description="Return the created user resource",
     * @Model(type=User::class)
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
     *     name="username",
     *     in="formData",
     *     type="string",
     *     description="The username of user to register"
     * )
     * @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     type="string",
     *     description="The email of user to register"
     * )
     * @SWG\Parameter(
     *     name="plainPassword",
     *     in="formData",
     *     type="string",
     *     description="The password of user to register"
     * )
     * @SWG\Tag(name="Users")
     * @SEC(name="Bearer")
     *
     * @Rest\Post(
     *     path="/api/users",
     *     name="user_add"
     * )
     *
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     *
     * @param Request $request
     * @param UserManager $userManager
     *
     * @return \FOS\RestBundle\View\View
     *
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function createAction(Request $request, UserManager $userManager)
    {
        $data = $userManager->registerUser($request);
        if (is_array($data)) {
            return $this->throwApiProblemValidationException($data);
        }
        return $this->generateApiResponse($data, 201, 'user_add');
    }

    /**
     * Delete one user of your team : required [ROLE_ADMIN]
     *
     * @SWG\Response(
     *     response=204,
     *     description="Return blank",
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
     *     description="return access denied | not allowed to delete this user"
     * )
     * @SWG\Response(
     *     response=500,
     *     description="returned on any others errors"
     * )
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The unique id of the phone to delete"
     * )
     *
     * @SWG\Tag(name="Users")
     * @SEC(name="Bearer")
     *
     * @Rest\Delete(
     *     path="/api/users/{id}",
     *     name="user_delete",
     *     requirements={"id"="\d+"}
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param User $user
     * @param Request $request
     * @param UserManager $userManager
     *
     * @return JsonResponse
     *
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function deleteAction(User $user, Request $request, UserManager $userManager)
    {
        if (!$user) {
            throw new NotFoundHttpException('This user does\'t exist');
        }
        if ($user->getClient()->getId() !== $userManager->getClientId($request) || $user === $this->getUser()) {
            return new JsonResponse(
                ['message' => 'you\'re not allowed to delete this user .'],
                Response::HTTP_FORBIDDEN
            );
        }
        $userManager->deleteUser($user);
        return;
    }

    /**
     * Disable or enable a user account : required [ROLE_SUPER_ADMIN]
     *
     * @SWG\Response(
     *     response=200,
     *     description="Return the user informations",
     * @Model(type=User::class)
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
     *     description="The unique id of the user to disable or enable account"
     * )
     * @SWG\Tag(name="Users")
     * @SEC(name="Bearer")
     *
     * @Rest\Patch(
     *     path="/api/users/{id}",
     *     name="user_switch_active"
     * )
     *
     * @param User $user
     *
     * @return \FOS\RestBundle\View\View|JsonResponse
     *
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

        return $this->generateApiResponse(
            $user,
            200,
            'user_switch_active',
            ['id' => $user->getId()]
        );
    }
}
