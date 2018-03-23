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

    protected function tearDown()
    {
        // purposefully not calling parent class, which shuts down the kernel
    }
}
