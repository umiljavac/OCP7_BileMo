<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 28/03/2018
 * Time: 09:59
 */

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\BaseTest;
use GuzzleHttp\Exception\ClientException;

class UserControllerTest extends BaseTest
{
    /**
     * an exception is thrown cause user must have a 'ROLE_ADMIN'
     */
    public function testFromUserShowAction()
    {
        $this->expectException(ClientException::class);
        $this->client->request('GET', self::URI_USERS . '/5', [
            'headers' => $this->generateAuthHeaders(self::USER)
        ]);
    }

    public function testFromAdminShowAction()
    {
        $response = $this->client->request('GET', self::URI_USERS . '/4', [
            'headers' => $this->generateAuthHeaders(self::ADMIN)
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testListAction()
    {
        $response = $this->client->request('GET', self::URI_USERS, [
            'headers' => $this->generateAuthHeaders(self::ADMIN)
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(10, json_decode((string) $response->getBody()));
    }

    public function testCreateAction()
    {
        $response = $this->client->request('POST', self::URI_USERS, [
            'headers' => $this->generateAuthHeaders(self::ADMIN),
            'form_params' => [
                'username' => 'toto',
                'email' => 'toto@gmail.com',
                'plainPassword' => 'toto'
                ]
        ]);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testValidationErrors()
    {
        $this->expectException(ClientException::class);
        $exception = $this->client->request('POST', self::URI_USERS, [
            'headers' => $this->generateAuthHeaders(self::ADMIN),
            'form_params' => [
                'username' => 'titi',
                'plainPassword' => 'titi'
            ]
        ]);
        $this->assertEquals('application/problem+json', $exception->getResponse()->getHeader('Content-type'));
        $this->assertEquals(400, $exception->getResponse()->getBody('status'));
        $this->assertEquals('validation_error', $exception->getResponse()->getBody('type'));
    }

    public function testDeleteAction()
    {
        $response = $this->client->request('DELETE', self::URI_USERS . '/22', [
            'headers' => $this->generateAuthHeaders(self::ADMIN)
        ]);

        $this->assertEquals(204, $response->getStatusCode());
    }

    public function testDeleteForeignUserClientAction()
    {
        $this->expectException(ClientException::class);
        $this->client->request('DELETE', self::URI_USERS . '/14', [
            'headers' => $this->generateAuthHeaders(self::ADMIN)
        ]);
    }

    public function testDisableUnableUserAccountAction()
    {
        $response = $this->client->request('PATCH', self::URI_USERS . '/21', [
            'headers' => $this->generateAuthHeaders(self::SUPER_ADMIN)
        ]);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDisableUnableSuperAdminAccountAction()
    {
         $this->expectException(ClientException::class);
         $this->client->request('PATCH', self::URI_USERS . '/1', [
            'headers' => $this->generateAuthHeaders(self::SUPER_ADMIN)
        ]);
    }

    public function testCreateAdminAction()
    {
        $response = $this->client->request('POST', self::URI_CLIENT . '/4/admin', [
            'headers' => $this->generateAuthHeaders(self::SUPER_ADMIN),
            'form_params' => [
                'username' => 'admin4',
                'email' => 'admin4@gmail.com',
                'plainPassword' => 'admin4'
            ]
        ]);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function test404Exception()
    {
        $this->expectException(ClientException::class);
        $exception = $this->client->request('GET', self::URI_USERS . '/123', [
            'headers' => $this->generateAuthHeaders(self::ADMIN)
        ]);

        $this->assertEquals('application/problem+json', $exception->getResponse()->getHeader('Content-type'));
        $this->assertEquals(404, $exception->getResponse()->getBody('status'));
        $this->assertEquals('about:blank', $exception->getResponse()->getBody('type'));
    }
}
