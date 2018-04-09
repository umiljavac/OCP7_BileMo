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

    public function testShowAction()
    {
        $response = $this->client->request('GET', self::URI_PHONE . '/1', [
            'headers' => $this->generateAuthHeaders(self::USER)
        ]);
        $body = json_decode($response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
        $this->assertEquals(array(
            'id',
            'mark',
            'reference',
            'description',
            'price',
            '_links'
        ), array_keys((array) $body));
    }

    public function testListAction()
    {
        $response = $this->client->request('GET', self::URI_PHONE . '/all', [
            'headers' => $this->generateAuthHeaders(self::USER)
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
        $this->assertEquals(['/api/phones/all'], $response->getHeader('Location'));
        $this->assertInternalType('array', json_decode((string) $response->getBody()));
        $this->assertCount(40, json_decode((string) $response->getBody()));
    }

    public function testListWithCriteriaAction()
    {
        $response = $this->client->request('GET', self::URI_PHONE . '?keyword=Sung&offset=10', [
            'headers' => $this->generateAuthHeaders(self::ADMIN)
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
        $body = json_decode($response->getBody(), true);
        $this->assertInternalType('array', $body);
        $this->assertEquals(10, count($body['data']));
        for ($i = 0; $i < count($body['data']); $i++) {
            $this->assertEquals('Sungsong', $body['data'][$i]['mark']);
            if ($i < count($body['data']) - 1) {
                $this->assertTrue($body['data'][$i]['price'] <= $body['data'][$i + 1]['price']);
             }
        }
        $this->assertEquals(1, $this->count($body['meta']));
        $this->assertEquals(10, $body['meta']['total_items']);
        $this->assertEquals(10, $body['meta']['current_items']);
    }

    public function testAddAction()
    {
        $response = $this->client->request('POST', self::URI_PHONE, [
            'headers' => $this->generateAuthHeaders(self::SUPER_ADMIN),
            'form_params' => [
                'mark' => 'Wikool',
                'reference' => 'wik-1',
                'description' => 'yeah !',
                'price' => 123
            ]
        ]);
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testPatchUpdate()
    {
        $response = $this->client->request('PATCH', self::URI_PHONE . '/41', [
            'headers' => $this->generateAuthHeaders(self::SUPER_ADMIN),
            'form_params' => [
                'price' => 145
            ]
        ]);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testPutUpdate()
    {
        $response = $this->client->request('PUT', self::URI_PHONE . '/41', [
            'headers' => $this->generateAuthHeaders(self::SUPER_ADMIN),
            'form_params' => [
                'mark' => 'Wikoolo',
                'reference' => 'wik-1A',
                'description' => 'yeah awesome !',
                'price' => 156
            ]
        ]);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDelete()
    {
        $response = $this->client->request('DELETE', self::URI_PHONE . '/41', [
            'headers' => $this->generateAuthHeaders(self::SUPER_ADMIN)
        ]);
        $this->assertEquals(204, $response->getStatusCode());
    }
}
