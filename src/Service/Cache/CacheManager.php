<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 24/04/2018
 * Time: 14:33
 */

namespace App\Service\Cache;

use App\Entity\Phone;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class CacheManager
{
    protected $cacheService;
    protected $pg;

    const PHONE_LIST = 'api_phones';
    const PHONE_LIST_MARK = 'api_phones_marks';
    const USER_LIST = 'api_users';

    public function __construct(CacheService $cacheService, $pagination)
    {
        $this->cacheService = $cacheService;
        $this->pg = $pagination;
    }

    /**
     * @param $data
     * @param Request $request
     * @param $param
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function saveResourcesOnCache($data, Request $request, $param = null)
    {
        $formatedUri = is_null($param) ?
            $this->cacheService->setCacheKey($request->getRequestUri()) :
            $this->cacheService->setCacheKey($request->getRequestUri() . '_' . $param);

        $this->cacheService->cacheData($formatedUri, $data);
    }

    /**
     * @param $key
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function deleteCachedResources($key)
    {
        $this->cacheService->deleteItem($key);
    }

    /**
     * @param Phone $phone
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function cleanCachedPhonesResources(Phone $phone)
    {
        $this->deleteCachedResources(self::PHONE_LIST . '_' . $phone->getId());
        $this->deleteCachedResources(self::PHONE_LIST_MARK . '_' . $phone->getMark());
        $this->deleteCachedResources(self::PHONE_LIST);

        $i = 1;
        while ($this->cacheService->isCached(self::PHONE_LIST . '_page_' . $i . '_limit_' . $this->pg)) {
            $this->deleteCachedResources(self::PHONE_LIST. '_page_' . $i . '_limit_' . $this->pg);
            $i++;
        }
    }

    /**
     * @param User $user
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function cleanCachedUsersResources(User $user)
    {
        $cId = $user->getClient()->getId();

        $this->deleteCachedResources(self::USER_LIST . '_' . $user->getId() .'_' . $cId);
        $this->deleteCachedResources(self::USER_LIST . '_' . $cId);
        $i = 1;

        while ($this->cacheService->isCached(self::USER_LIST.'_page_'.$i.'_limit_' . $this->pg .'_'. $cId)) {
            $this->deleteCachedResources(self::USER_LIST. '_page_' . $i . '_limit_' . $this->pg .'_'. $cId);
            $i++;
        }
    }
}
