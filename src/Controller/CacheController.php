<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 23/04/2018
 * Time: 13:54
 */

namespace App\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;

class CacheController
{
    private $data;
    private $key;

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @param $key
     */
    public function setKey($key): void
    {
        $this->key = $key;
    }

    /**
     * @return View
     * @Rest\View()
     */
    public function generateCachedApiResponse()
    {
        return new View(
            $this->data,
            200,
            array(
                'Content-Type' => 'application/hal+json',
                'Location' => $this->key,
                'From-Cache' => 'true'
            )
        );
    }
}
