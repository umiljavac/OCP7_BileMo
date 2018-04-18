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
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpFoundation\Request;

class PhoneManager
{
    private $em;
    private $repository;
    private $formHelper;
    private $paramFetcher;

    public function __construct(
        EntityManagerInterface $em,
        FormHelper $formHelper,
        ParamFetcherInterface $paramFetcher
    ) {
        $this->em = $em;
        $this->repository = $em->getRepository(Phone::class);
        $this->formHelper = $formHelper;
        $this->paramFetcher = $paramFetcher;
    }

    public function getPager()
    {
        $pager = $this->repository->search(
            $this->paramFetcher->get('keyword'),
            $this->paramFetcher->get('order'),
            $this->paramFetcher->get('limit'),
            $this->paramFetcher->get('offset'),
            $this->paramFetcher->get('page')
        );

        foreach ($pager->getCurrentPageResults() as $phoneObject) {
            $desc = substr($phoneObject->getDescription(), 0, 90);
            $phoneObject->setDescription($desc . ' ...');
        }

        $pagerPackage['pager'] = $pager;
        $pagerPackage['keyword'] = $this->paramFetcher->get('keyword');
        return $pagerPackage;
    }

    public function listPhonesByMark($mark)
    {
        return $this->repository->listPhonesByMark($mark);
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
     * @param Phone        $phone
     * @param Request      $request
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
