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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security as SEC;

class PhoneController extends BaseController
{

    /**
     * Show one phone model
     *
     * @SWG\Response(
     *     response=200,
     *     description="Return the full description of a phone",
     * @Model(type=Phone::class, groups={"detail"})
     * )
     * @SWG\Response(
     *     response=404,
     *     description="return not found"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="return message : authorization is required"
     * )
     * @SWG\Response(
     *     response=500,
     *     description="returned on any others errors"
     * )
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The unique id of the phone to show"
     * )
     * @SWG\Tag(name="Phones")
     * @SEC(name="Bearer")
     *
     * @Rest\Get(
     *     path="/api/phones/{id}",
     *     name="phone_show",
     *     requirements={"id"="\d+"}
     * )
     * @Security("is_granted('ROLE_USER')")
     * @Rest\View(
     *     serializerGroups = {"detail"}
     * )
     * @param                               Phone $phone
     * @return                              \FOS\RestBundle\View\View
     */
    public function showAction(Phone $phone)
    {
        return $this->generateApiResponse($phone, 200, 'phone_show', ['id' => $phone->getId()]);
    }


    /**
     * List all phones models
     *
     * @SWG\Response(
     *     response=200,
     *     description="Return a paginated list of all phones models"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="return not found"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="return message : authorization is required"
     * )
     * @SWG\Response(
     *     response=500,
     *     description="returned on any others errors"
     * )
     * @SWG\Tag(name="Phones")
     * @SEC(name="Bearer")
     *
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
     * @param            PhoneManager $phoneManager
     * @return           mixed* @Security("is_granted('ROLE_USER')")
     */
    public function listAction(PhoneManager $phoneManager)
    {
        $pagerPackage = $phoneManager->getPager();

        $pagerfantaFactory = new PagerfantaFactory();

        $paginatedCollection = $pagerfantaFactory->createRepresentation(
            $pagerPackage['pager'],
            new Route('phone_list', array('keyword' => $pagerPackage['keyword'])),
            new CollectionRepresentation(
                $pagerPackage['pager']->getCurrentPageResults(),
                'phones',
                'phones'
            )
        );
        return $this->generateApiResponse($paginatedCollection, 200, 'phone_list');
    }

    /**
     * List all the phones of the same mark
     *
     * @SWG\Response(
     *     response=200,
     *     description="Return the full list of phones of the same mark ",
     * @Model(type=Phone::class, groups={"mark"})
     * )
     * @SWG\Response(
     *     response=404,
     *     description="return not found"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="return message : authorization is required"
     * )
     * @SWG\Response(
     *     response=500,
     *     description="returned on any others errors"
     * )
     * @SWG\Parameter(
     *     name="mark",
     *     in="path",
     *     type="string",
     *     description="The mark of requested phones"
     * )
     * @SWG\Tag(name="Phones")
     * @SEC(name="Bearer")
     *
     * @Rest\Get(
     *     path="/api/phones/marks/{mark}",
     *     name="phone_list_mark",
     *     requirements={"mark"="\w+"}
     * )
     * @Rest\View(
     *     serializerGroups = {"mark"}
     * )
     * @param      $mark
     * @param      PhoneManager $phoneManager
     * @return     \FOS\RestBundle\View\View
     */
    public function listPhonesByMark($mark, PhoneManager $phoneManager)
    {
        return $this->generateApiResponse(
            $phoneManager->listPhonesByMark($mark),
            200,
            'phone_list_mark',
            ['mark' => $mark]
        );
    }

    /**
     * Create a new phone resource : required [ROLE_SUPER_ADMIN]
     *
     * @SWG\Response(
     *     response=201,
     *     description="Return the created phone resource",
     * @Model(type=Phone::class, groups={"detail"})
     * )
     * @SWG\Response(
     *     response=400,
     *     description="return validation error(s) or message that Invalid Json format was sent"
     * )
     * @SWG\Response(
     *     response=403,
     *     description="return access denied"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="return not found"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="return message : authorization is required"
     * )
     * @SWG\Response(
     *     response=500,
     *     description="returned on any others errors"
     * )
     * @SWG\Parameter(
     *     name="mark",
     *     in="formData",
     *     type="string",
     *     description="The mark of the phone"
     * )
     * @SWG\Parameter(
     *     name="reference",
     *     in="formData",
     *     type="string",
     *     description="The reference of the phone"
     * )
     * @SWG\Parameter(
     *     name="description",
     *     in="formData",
     *     type="string",
     *     description="The full description of the phone"
     * )
     * @SWG\Parameter(
     *     name="price",
     *     in="formData",
     *     type="number",
     *     description="The price of the phone"
     * )
     * @SWG\Tag(name="Phones")
     * @SEC(name="Bearer")
     *
     * @Rest\Post(
     *     path="/api/phones",
     *     name="phone_add"
     * )
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     * @Rest\View()
     * @param                                      Request      $request
     * @param                                      PhoneManager $phoneManager
     * @return                                     \FOS\RestBundle\View\View
     */
    public function createAction(Request $request, PhoneManager $phoneManager)
    {
        $data = $phoneManager->addPhone($request);
        if (is_array($data)) {
            $this->throwApiProblemValidationException($data);
        }

        return $this->generateApiResponse($data, 201, 'phone_add');
    }

    /**
     * Partial update off a phone resource : required [ROLE_SUPER_ADMIN]
     *
     * @SWG\Response(
     *     response=200,
     *     description="Return the updated phone resource",
     * @Model(type=Phone::class, groups={"detail"})
     * )
     * @SWG\Response(
     *     response=400,
     *     description="return validation error(s) or message that Invalid Json format was sent"
     * )
     * @SWG\Response(
     *     response=403,
     *     description="return access denied"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="return not found"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="return message : authorization is required"
     * )
     * @SWG\Response(
     *     response=500,
     *     description="returned on any others errors"
     * )
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The unique id of the phone to update"
     * )
     * @SWG\Tag(name="Phones")
     * @SEC(name="Bearer")
     *
     * @Rest\Patch(
     *     path="/api/phones/{id}",
     *     name="phone_update_patch",
     *     requirements={"id"="\d+"}
     * )
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     * @Rest\View()
     * @param                                      Phone        $phone
     * @param                                      Request      $request
     * @param                                      PhoneManager $phoneManager
     * @return                                     \FOS\RestBundle\View\View
     */
    public function patchAction(Phone $phone, Request $request, PhoneManager $phoneManager)
    {
        if (!$phone) {
            $this->createNotFoundException();
        }
        $data = $phoneManager->updatePhone($phone, $request, false);

        if (is_array($data)) {
            $this->throwApiProblemValidationException($data);
        }
        return $this->generateApiResponse($data, 200, 'phone_update_patch', ['id' => $phone->getId()]);
    }

    /**
     * Full update off a phone resource : required [ROLE_SUPER_ADMIN]
     *
     * @SWG\Response(
     *     response=200,
     *     description="Return the updated phone resource",
     * @Model(type=Phone::class, groups={"detail"})
     * )
     * @SWG\Response(
     *     response=400,
     *     description="return validation error(s) or message that Invalid Json format was sent"
     * )
     * @SWG\Response(
     *     response=403,
     *     description="return access denied"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="return not found"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="return message : authorization is required"
     * )
     * @SWG\Response(
     *     response=500,
     *     description="returned on any others errors"
     * )
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The unique id of the phone to update"
     * )
     * @SWG\Tag(name="Phones")
     * @SEC(name="Bearer")
     *
     * @Rest\Put(
     *     path="/api/phones/{id}",
     *     name="phone_update_put",
     *     requirements={"id"="\d+"}
     * )
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     * @param                                      Phone        $phone
     * @param                                      Request      $request
     * @param                                      PhoneManager $phoneManager
     * @return                                     \FOS\RestBundle\View\View
     */
    public function putAction(Phone $phone, Request $request, PhoneManager $phoneManager)
    {
        if (!$phone) {
            $this->createNotFoundException();
        }
        $data = $phoneManager->updatePhone($phone, $request, true);

        if (is_array($data)) {
            $this->throwApiProblemValidationException($data);
        }
        return $this->generateApiResponse($data, 200, 'phone_update_put', ['id' => $phone->getId()]);
    }

    /**
     * Delete a phone resource : required [ROLE_SUPER_ADMIN]
     *
     * @SWG\Response(
     *     response=204,
     *     description="Return blank"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="return not found"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="return message : authorization is required"
     * )
     * @SWG\Response(
     *     response=403,
     *     description="return access denied"
     * )
     * @SWG\Response(
     *     response=500,
     *     description="returned on any others errors"
     * )
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The unique id of the phone to delete"
     * )
     * @SWG\Tag(name="Phones")
     * @SEC(name="Bearer")
     *
     * @Rest\Delete(
     *     path="/api/phones/{id}",
     *     name="phone_delete",
     *     requirements={"id"="\d+"}
     * )
     * @Rest\View(statusCode=204)
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     * @param                                      Phone        $phone
     * @param                                      PhoneManager $phoneManager
     */
    public function deleteAction(Phone $phone, PhoneManager $phoneManager)
    {
        if (!$phone) {
            $this->createNotFoundException();
        }

        $phoneManager->deletePhone($phone);
    }
}
