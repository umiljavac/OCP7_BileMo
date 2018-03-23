<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 23/03/2018
 * Time: 09:41
 */

namespace App\Tests\Controller;

use App\Tests\BaseTest;

class PhoneControllerTest extends BaseTest
{
    CONST GET_URI = '/phones';

    public function testShowAction()
    {
        $response = $this->client->request('GET', self::GET_URI . '/1');
        $body = json_decode($response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
        $this->assertEquals(array(
            'id',
            'mark',
            'reference',
            'description',
            'price'
        ), array_keys((array) $body));
    }

    public function  testListAction()
    {
        $response = $this->client->request('GET', self::GET_URI);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
        $this->assertEquals(['/phones'], $response->getHeader('Location'));
    }
}
