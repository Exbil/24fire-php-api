<?php

namespace Tests\Unit\Accounting;

use FireAPI\Accounting\Accounting;
use FireAPI\Client;
use FireAPI\Exceptions\ParameterException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class AccountingTest extends TestCase
{
    private Accounting $accounting;
    private MockHandler $mockHandler;
    private string $testToken = 'test-token-123';

    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock handler for HTTP responses
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);

        // Create a GuzzleHttp client with the mock handler
        $guzzleClient = new GuzzleClient(['handler' => $handlerStack]);

        // Initialize the FireAPI client with the mocked GuzzleHttp client
        $client = new Client($this->testToken, false, $guzzleClient);

        // Create the Accounting instance with the mocked client
        $this->accounting = new Accounting($client);
    }

    public function testGetInvoices(): void
    {
        // Mock response for getting invoices
        $expectedResponse = [
            'invoices' => [
                [
                    'id' => 1,
                    'number' => 'INV-2024-001',
                    'amount' => 100.00,
                    'status' => 'paid'
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

        $result = $this->accounting->getInvoices();

        $this->assertEquals($expectedResponse, $result);
    }

    public function testGetInvoiceDetails(): void
    {
        // Mock response for getting a specific invoice
        $invoiceId = 1;
        $expectedResponse = [
            'id' => $invoiceId,
            'number' => 'INV-2024-001',
            'amount' => 100.00,
            'status' => 'paid',
            'items' => [
                [
                    'description' => 'Service A',
                    'quantity' => 1,
                    'price' => 100.00
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

        $result = $this->accounting->getInvoice($invoiceId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreateInvoice(): void
    {
        // Test data for creating an invoice
        $invoiceData = [
            'customer_id' => 123,
            'items' => [
                [
                    'description' => 'New Service',
                    'quantity' => 1,
                    'price' => 150.00
                ]
            ]
        ];

        // Mock response for invoice creation
        $expectedResponse = [
            'id' => 2,
            'status' => 'created',
            'number' => 'INV-2024-002'
        ];

        $this->mockHandler->append(
            new Response(
                201,
                ['Content-Type' => 'application/json'],
                json_encode($expectedResponse)
            )
        );

        $result = $this->accounting->createInvoice($invoiceData);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUpdateInvoice(): void
    {
        // Test data for updating an invoice
        $invoiceId = 1;
        $updateData = [
            'status' => 'cancelled',
            'notes' => 'Customer requested cancellation'
        ];

        // Mock response for invoice update
        $expectedResponse = [
            'id' => $invoiceId,
            'status' => 'cancelled',
            'notes' => 'Customer requested cancellation'
        ];

        $this->mockHandler->append(
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($expectedResponse)
            )
        );

        $result = $this->accounting->updateInvoice($invoiceId, $updateData);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDeleteInvoice(): void
    {
        $invoiceId = 1;

        // Mock response for invoice deletion
        $this->mockHandler->append(
            new Response(
                204,
                ['Content-Type' => 'application/json']
            )
        );

        $result = $this->accounting->deleteInvoice($invoiceId);

        $this->assertTrue($result);
    }

    public function testInvalidInvoiceDataThrowsException(): void
    {
        // Test invalid invoice data handling
        $invalidData = 'not an array';

        $this->expectException(ParameterException::class);
        $this->expectExceptionMessage('Invoice data must be an array');

        $this->accounting->createInvoice($invalidData);
    }

    public function testGetInvoiceFilters(): void
    {
        // Test getting invoices with filters
        $filters = [
            'status' => 'paid',
            'date_from' => '2024-01-01',
            'date_to' => '2024-12-31'
        ];

        $expectedResponse = [
            'invoices' => [
                [
                    'id' => 1,
                    'status' => 'paid',
                    'date' => '2024-02-15'
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

        $result = $this->accounting->getInvoices($filters);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testGetPayments(): void
    {
        // Mock response for getting payments
        $expectedResponse = [
            'payments' => [
                [
                    'id' => 1,
                    'invoice_id' => 1,
                    'amount' => 100.00,
                    'date' => '2024-03-01'
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

        $result = $this->accounting->getPayments();

        $this->assertEquals($expectedResponse, $result);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->mockHandler = null;
        $this->accounting = null;
    }
}