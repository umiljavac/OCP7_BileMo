<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 23/03/2018
 * Time: 15:48
 */

namespace App\Controller;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use FOS\RestBundle\Controller\Annotations as Rest;


/**
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController extends FOSRestController
{
    /**
     * @Rest\Post(path="/api/login", name="api_login")
     * @Rest\RequestParam(
     *     name = "username",
     *     description="Enter your username"
     * )
     * @Rest\RequestParam(
     *     name = "password",
     *     description="Enter your password"
     * )
     * @Rest\View(statusCode=200)
     */
    public function apiLogin($username, $password, UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('App:User')->findOneBy(array('username' => $username));
        if ($passwordEncoder->isPasswordValid($user, $password)) {
            $token = $this->get('lexik_jwt_authentication.encoder')
                ->encode([
                    'username' => $user->getUsername(),
                    'roles' => $user->getRoles(),
                    'client' => $user->getClient()->getName(),
                    'exp' => time() + 3600 // 1 hour expiration
                ]);
            return new JsonResponse(['token' => $token]);
        }
        else {
            return new JsonResponse(['message' => 'bad credentials'], Response::HTTP_NOT_FOUND);
        }
    }
}
