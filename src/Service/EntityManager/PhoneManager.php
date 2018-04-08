<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 08/04/2018
 * Time: 05:54
 */

namespace App\Service\EntityManager;

use App\Entity\Phone;
use App\Service\Helper\FormHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class PhoneManager
{
    private $em;
    private $repository;
    private $formHelper;

    public function __construct(
        EntityManagerInterface $em,
        FormHelper $formHelper
    )
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Phone::class);
        $this->formHelper = $formHelper;
    }

    /**
     * @param Request $request
     * @return Phone|array
     */
    public function addPhone(Request $request)
    {
        $phone = new Phone();
        $form = $this->formHelper->createPhoneForm($phone);

        $form->submit($request->request->all(), true);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($phone);
            $this->em->flush();
            return $phone;
        }
        return $this->formHelper->getFormDataErrors($form);
    }

    /**
     * @param Phone $phone
     */
    public function deletePhone(Phone $phone)
    {
        $this->em->remove($phone);
        $this->em->flush();
    }

    /**
     * @param Phone $phone
     * @param Request $request
     * @param $clearMissing
     * @return Phone|array
     */
    public function updatePhone(Phone $phone, Request $request, $clearMissing)
    {
        $form = $this->formHelper->createPhoneForm($phone);

        $form->submit($request->request->all(), $clearMissing);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            return $phone;
        }
        return $this->formHelper->getFormDataErrors($form);
    }
}
