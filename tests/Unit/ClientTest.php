<?php

namespace Tests\Unit;

use FireAPI\Client;
use FireAPI\Credentials;
use FireAPI\Exceptions\ParameterException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private string $testToken = 'test-token-123';
    private Client $client;
    private MockHandler $mockHandler;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock for GuzzleHttp responses
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);

        // Create a GuzzleHttp client with the mock handler
        $guzzleClient = new GuzzleClient(['handler' => $handlerStack]);

        // Initialize the FireAPI client with the mocked GuzzleHttp client
        $this->client = new Client($this->testToken, false, $guzzleClient);
    }

    public function testClientConstructorInitializesCorrectly(): void
    {
        $client = new Client($this->testToken);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(GuzzleClient::class, $client->getHttpClient());
    }

    public function testHttpClientHasCorrectDefaultHeaders(): void
    {
        $client = new Client($this->testToken);
        $httpClient = $client->getHttpClient();

        $config = $httpClient->getConfig();

        $this->assertArrayHasKey('headers', $config);
        $this->assertEquals('application/json', $config['headers']['Accept']);
        $this->assertEquals('application/json', $config['headers']['Content-Type']);
        $this->assertEquals('Bearer ' . $this->testToken, $config['headers']['Authorization']);
    }

    public function testSuccessfulPostRequest(): void
    {
        // Prepare mock response
        $expectedResponse = ['status' => 'success'];
        $this->mockHandler->append(
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($expectedResponse)
            )
        );

        $result = $this->client->post('test/endpoint', ['param' => 'value']);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testSuccessfulGetRequest(): void
    {
        // Prepare mock response
        $expectedResponse = ['data' => 'test'];
        $this->mockHandler->append(
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($expectedResponse)
            )
        );

        $result = $this->client->get('test/endpoint');

        $this->assertEquals($expectedResponse, $result);
    }

    public function testInvalidJsonThrowsException(): void
    {
        $this->mockHandler->append(
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                'invalid json'
            )
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Error decoding JSON');

        $this->client->get('test/endpoint');
    }

    public function testNonArrayParamsThrowsException(): void
    {
        $this->expectException(ParameterException::class);
        $this->expectExceptionMessage('Parameters must be an array');

        $invalidParams = 'not an array';
        $reflection = new \ReflectionClass($this->client);
        $method = $reflection->getMethod('request');
        $method->setAccessible(true);

        $method->invoke($this->client, 'test/endpoint', $invalidParams);
    }

    public function testUnsupportedMethodThrowsException(): void
    {
        $this->expectException(ParameterException::class);
        $this->expectExceptionMessage('Unsupported HTTP method');

        $reflection = new \ReflectionClass($this->client);
        $method = $reflection->getMethod('request');
        $method->setAccessible(true);

        $method->invoke($this->client, 'test/endpoint', [], 'INVALID');
    }

    public function testSandboxModeUsesCorrectUrl(): void
    {
        $sandboxClient = new Client($this->testToken, true);
        $reflection = new \ReflectionClass($sandboxClient);

        $credentialsProperty = $reflection->getProperty('credentials');
        $credentialsProperty->setAccessible(true);

        $credentials = $credentialsProperty->getValue($sandboxClient);

        $this->assertInstanceOf(Credentials::class, $credentials);
        $this->assertStringContainsString('sandbox', $credentials->baseUrl);
    }

    public function testLiveModeUsesCorrectUrl(): void
    {
        $liveClient = new Client($this->testToken, false);
        $reflection = new \ReflectionClass($liveClient);

        $credentialsProperty = $reflection->getProperty('credentials');
        $credentialsProperty->setAccessible(true);

        $credentials = $credentialsProperty->getValue($liveClient);

        $this->assertInstanceOf(Credentials::class, $credentials);
        $this->assertStringContainsString('live', $credentials->baseUrl);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->mockHandler = null;
        $this->client = null;
    }
}