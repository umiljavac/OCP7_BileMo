<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 23/03/2018
 * Time: 09:46
 */

namespace App\Controller;

use App\Entity\Phone;
use App\Representation\Phones;
use App\Service\Helper\ViewHelper;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class PhoneController extends FOSRestController
{

    /**
     * @param Phone $phone
     * @Rest\Get(
     *     path="/api/phones/{id}",
     *     name="phone_show",
     *     requirements={"id"="\d+"}
     * )
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("is_granted('ROLE_USER')")
     */
    public function showAction(Phone $phone, ViewHelper $view)
    {
        return $view->generateCustomView($phone, 200, 'phone_show', ['id' => $phone->getId()]);
    }

    /**
     * @Rest\Get(
     *     path="/api/phones/all",
     *     name="phone_list_all",
     * )
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("is_granted('ROLE_USER')")
     */
    public function listAction(ViewHelper $view)
    {
        $repository = $this->getDoctrine()->getRepository('App:Phone');
        $phoneList =  $repository->findAll();

        return $view->generateCustomView($phoneList, 200, 'phone_list_all');
    }

    /**
     * @param ParamFetcherInterface $paramFetcher
     * @Rest\Get(
     *     path="/api/phones",
     *     name="phone_list_criteria"
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
     *     default="20",
     *     description="The pagination offset."
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements={"\d+"},
     *     default="0",
     *     description="The pagination offset."
     * )
     * @Rest\View()
     * @return mixed
     * @Security("is_granted('ROLE_USER')")
     */
    public function listWithCriteriaAction(ParamFetcherInterface $paramFetcher, ViewHelper $view)
    {
        $pager = $this->getDoctrine()->getRepository('App:Phone')->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        return $view->generateCustomView(new Phones($pager), 200, 'phone_list_criteria');
    }
}
