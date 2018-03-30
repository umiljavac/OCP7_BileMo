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
use App\Form\Type\UserRegistrationType;
use App\Security\JwtTokenAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\FormInterface;



class UserManager
{
    private $em;
    private $repository;
    private $formFactory;
    private $passwordEncoder;
    private $authenticator;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $em,
        JwtTokenAuthenticator $authenticator
    ) {
        $this->em = $em;
        $this->repository = $em->getRepository(User::class);
        $this->formFactory = $formFactory;
        $this->passwordEncoder = $passwordEncoder;
        $this->authenticator = $authenticator;
    }

    /**
     * @param Request $request
     * @param $id
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getOneUserByClient(Request $request, $id)
    {
        $clientId = $this->getClientId($request);
        $searchedUser = $this->repository->findOneByClient($clientId, $id);
        return $searchedUser;
    }

    public function getAllUsersByClient(Request $request)
    {
        $clientId = $this->getClientId($request);
        $clientUsers = $this->repository->findByClient($clientId);
        return $clientUsers;
    }

    public function getClient(Request $request)
    {
        $clientId = $this->getClientId($request);
        $client = $this->em->getRepository(Client::class)->find($clientId);
        return $client;
    }

    public function getClientId(Request $request)
    {
        $userData = $this->authenticator->getUserUtils($request);
        return $userData['client'];
    }

    public function registerUser(Request $request)
    {
        $client = $this->getClient($request);
        $user = new User();
        $form = $this->createForm(UserRegistrationType::class, $user);
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

        $errors = $this->getErrorsFromForm($form);
        $data = [
            'type' => 'validation_error',
            'title' => 'There was a validation error',
            'errors' => $errors
        ];
        return $data;
    }

    public function deleteUser($user)
    {
        $this->em->remove($user);
        $this->em->flush();
    }

    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }
        return $errors;
    }

    /**
     * @param $type
     * @param null $data
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createForm($type, $data = null, array $options = array())
    {
        return $this->formFactory->create($type, $data, $options);
    }
}
