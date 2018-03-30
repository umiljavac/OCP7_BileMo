<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 29/03/2018
 * Time: 20:23
 */

namespace App\Service\EntityManager;


use App\Entity\Client;
use App\Entity\User;
use App\Security\JwtTokenAuthenticator;
use App\Service\Helper\FormHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class UserManager
{
    private $em;
    private $repository;
    private $passwordEncoder;
    private $authenticator;
    private $formHelper;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $em,
        JwtTokenAuthenticator $authenticator,
        FormHelper $formHelper
    )
    {
        $this->em = $em;
        $this->repository = $em->getRepository(User::class);
        $this->passwordEncoder = $passwordEncoder;
        $this->authenticator = $authenticator;
        $this->formHelper = $formHelper;
    }

    /**
     * @param Request $request
     * @param $id
     * @return User|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getOneUserByClient(Request $request, $id)
    {
        $clientId = $this->getClientId($request);
        $searchedUser = $this->repository->findOneByClient($clientId, $id);
        return $searchedUser;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getAllUsersByClient(Request $request)
    {
        $clientId = $this->getClientId($request);
        $clientUsers = $this->repository->findByClient($clientId);
        return $clientUsers;
    }

    /**
     * @param Request $request
     * @return Client|null|object
     */
    public function getClient(Request $request)
    {
        $clientId = $this->getClientId($request);
        $client = $this->em->getRepository(Client::class)->find($clientId);
        return $client;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getClientId(Request $request)
    {
        $userData = $this->authenticator->getUserUtils($request);
        return $userData['client'];
    }

    /**
     * @param Request $request
     * @return User|array
     */
    public function registerUser(Request $request)
    {
        $client = $this->getClient($request);
        $user = new User();
        $form = $this->formHelper->createUserRegistrationForm($user);
        $form->submit($request->request->all(), true);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setClient($client);
            $user->setRoles('ROLE_USER');
            $this->em->persist($user);
            $this->em->flush();
            return $user;
        }
        return $this->formHelper->getFormDataErrors($form);
    }

    public function deleteUser($user)
    {
        $this->em->remove($user);
        $this->em->flush();
    }
}