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
use App\Service\EntityManager\PhoneManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;


class PhoneController extends BaseController
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
    public function showAction(Phone $phone)
    {
        return $this->generateCustomView($phone, 200, 'phone_show', ['id' => $phone->getId()]);
    }

    /**
     * @Rest\Get(
     *     path="/api/phones/all",
     *     name="phone_list_all",
     * )
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("is_granted('ROLE_USER')")
     */
    public function listAction(PhoneManager $phoneManager)
    {
        return $this->generateCustomView($phoneManager->listAll(), 200, 'phone_list_all');
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
    public function listWithCriteriaAction(ParamFetcherInterface $paramFetcher)
    {
        $pager = $this->getDoctrine()->getRepository('App:Phone')->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        return $this->generateCustomView(new Phones($pager), 200, 'phone_list_criteria');
    }

    /**
     * @Rest\Post(
     *     path="/api/phones",
     *     name="phone_add"
     * )
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     */
    public function createAction(Request $request, PhoneManager $phoneManager)
    {
        $data = $phoneManager->addPhone($request);
        if (is_array($data)) {
            $this->throwApiProblemValidationException($data);
        }

        return $this->generateCustomView($data, 201, 'phone_add');
    }

    /**
     * @Rest\Patch(
     *     path="/api/phones/{id}",
     *     name="phone_update_patch",
     *     requirements={"id"="\d+"}
     * )
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     */
    public function patchAction(Phone $phone, Request $request, PhoneManager $phoneManager)
    {
        if(!$phone) {
            $this->createNotFoundException();
        }
        $data = $phoneManager->updatePhone($phone, $request, false);

        if (is_array($data)) {
            $this->throwApiProblemValidationException($data);
        }
        return $this->generateCustomView($data,200, 'phone_update_patch', ['id' => $phone->getId()]);
    }

    /**
     * @Rest\Put(
     *     path="/api/phones/{id}",
     *     name="phone_update_put",
     *     requirements={"id"="\d+"}
     * )
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     */
    public function putAction(Phone $phone, Request $request, PhoneManager $phoneManager)
    {
        if(!$phone) {
            $this->createNotFoundException();
        }
        $data = $phoneManager->updatePhone($phone, $request, true);

        if (is_array($data)) {
            $this->throwApiProblemValidationException($data);
        }
        return $this->generateCustomView($data,200, 'phone_update_put', ['id' => $phone->getId()]);
    }

    /**
     * @Rest\Delete(
     *     path="/api/phones/{id}",
     *     name="phone_delete",
     *     requirements={"id"="\d+"}
     * )
     * @Rest\View(statusCode=204)
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     */
    public function deleteAction(Phone $phone, PhoneManager $phoneManager)
    {
        if (!$phone) {
            $this->createNotFoundException();
        }

        $phoneManager->deletePhone($phone);
    }
}
