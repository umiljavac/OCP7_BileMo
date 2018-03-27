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
     *     path="/phones/{id}",
     *     name="phone_show",
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
     *     path="/phones/all",
     *     name="phone_list_all",
     * )
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $repository = $this->getDoctrine()->getRepository('App:Phone');
        $phoneList =  $repository->findAll();
        $view = $this->view($phoneList, 200)
            ->setHeader('Location', $this->generateUrl('phone_list_all'));
        return $this->handleView($view);
    }

    /**
     * @param ParamFetcherInterface $paramFetcher
     * @Rest\Get(
     *     path="/phones",
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
     */
    public function listWithCriteriaAction(ParamFetcherInterface $paramFetcher)
    {
        $pager = $this->getDoctrine()->getRepository('App:Phone')->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

       // return $pager->getCurrentPageResults();
      //  $view = $this->view($pager->getCurrentPageResults(), 200)
        $view = $this->view(new Phones($pager), 200)
            ->setHeader('Location', $this->generateUrl('phone_list_criteria'));
        return $this->handleView($view);
    }
}
