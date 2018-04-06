<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 23/03/2018
 * Time: 15:49
 */

namespace App\Tests\Controller;


use App\Tests\BaseTest;

class SecurityControllerTest extends BaseTest
{
    public function testApiLogin()
    {
        $roles = ['ROLE_SUPER_ADMIN'];
        $client = 1;
        $response = $this->client->request('POST', self::URI_LOGIN, ['form_params' => ['username' => self::SUPER_ADMIN, 'password' => self::SUPER_ADMIN]]);
        $body = json_decode($response->getBody(), true);
        $decodedToken = $this->getService('lexik_jwt_authentication.encoder')->decode($body['token']);

        $this->assertEquals(array(
            'message',
            'token'
        ), array_keys((array) $body));

        $this->assertEquals($roles, $decodedToken['roles']);
        $this->assertEquals($client, $decodedToken['client']);
        $this->assertEquals(self::SUPER_ADMIN, $decodedToken['username']);
        $this->assertEquals(time() + 3600, $decodedToken['exp']);
    }

    public function testTokenAuthorizationHeader()
    {
        $responseLogin = $this->client->request('POST', self::URI_LOGIN, ['form_params' => ['username' => self::USER, 'password' => self::USER]]);
        $bodyLogin = json_decode($responseLogin->getBody(), true);
        $token = $bodyLogin['token'];
        $responseGet = $this->client->request('GET', self::URI_PHONE . '/1', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);
        $decodedToken = $this->getService('lexik_jwt_authentication.encoder')->decode($token);
        $this->assertEquals(200, $responseGet->getStatusCode());
        $this->assertEquals(['ROLE_USER'], $decodedToken['roles']);
    }
}
