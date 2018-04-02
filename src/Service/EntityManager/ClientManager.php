<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 02/04/2018
 * Time: 00:36
 */

namespace App\Service\EntityManager;

use App\Entity\Client;
use App\Service\Helper\FormHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ClientManager
{
    private $em;
    private $formHelper;

    public function __construct(EntityManagerInterface $entityManager, FormHelper $formHelper)
    {
        $this->em = $entityManager;
        $this->formHelper = $formHelper;
    }

    public function registerClient(Request $request)
    {
        $client = new Client();
        $form = $this->formHelper->createClientRegistrationForm($client);
        $form->submit($request->request->all(), true);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($client);
            $this->em->flush();
            return $client;
        }
        return $this->formHelper->getFormDataErrors($form);
    }
}
