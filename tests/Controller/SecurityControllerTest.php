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
    const POST_LOGIN = '/api/login';
    const GET_URI = '/phones';

    public function testApiLogin()
    {
        $username = 'boss';
        $roles = ['ROLE_SUPER_ADMIN'];
        $client = 'bilemo';
        $response = $this->client->request('POST', self::POST_LOGIN, ['form_params' => ['username' => 'boss', 'password' => 'boss']]);
        $body = json_decode($response->getBody(), true);
        $decodedToken = $this->getService('lexik_jwt_authentication.encoder')->decode($body['token']);

        $this->assertEquals(array(
            'token'
        ), array_keys((array) $body));

        $this->assertEquals($roles, $decodedToken['roles']);
        $this->assertEquals($client, $decodedToken['client']);
        $this->assertEquals($username, $decodedToken['username']);
        $this->assertEquals(time() + 3600, $decodedToken['exp']);
    }

    public function testTokenAuthorizationHeader()
    {
        $responseLogin = $this->client->request('POST', self::POST_LOGIN, ['form_params' => ['username' => 'alphauser1', 'password' => 'alphauser1']]);
        $bodyLogin = json_decode($responseLogin->getBody(), true);
        $token = $bodyLogin['token'];
        $responseGet = $this->client->request('GET', self::GET_URI . '/1', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);
        $decodedToken = $this->getService('lexik_jwt_authentication.encoder')->decode($token);
        $this->assertEquals(200, $responseGet->getStatusCode());
        $this->assertEquals(['ROLE_USER'], $decodedToken['roles']);
    }
}
