<?php

namespace FireAPI\Accounting;

use FireAPI\Client;
use GuzzleHttp\Exception\GuzzleException;

class Accounting
{
    private Client $client;

    /**
     * Constructor method for initializing the class with a Client instance.
     *
     * @param Client $client The client instance to be used by the class.
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Retrieves the account information by making a POST request to the 'account' endpoint.
     *
     * @return mixed The response from the
     * @throws GuzzleException
     */
    public function getAccount(): mixed
    {
        return $this->client->post('account/requests');
    }

    /**
     * Retrieves the OTP code by making a POST request to the 'account/otp' endpoint.
     *
     * @return mixed The response from the OTP endpoint.
     * @throws GuzzleException
     */
    public function otpCode(): mixed
    {
        return $this->client->post('account/otp');
    }

    /**
     * Fetches invoice details by sending a GET request to the 'accounting/invoice' endpoint.
     *
     * @return mixed The response containing the invoice details.
     * @throws GuzzleException
     */
    public function getInvoice(): mixed
    {
        return $this->client->get('accounting/invoices');
    }

    /**
     * Retrieves the details of a specific invoices by its ID.
     *
     * @param int $id The unique identifier of the invoice.
     * @return mixed The details of the specified invoice.
     * @throws GuzzleException
     */
    public function getInvoiceDetails(int $id): mixed
    {
        return $this->client->get('accounting/invoices/' . $id);
    }

    /**
     * Retrieves the current state of the invoice.
     *
     * @return mixed The current state of the invoice.
     * @throws GuzzleException
     */
    public function getInvoiceState(): mixed
    {
        return $this->client->get('accounting/invoices/current');
    }

    /**
     * Retrieves pricing information.
     *
     * @return mixed The pricing details.
     * @throws GuzzleException
     */
    public function getPricing(): mixed
    {
        return $this->client->get('accounting/pricings');
    }

    /**
     * Retrieves the list of available discount offers.
     *
     * @return mixed The details of the discount offers.
     * @throws GuzzleException
     */
    public function getDiscountOffers(): mixed
    {
        return $this->client->get('accounting/sales');
    }
}