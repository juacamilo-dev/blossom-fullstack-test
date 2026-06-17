<?php

use PHPUnit\Framework\TestCase;

class TransactionApiTest extends TestCase
{
    private $baseUrl = 'http://blossom_nginx:80';

    private function makeRequest(string $method, string $path, array $body = []): array
    {
        $ch = curl_init();
        $url = $this->baseUrl . $path;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'code' => $httpCode,
            'body' => json_decode($response, true)
        ];
    }

    public function testGetTransactionsReturnsSuccess(): void
    {
        $result = $this->makeRequest('GET', '/transactions');

        $this->assertEquals(200, $result['code']);
        $this->assertArrayHasKey('data', $result['body']);
        $this->assertArrayHasKey('pagination', $result['body']);
    }

    public function testGetTransactionsReturnsPagination(): void
    {
        $result = $this->makeRequest('GET', '/transactions?page=1&limit=5');

        $this->assertEquals(200, $result['code']);
        $this->assertArrayHasKey('total', $result['body']['pagination']);
        $this->assertArrayHasKey('total_pages', $result['body']['pagination']);
        $this->assertLessThanOrEqual(5, count($result['body']['data']));
    }

    public function testGetTransactionsFilterByType(): void
    {
        $result = $this->makeRequest('GET', '/transactions?type=credit');

        $this->assertEquals(200, $result['code']);
        foreach ($result['body']['data'] as $transaction) {
            $this->assertEquals('credit', $transaction['type']);
        }
    }

    public function testCreateTransactionSuccess(): int
    {
        $result = $this->makeRequest('POST', '/transactions', [
            'accountNumberFrom'     => '1234567890',
            'accountNumberTypeFrom' => 'savings',
            'accountNumberTo'       => '0987654321',
            'accountNumberTypeTo'   => 'checking',
            'amount'                => '500.00',
            'type'                  => 'credit',
            'description'           => 'PHPUnit test transaction',
            'reference'             => 'TEST001'
        ]);

        $this->assertEquals(201, $result['code']);
        $this->assertArrayHasKey('data', $result['body']);
        $this->assertArrayHasKey('traceNumber', $result['body']['data']);
        $this->assertEquals('credit', $result['body']['data']['type']);
        $this->assertEquals('500.00', $result['body']['data']['amount']);

        return $result['body']['data']['transactionID'];
    }

    public function testCreateTransactionMissingFields(): void
    {
        $result = $this->makeRequest('POST', '/transactions', [
            'accountNumberFrom' => '1234567890',
        ]);

        $this->assertEquals(400, $result['code']);
        $this->assertArrayHasKey('error', $result['body']);
    }

    public function testCreateTransactionInvalidType(): void
    {
        $result = $this->makeRequest('POST', '/transactions', [
            'accountNumberFrom'     => '1234567890',
            'accountNumberTypeFrom' => 'savings',
            'accountNumberTo'       => '0987654321',
            'accountNumberTypeTo'   => 'checking',
            'amount'                => '100.00',
            'type'                  => 'invalid_type',
        ]);

        $this->assertEquals(400, $result['code']);
        $this->assertArrayHasKey('error', $result['body']);
    }

    public function testDeleteTransactionNotFound(): void
    {
        $result = $this->makeRequest('DELETE', '/transactions/999999999');

        $this->assertEquals(404, $result['code']);
        $this->assertArrayHasKey('error', $result['body']);
    }

    public function testDeleteTransactionSuccess(): void
    {
        // Primero crea una transaccion para luego borrarla
        $create = $this->makeRequest('POST', '/transactions', [
            'accountNumberFrom'     => '1111111111',
            'accountNumberTypeFrom' => 'savings',
            'accountNumberTo'       => '2222222222',
            'accountNumberTypeTo'   => 'checking',
            'amount'                => '100.00',
            'type'                  => 'debit',
            'description'           => 'Transaction to delete',
        ]);

        $this->assertEquals(201, $create['code']);
        $id = $create['body']['data']['transactionID'];

        // Ahora la borra
        $delete = $this->makeRequest('DELETE', '/transactions/' . $id);

        $this->assertEquals(200, $delete['code']);
        $this->assertArrayHasKey('message', $delete['body']);
    }

    public function testRouteNotFound(): void
    {
        $result = $this->makeRequest('GET', '/nonexistent');

        $this->assertEquals(404, $result['code']);
    }
}