<?php

namespace Tests\Unit\Domain;

use FireAPI\Domain\Domain;
use FireAPI\Client;
use FireAPI\Exceptions\ParameterException;
use FireAPI\Exceptions\DomainException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class DomainTest extends TestCase
{
    private Domain $domain;
    private MockHandler $mockHandler;
    private string $testToken = 'test-token-123';

    protected function setUp(): void
    {
        parent::setUp();

        // Setup mock handler for HTTP responses
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);

        // Create GuzzleHttp client with mock handler
        $guzzleClient = new GuzzleClient(['handler' => $handlerStack]);

        // Initialize FireAPI client with mocked GuzzleHttp client
        $client = new Client($this->testToken, false, $guzzleClient);

        // Create Domain instance
        $this->domain = new Domain($client);
    }

    public function testGetDomains(): void
    {
        $expectedResponse = [
            'domains' => [
                [
                    'id' => 1,
                    'name' => 'example.com',
                    'status' => 'active',
                    'expiry_date' => '2025-01-01',
                    'auto_renew' => true
                ],
                [
                    'id' => 2,
                    'name' => 'test.com',
                    'status' => 'pending',
                    'expiry_date' => '2025-02-01',
                    'auto_renew' => false
                ]
            ]
        ];

        $this->mockHandler->append(
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($expectedResponse)
            )
        );

        $result = $this->domain->getDomains();

        $this->assertEquals($expectedResponse, $result);
    }

    public function testGetDomainDetails(): void
    {
        $domainName = 'example.com';
        $expectedResponse = [
            'id' => 1,
            'name' => 'example.com',
            'status' => 'active',
            'expiry_date' => '2025-01-01',
            'registrant' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.com'
            ],
            'nameservers' => [
                'ns1.example.com',
                'ns2.example.com'
            ],
            'dns_records' => [
                [
                    'type' => 'A',
                    'name' => '@',
                    'content' => '192.168.1.1'
                ]
            ]
        ];

        $this->mockHandler->append(
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($expectedResponse)
            )
        );

        $result = $this->domain->getDomain($domainName);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCheckDomainAvailability(): void
    {
        $domainName = 'available-domain.com';
        $expectedResponse = [
            'domain' => 'available-domain.com',
            'available' => true,
            'price' => [
                'registration' => 10.99,
                'renewal' => 12.99
            ]
        ];

        $this->mockHandler->append(
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($expectedResponse)
            )
        );

        $result = $this->domain->checkAvailability($domainName);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testRegisterDomain(): void
    {
        $domainData = [
            'name' => 'newdomain.com',
            'period' => 1,
            'registrant' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.com',
                'phone' => '+1.1234567890',
                'address' => '123 Main St',
                'city' => 'Anytown',
                'country' => 'US'
            ],
            'nameservers' => [
                'ns1.example.com',
                'ns2.example.com'
            ]
        ];

        $expectedResponse = [
            'domain' => 'newdomain.com',
            'status' => 'pending',
            'order_id' => '123456',
            'expiry_date' => '2025-03-01'
        ];

        $this->mockHandler->append(
            new Response(
                201,
                ['Content-Type' => 'application/json'],
                json_encode($expectedResponse)
            )
        );

        $result = $this->domain->registerDomain($domainData);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUpdateNameservers(): void
    {
        $domainName = 'example.com';
        $nameservers = [
            'ns1.newprovider.com',
            'ns2.newprovider.com'
        ];

        $expectedResponse = [
            'status' => 'success',
            'nameservers' => $nameservers
        ];

        $this->mockHandler->append(
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($expectedResponse)
            )
        );

        $result = $this->domain->updateNameservers($domainName, $nameservers);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUpdateDNSRecords(): void
    {
        $domainName = 'example.com';
        $records = [
            [
                'type' => 'A',
                'name' => '@',
                'content' => '192.168.1.1',
                'ttl' => 3600
            ],
            [
                'type' => 'MX',
                'name' => '@',
                'content' => 'mail.example.com',
                'priority' => 10,
                'ttl' => 3600
            ]
        ];

        $expectedResponse = [
            'status' => 'success',
            'records' => $records
        ];

        $this->mockHandler->append(
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($expectedResponse)
            )
        );

        $result = $this->domain->updateDNSRecords($domainName, $records);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testEnableAutoRenew(): void
    {
        $domainName = 'example.com';
        $expectedResponse = [
            'status' => 'success',
            'auto_renew' => true
        ];

        $this->mockHandler->append(
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($expectedResponse)
            )
        );

        $result = $this->domain->enableAutoRenew($domainName);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testInvalidDomainNameThrowsException(): void
    {
        $invalidDomain = 'not-a-valid-domain';

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Invalid domain name format');

        $this->domain->checkAvailability($invalidDomain);
    }

    public function testMissingRegistrantDataThrowsException(): void
    {
        $incompleteData = [
            'name' => 'newdomain.com',
            'period' => 1
            // Missing registrant data
        ];

        $this->expectException(ParameterException::class);
        $this->expectExceptionMessage('Missing required registrant information');

        $this->domain->registerDomain($incompleteData);
    }

    public function testTransferDomain(): void
    {
        $transferData = [
            'domain' => 'transfer-domain.com',
            'auth_code' => 'ABC123XYZ',
            'registrant' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.com'
            ]
        ];

        $expectedResponse = [
            'status' => 'pending_transfer',
            'transfer_id' => 'TRF123456',
            'estimated_completion' => '2024-04-01'
        ];

        $this->mockHandler->append(
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($expectedResponse)
            )
        );

        $result = $this->domain->transferDomain($transferData);

        $this->assertEquals($expectedResponse, $result);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->mockHandler = null;
        $this->domain = null;
    }
}