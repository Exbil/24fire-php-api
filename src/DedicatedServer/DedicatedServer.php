<?php

namespace FireAPI\DedicatedServer;

use FireAPI\Client;
use GuzzleHttp\Exception\GuzzleException;

class DedicatedServer
{
    private Client $client;

    /**
     * Constructor method to initialize the class with a Client instance.
     *
     * @param Client $client An instance of the Client class.
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return mixed
     * @throws GuzzleException
     */
    public function getAvailable(): mixed
    {
        return $this->client->get('dedicated/available');
    }

    /**
     * @param string $identifier
     * @return mixed
     * @throws GuzzleException
     */
    public function checkAvailable(string $identifier): mixed
    {
        return $this->client->get("/dedicated/available/{$identifier}");
    }

    /**
     * @param string $identifier
     * @param string|null $webhook
     * @param string|null $connect
     * @return mixed
     * @throws GuzzleException
     */
    public function create(string $identifier, ?string $webhook = null, ?string $connect = null): mixed
    {
        return $this->client->put("/dedicated/{$identifier}/purchase", ['webhook' => $webhook, 'connect' => $connect]);
    }

    /**
     * @param string $identifier
     * @return mixed
     * @throws GuzzleException
     */
    public function delete(string $identifier): mixed
    {
        return $this->client->delete("/dedicated/{$identifier}/delete");
    }

    /**
     * @param string $identifier
     * @return mixed
     * @throws GuzzleException
     */
    public function undelete(string $identifier): mixed
    {
        return $this->client->post("/dedicated/{$identifier}/undelete");
    }

    /**
     * @param string $identifier
     * @return mixed
     * @throws GuzzleException
     */
    public function getInfo(string $identifier): mixed
    {
        return $this->client->get("/dedicated/{$identifier}/info");
    }

    /**
     * @return mixed
     * @throws GuzzleException
     */
    public function getList(): mixed
    {
        return $this->client->get('/dedicated/list');
    }
}