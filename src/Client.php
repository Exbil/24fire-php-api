<?php

namespace FireAPI;

use FireAPI\Accounting\Accounting;
use FireAPI\Domain\Domain;
use FireAPI\Exceptions\ParameterException;
use FireAPI\RootServer\RootServer;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class Client
{
    private \GuzzleHttp\Client $httpClient;
    private Credentials $credentials;
    private string $token;

    /**
     * Client constructor.
     *
     * @param string $token
     * @param \GuzzleHttp\Client|null $httpClient
     * @param bool $sandbox
     */
    public function __construct(string $token, bool $sandbox = false, ?\GuzzleHttp\Client $httpClient = null)
    {
        $this->token = $token;
        $this->setHttpClient($httpClient);
        $this->credentials = new Credentials($token, $sandbox);
    }

    /**
     * Set HTTP client with custom options or default.
     *
     * @param \GuzzleHttp\Client|null $httpClient
     */
    public function setHttpClient(?\GuzzleHttp\Client $httpClient = null): void
    {
        $this->httpClient = $httpClient ?: new \GuzzleHttp\Client([
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
                'User-Agent' => 'YourResellingClient/1.0'
            ],
            'allow_redirects' => false,
            'timeout' => 120
        ]);
    }

    /**
     * Get the HTTP client.
     *
     * @return \GuzzleHttp\Client
     */
    public function getHttpClient(): \GuzzleHttp\Client
    {
        return $this->httpClient;
    }

    /**
     * Get the credentials for the API client.
     *
     * @return Credentials
     */
    private function getCredentials(): Credentials
    {
        return $this->credentials;
    }

    /**
     * Make an API request to a specific endpoint.
     *
     * @param string $actionPath
     * @param array $params
     * @param string $method
     * @return ResponseInterface
     * @throws GuzzleException
     */
    private function request(string $actionPath, array $params = [], string $method = 'GET'): ResponseInterface
    {
        $url = $this->getCredentials()->baseUrl . '/' . $actionPath;

        if (!is_array($params)) {
            throw new ParameterException('Parameters must be an array.');
        }

        $options = [
            'verify' => false,
            'json' => $params
        ];

        return match ($method) {
            'GET' => $this->getHttpClient()->get($url, $options),
            'POST' => $this->getHttpClient()->post($url, $options),
            'PUT' => $this->getHttpClient()->put($url, $options),
            'DELETE' => $this->getHttpClient()->delete($url, $options),
            default => throw new ParameterException('Unsupported HTTP method: ' . $method),
        };
    }

    /**
     * Process and decode the API response.
     *
     * @param ResponseInterface $response
     * @return mixed
     * @throws RuntimeException
     */
    public function processRequest(ResponseInterface $response): mixed
    {
        $responseBody = (string)$response->getBody();
        $result = json_decode($responseBody, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Error decoding JSON: ' . json_last_error_msg());
        }

        return $result ?? $responseBody;
    }

    /**
     * Send a POST request to the API.
     *
     * @param string $actionPath
     * @param array $params
     * @return mixed
     * @throws GuzzleException
     */
    public function post(string $actionPath, array $params = []): mixed
    {
        $response = $this->request($actionPath, $params, 'POST');
        return $this->processRequest($response);
    }

    /**
     * Send a GET request to the API.
     *
     * @param string $actionPath
     * @param array $params
     * @return mixed
     * @throws GuzzleException
     */
    public function get(string $actionPath, array $params = []): mixed
    {
        $response = $this->request($actionPath, $params, 'GET');
        return $this->processRequest($response);
    }

    /**
     * Send a PUT request to the API.
     *
     * @param string $actionPath
     * @param array $params
     * @return mixed
     * @throws GuzzleException
     */
    public function put(string $actionPath, array $params = []): mixed
    {
        $response = $this->request($actionPath, $params, 'PUT');
        return $this->processRequest($response);
    }

    /**
     * Send a DELETE request to the API.
     *
     * @param string $actionPath
     * @param array $params
     * @return mixed
     * @throws GuzzleException
     */
    public function delete(string $actionPath, array $params = []): mixed
    {
        $response = $this->request($actionPath, $params, 'DELETE');
        return $this->processRequest($response);
    }

    /**
     * Retrieve the accounting handler instance.
     *
     * If the accounting handler does not already exist, it initializes a new instance.
     *
     * @return Accounting
     */
    public function accounting(): Accounting
    {
        return $this->accountingHandler ??= new Accounting($this);
    }

    /**
     * Retrieve the domain handler instance.
     *
     * @return Domain
     */
    public function domain(): Domain
    {
        return $this->domainHandler ??= new Domain($this);
    }

    /**
     * Retrieves the RootServer instance. If the instance does not already exist, it initializes a new RootServer object.
     *
     * @return RootServer The instance of the RootServer.
     */
    public function rootServer(): RootServer
    {
        return $this->rootServerHandler ??= new RootServer($this);
    }
}