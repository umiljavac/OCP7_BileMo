<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 31/03/2018
 * Time: 02:25
 */

namespace App\Tests\Controller;


use App\Tests\BaseTest;

class ClientControllerTest extends BaseTest
{

    public function testGetClientAction()
    {
        $response = $this->client->request('GET', self::URI_CLIENT. '/2', [
            'headers' => $this->generateAuthHeaders(self::SUPER_ADMIN)
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testListAllClientAction()
    {
        $response = $this->client->request('GET', self::URI_CLIENT, [
            'headers' => $this->generateAuthHeaders(self::SUPER_ADMIN)
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }


    public function testCreateAction()
    {
        $response = $this->client->request('POST', self::URI_CLIENT, [
            'headers' => $this->generateAuthHeaders(self::SUPER_ADMIN),
            'form_params' => [
                'name' => 'client4',
            ]
        ]);

        $this->assertEquals(201, $response->getStatusCode());
    }
}
