<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 23/03/2018
 * Time: 09:46
 */

namespace App\Controller;

use App\Entity\Phone;
use App\Service\EntityManager\PhoneManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use Hateoas\Configuration\Route;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\Factory\PagerfantaFactory;
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
     * @Rest\View(
     *     serializerGroups = {"detail"}
     * )
     */
    public function showAction(Phone $phone)
    {
        return $this->generateApiView($phone, 200, 'phone_show', ['id' => $phone->getId()]);
    }

    /**
     * @Rest\Get(
     *     path="/api/phones",
     *     name="phone_list"
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
     *     default="5",
     *     description="The pagination limit."
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements={"\d+"},
     *     default="0",
     *     description="The pagination offset."
     * )
     * @Rest\QueryParam(
     *     name="page",
     *     requirements={"\d+"},
     *     default="1",
     *     description="The current page."
     * )
     * @Rest\View(serializerGroups = {"detail, list"})
     * @return mixed
     * @Security("is_granted('ROLE_USER')")
     */
    public function listAction(PhoneManager $phoneManager)
    {
        $pagerPackage = $phoneManager->getPager();

        $pagerfantaFactory = new PagerfantaFactory();

        $paginatedCollection = $pagerfantaFactory->createRepresentation(
            $pagerPackage['pager'],
            new Route('phone_list', array('keyword' => $pagerPackage['keyword'])),
            new CollectionRepresentation($pagerPackage['pager']->getCurrentPageResults(),
            'phones',
            'phones'
            )
        );
        return $this->generateCustomView($paginatedCollection, 200, 'phone_list');
    }

    /**
     * @Rest\Post(
     *     path="/api/phones",
     *     name="phone_add"
     * )
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     * @Rest\View()
     */
    public function createAction(Request $request, PhoneManager $phoneManager)
    {
        $data = $phoneManager->addPhone($request);
        if (is_array($data)) {
            $this->throwApiProblemValidationException($data);
        }

        return $this->generateApiView($data, 201, 'phone_add');
    }

    /**
     * @Rest\Patch(
     *     path="/api/phones/{id}",
     *     name="phone_update_patch",
     *     requirements={"id"="\d+"}
     * )
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     * @Rest\View()
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
        return $this->generateApiView($data, 200, 'phone_update_patch', ['id' => $phone->getId()]);
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
        return $this->generateApiView($data,200, 'phone_update_put', ['id' => $phone->getId()]);
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

    /**
     * * @Rest\Get(
     *     path="/api/phones/marks/{mark}",
     *     name="phone_list_mark",
     *     requirements={"mark"="\w+"}
     * )
     * @Rest\View(
     *     serializerGroups = {"mark"}
     * )
     * @param $mark
     */
    public function listPhonesByMark($mark, PhoneManager $phoneManager)
    {
        return $this->generateApiView(
            $phoneManager->listPhonesByMark($mark),
            200,
            'phone_list_mark', ['mark' => $mark]
            );
    }
}
