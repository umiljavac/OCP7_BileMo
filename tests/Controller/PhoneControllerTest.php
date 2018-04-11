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
        $response = $this->client->request('GET', self::URI_PHONE, [
            'headers' => $this->generateAuthHeaders(self::USER)
        ]);
        $body = json_decode($response->getBody(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
        $this->assertEquals(['/api/phones'], $response->getHeader('Location'));
        $this->assertInternalType('array', $body['_embedded']['phones']);
        $this->assertEquals(6, count($body));
    }

    public function testListPaginationAction()
    {
        $response = $this->client->request('GET', self::URI_PHONE . '?page=2&limit=5', [
            'headers' => $this->generateAuthHeaders(self::ADMIN)
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
        $body = json_decode($response->getBody(), true);
        $this->assertEquals(2, $body['page']);
        $this->assertEquals(8, $body['pages']);
        $this->assertEquals(5, $body['limit']);
        $this->assertEquals(40, $body['total']);
        $this->assertEquals(5, count($body['_links']));
        $this->assertEquals(1, count($body['_embedded']));
        $this->assertEquals(5, count($body['_embedded']['phones']));

        for ($i = 0; $i < count($body['_embedded']['phones']); $i++) {
            if ($i < count($body['_embedded']['phones']) - 1) {
                $this->assertTrue($body['_embedded']['phones'][$i]['price'] <= $body['_embedded']['phones'][$i + 1]['price']);
            }
        }
    }

    public function testMarksAction()
    {
        $response = $this->client->request('GET', self::URI_PHONE . '/marks/Sungsong', [
            'headers' => $this->generateAuthHeaders(self::ADMIN)
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['application/hal+json'], $response->getHeader('Content-Type'));
        $body = json_decode($response->getBody(), true);
        $this->assertInternalType('array', $body);
        $this->assertEquals(10, count($body));
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
