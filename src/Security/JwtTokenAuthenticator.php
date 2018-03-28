<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 27/03/2018
 * Time: 21:28
 */

namespace App\Security;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class JwtTokenAuthenticator extends AbstractGuardAuthenticator
{

    /**
     * @var JWTEncoderInterface
     */
    private $jwtEncoder;
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(JWTEncoderInterface $jwtEncoder, EntityManagerInterface $em)
    {

        $this->jwtEncoder = $jwtEncoder;
        $this->em = $em;
    }

    public function supports(Request $request)
    {
        return $request->headers->has('Authorization');
    }

    public function getCredentials(Request $request)
    {
        $extractor = new AuthorizationHeaderTokenExtractor(
            'Bearer',
            'Authorization'
        );

        $token = $extractor->extract($request);

        if (!$token) {
            return;
        }

        return $token;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $data = $this->jwtEncoder->decode($credentials);
        } catch (JWTDecodeFailureException $e) {
            // if you want to, use can use $e->getReason() to find out which of the 3 possible things went wrong
            // and tweak the message accordingly
            // https://github.com/lexik/LexikJWTAuthenticationBundle/blob/05e15967f4dab94c8a75b275692d928a2fbf6d18/Exception/JWTDecodeFailureException.php

            throw new CustomUserMessageAuthenticationException('Invalid Token');
        }
        $username = $data['username'];
        return $this->em
            ->getRepository('App:User')
            ->findOneBy(['username' => $username]);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // wip
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // do nothing call the controller
    }

    public function supportsRememberMe()
    {
        return false;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse([
            'error' => 'authorization required : did you send a valid Authorization header ? Re-login will probably fix that !'
        ], 401);
    }

    // just added : must confirm
    public function getUserUtils(Request $request)
    {
        $token = $this->getCredentials($request);
        try {
            $data = $this->jwtEncoder->decode($token);
        } catch (JWTDecodeFailureException $e) {

            throw new CustomUserMessageAuthenticationException('Invalid Token');
        }
        return $data;
    }
}
