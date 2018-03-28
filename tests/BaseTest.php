<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 23/03/2018
 * Time: 09:15
 */

namespace App\Tests;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BaseTest extends KernelTestCase
{
    private static $staticClient;

    protected $client;

    const POST_LOGIN = '/api/login';
    const GET_URI = '/phones';
    const USER = 'alphauser1';
    const ADMIN = 'alphaleader';
    const SUPER_ADMIN = 'boss';

    public static function setUpBeforeClass()
    {
        self::$staticClient = new Client([
            'base_uri' => 'http://localhost:8000',
            'defaults' => [
                'exceptions' => false
            ]
        ]);

        self::bootKernel();
    }

    protected function setUp()
    {
        $this->client = self::$staticClient;

        // $this->purgeDatabase();
    }

    /**
     * @return object entityManager
     */
    protected function getEntityManager()
    {
        return $this->getService('doctrine.orm.entity_manager');
    }

    protected function getService($id)
    {
        return self::$kernel->getContainer()
            ->get($id);
    }

    protected function getAuthorizedHeaders($username, $headers = array())
    {
        $token = $this->getService('lexik_jwt_authentication.encoder')
            ->encode([
                'username' => $username,
                'exp' => time() + 3600
            ]);

        $headers['Authorization'] = 'Bearer ' . $token;

        return $headers;
    }

    protected function tearDown()
    {
        // purposefully not calling parent class, which shuts down the kernel
    }

    protected function generateAuthHeaders($userType)
    {
        $responseLogin = $this->client->request('POST', self::POST_LOGIN, ['form_params' => ['username' => $userType, 'password' => $userType ]]);
        $bodyLogin = json_decode($responseLogin->getBody(), true);
        $token = $bodyLogin['token'];
        $headers = ['Authorization' => 'Bearer ' . $token];
        return $headers;
    }

}
