<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 28/03/2018
 * Time: 09:59
 */

namespace App\Tests\Controller;

use App\Tests\BaseTest;
use GuzzleHttp\Exception\ClientException;

class ClientUserControllerTest extends BaseTest
{
    /**
     * an exception is thrown cause user must have a 'ROLE_ADMIN'
     */
    public function testFromUserShowAction()
    {
        $this->expectException(ClientException::class);
        $this->client->request('GET', self::URI_CLIENT . '/5', [
            'headers' => $this->generateAuthHeaders(self::USER)
        ]);
    }

    public function testFromAdminShowAction()
    {
        $response = $this->client->request('GET', self::URI_CLIENT . '/4', [
            'headers' => $this->generateAuthHeaders(self::ADMIN)
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testListAction()
    {

    }

    public function testCreateAction()
    {

    }

    public function testDeleteAction()
    {

    }
}