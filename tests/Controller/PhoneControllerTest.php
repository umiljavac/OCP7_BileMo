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

    public function testListAction()
    {
        $response = $this->client->request('GET', self::GET_URI . '/all');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
        $this->assertEquals(['/phones/all'], $response->getHeader('Location'));
        $this->assertInternalType('array', json_decode((string) $response->getBody()));
        $this->assertCount(120, json_decode((string) $response->getBody()));
    }

    public function testListWithCriteriaAction()
    {
        $response = $this->client->request('GET', self::GET_URI . '?keyword=Sung&offset=20');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
        $body = json_decode($response->getBody(), true);
        $this->assertInternalType('array', $body);
        $this->assertEquals(20, count($body['data']));
        for ($i = 0; $i < count($body['data']); $i++) {
            $this->assertEquals('Sungsong', $body['data'][$i]['mark']);
            if ($i < count($body['data']) - 1) {
                $this->assertTrue($body['data'][$i]['price'] <= $body['data'][$i + 1]['price']);
             }
        }
        $this->assertEquals(1, $this->count($body['meta']));
        $this->assertEquals(60, $body['meta']['total_items']);
        $this->assertEquals(20, $body['meta']['current_items']);
    }
}
