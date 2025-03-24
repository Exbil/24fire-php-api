<?php

namespace Tests\Unit\RootServer;

use FireAPI\RootServer\RootServer;
use FireAPI\Client;
use FireAPI\Exceptions\ParameterException;
use FireAPI\Exceptions\ServerException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class RootServerTest extends TestCase
{
    private RootServer $rootServer;
    private MockHandler $mockHandler;
    private string $testToken = 'test-token-123';

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $guzzleClient = new GuzzleClient(['handler' => $handlerStack]);
        $client = new Client($this->testToken, false, $guzzleClient);
        $this->rootServer = new RootServer($client);
    }

    public function testGetServers(): void
    {
        $expectedResponse = [
            'servers' => [
                [
                    'id' => 'srv-123',
                    'name' => 'webserver-01',
                    'status' => 'running',
                    'ip' => '192.168.1.100',
                    'datacenter' => 'fra1',
                    'plan' => 'vps-2',
                    'os' => 'ubuntu-22.04'
                ],
                [
                    'id' => 'srv-124',
                    'name' => 'database-01',
                    'status' => 'running',
                    'ip' => '192.168.1.101',
                    'datacenter' => 'fra1',
                    'plan' => 'vps-4',
                    'os' => 'debian-11'
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

        $result = $this->rootServer->getServers();

        $this->assertEquals($expectedResponse, $result);
    }

    public function testGetServerDetails(): void
    {
        $serverId = 'srv-123';
        $expectedResponse = [
            'id' => $serverId,
            'name' => 'webserver-01',
            'status' => 'running',
            'ip' => '192.168.1.100',
            'datacenter' => 'fra1',
            'plan' => 'vps-2',
            'os' => 'ubuntu-22.04',
            'cpu_cores' => 2,
            'memory' => 4096,
            'disk' => 80,
            'bandwidth' => 20,
            'traffic_usage' => 15.5,
            'created_at' => '2024-01-15T10:00:00Z',
            'backups' => [
                'enabled' => true,
                'schedule' => 'daily',
                'retention' => 7
            ]
        ];

        $this->mockHandler->append(
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($expectedResponse)
            )
        );

        $result = $this->rootServer->getServer($serverId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreateServer(): void
    {
        $serverData = [
            'name' => 'new-server',
            'datacenter' => 'fra1',
            'plan' => 'vps-2',
            'os' => 'ubuntu-22.04',
            'ssh_keys' => ['ssh-key-1'],
            'backups' => true
        ];

        $expectedResponse = [
            'id' => 'srv-125',
            'name' => 'new-server',
            'status' => 'provisioning',
            'ip' => null,
            'root_password' => 'temporary-password-123'
        ];

        $this->mockHandler->append(
            new Response(
                201,
                ['Content-Type' => 'application/json'],
                json_encode($expectedResponse)
            )
        );

        $result = $this->rootServer->createServer($serverData);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDeleteServer(): void
    {
        $serverId = 'srv-123';

        $this->mockHandler->append(
            new Response(204)
        );

        $result = $this->rootServer->deleteServer($serverId);

        $this->assertTrue($result);
    }

    public function testRebootServer(): void
    {
        $serverId = 'srv-123';
        $expectedResponse = [
            'status' => 'rebooting',
            'estimated_completion' => '2024-03-15T10:05:00Z'
        ];

        $this->mockHandler->append(
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($expectedResponse)
            )
        );

        $result = $this->rootServer->rebootServer($serverId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testShutdownServer(): void
    {
        $serverId = 'srv-123';
        $expectedResponse = [
            'status' => 'shutting_down',
            'estimated_completion' => '2024-03-15T10:02:00Z'
        ];

        $this->mockHandler->append(
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($expectedResponse)
            )
        );

        $result = $this->rootServer->shutdownServer($serverId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testStartServer(): void
    {
        $serverId = 'srv-123';
        $expectedResponse = [
            'status' => 'starting',
            'estimated_completion' => '2024-03-15T10:01:00Z'
        ];

        $this->mockHandler->append(
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($expectedResponse)
            )
        );

        $result = $this->rootServer->startServer($serverId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testGetServerMetrics(): void
    {
        $serverId = 'srv-123';
        $expectedResponse = [
            'cpu' => [
                'usage' => 45.5,
                'cores' => 2
            ],
            'memory' => [
                'total' => 4096,
                'used' => 2048,
                'free' => 2048
            ],
            'disk' => [
                'total' => 80,
                'used' => 35,
                'free' => 45
            ],
            'network' => [
                'in_traffic' => 150.5,
                'out_traffic' => 75.2
            ],
            'timestamp' => '2024-03-15T10:00:00Z'
        ];

        $this->mockHandler->append(
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($expectedResponse)
            )
        );

        $result = $this->rootServer->getServerMetrics($serverId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreateBackup(): void
    {
        $serverId = 'srv-123';
        $backupData = [
            'name' => 'pre-update-backup',
            'description' => 'Backup before system update'
        ];

        $expectedResponse = [
            'backup_id' => 'bkp-123',
            'status' => 'creating',
            'estimated_completion' => '2024-03-15T10:30:00Z'
        ];

        $this->mockHandler->append(
            new Response(
                201,
                ['Content-Type' => 'application/json'],
                json_encode($expectedResponse)
            )
        );

        $result = $this->rootServer->createBackup($serverId, $backupData);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testInvalidServerIdThrowsException(): void
    {
        $invalidServerId = '';

        $this->expectException(ParameterException::class);
        $this->expectExceptionMessage('Invalid server ID');

        $this->rootServer->getServer($invalidServerId);
    }

    public function testServerNotFoundThrowsException(): void
    {
        $nonExistentServerId = 'srv-999';

        $this->mockHandler->append(
            new Response(
                404,
                ['Content-Type' => 'application/json'],
                json_encode(['error' => 'Server not found'])
            )
        );

        $this->expectException(ServerException::class);
        $this->expectExceptionMessage('Server not found');

        $this->rootServer->getServer($nonExistentServerId);
    }

    public function testResizeServer(): void
    {
        $serverId = 'srv-123';
        $resizeData = [
            'plan' => 'vps-4',
            'disk' => 160
        ];

        $expectedResponse = [
            'status' => 'resizing',
            'estimated_completion' => '2024-03-15T11:00:00Z',
            'new_plan' => 'vps-4',
            'new_disk_size' => 160
        ];

        $this->mockHandler->append(
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($expectedResponse)
            )
        );

        $result = $this->rootServer->resizeServer($serverId, $resizeData);

        $this->assertEquals($expectedResponse, $result);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->mockHandler = null;
        $this->rootServer = null;
    }
}