<?php

namespace FireAPI\IP;

use FireAPI\Client;
use GuzzleHttp\Exception\GuzzleException;

class IP
{
    private Client $client;

    /**
     * Constructor method for initializing dependencies.
     *
     * @param Client $client An instance of the Client class.
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Retrieves a list of available IP addresses from the client.
     *
     * @return mixed The list of available IP addresses.
     * @throws GuzzleException
     */
    public function getAvailable(): mixed
    {
        return $this->client->get('ip/available');
    }

    /**
     * Initiates the purchase of an IP address for the specified network ID.
     *
     * @param int $netID The ID of the network for which the IP address is to be purchased.
     * @return mixed The result of the purchase operation.
     * @throws GuzzleException
     */
    public function create(int $netID): mixed
    {
        return $this->client->post('ip/purchase', ['netID' => $netID]);
    }

    /**
     * Fetches the list of IP addresses from the client.
     *
     * @return mixed The list of IP addresses.
     * @throws GuzzleException
     */
    public function getList(): mixed
    {
        return $this->client->get('ip/list');
    }

    /**
     * Deletes a network resource using the provided network ID.
     *
     * @param int $netID The unique identifier of the network to be deleted.
     * @return mixed The response from the client after performing the deletion.
     * @throws GuzzleException
     */
    public function delete(int $netID): mixed
    {
        return $this->client->delete('ip/delete', ['netID' => $netID]);
    }
}