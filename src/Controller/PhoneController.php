<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 23/03/2018
 * Time: 09:46
 */

namespace App\Controller;


use App\Entity\Phone;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class PhoneController extends FOSRestController
{

    /**
     * @param $phone
     * @Rest\Get(
     *     path = "/phones/{id}",
     *     name = "phone_show",
     *     requirements={"id"="\d+"}
     * )
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Phone $phone)
    {
        $view = $this->view($phone, 200)
            ->setHeader('Location', $this->generateUrl('phone_show', ['id' => $phone->getId()]));
        return $this->handleView($view);
    }

    /**
     * @Rest\Get(
     *     path = "/phones",
     *     name = "phone_list",
     * )
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $repository = $this->getDoctrine()->getRepository('App:Phone');
        $phoneList =  $repository->findAll();
        $view = $this->view($phoneList, 200)
            ->setHeader('Location', $this->generateUrl('phone_list'));
        return $this->handleView($view);
    }
}
