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
        $response = $this->client->request('GET', self::URI_CLIENT . '/all', [
            'headers' => $this->generateAuthHeaders(self::ADMIN)
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(10, json_decode((string) $response->getBody()));
    }

    public function testCreateAction()
    {
        $response = $this->client->request('POST', self::URI_CLIENT, [
            'headers' => $this->generateAuthHeaders(self::ADMIN),
            'form_params' => [
                'username' => 'toto',
                'email' => 'toto@gmail.com',
                'plainPassword' => 'toto'
                ]
        ]);

        $this->assertEquals(201, $response->getStatusCode());

    }

    public function testDeleteAction()
    {
        $response = $this->client->request('DELETE', self::URI_CLIENT . '/22', [
            'headers' => $this->generateAuthHeaders(self::ADMIN)
        ]);

        $this->assertEquals(204, $response->getStatusCode());
    }

    public function testDeleteForeignUserClientAction()
    {
        $this->expectException(ClientException::class);
        $this->client->request('DELETE', self::URI_CLIENT . '/14', [
            'headers' => $this->generateAuthHeaders(self::ADMIN)
        ]);
    }
}
