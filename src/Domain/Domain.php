<?php

namespace FireAPI\Domain;

use FireAPI\Client;
use GuzzleHttp\Exception\GuzzleException;

class Domain
{
    private Client $client;

    /**
     * Constructor method.
     *
     * @param Client $client The client instance to be used.
     *
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Retrieves DNS records for the specified domain.
     *
     * @param string $domain The domain name for which DNS records are to be retrieved.
     *
     * @return mixed The DNS records associated with the given domain.
     * @throws GuzzleException
     */
    public function getDNSRecords(string $domain): mixed
    {
        return $this->client->get("domains/{$domain}/dns");
    }

    /**
     * Creates a DNS record for the specified domain.
     *
     * @param string $domain The domain for which the DNS record is to be created.
     * @param string $type The type of the DNS record (e.g., A, CNAME, TXT).
     * @param string $name The name of the DNS record.
     * @param string $data The data associated with the DNS record.
     *
     * @return mixed The response from the API after creating the DNS record.
     * @throws GuzzleException
     */
    public function createDNSRecord(string $domain, string $type, string $name, string $data): mixed
    {
        return $this->client->post("domains/{$domain}/dns/add", ['type' => $type, 'name' => $name, 'data' => $data]);
    }

    /**
     * Deletes a DNS record for the specified domain.
     *
     * @param string $domain The domain name from which the DNS record will be deleted.
     * @param int $record_id The unique identifier of the DNS record to be deleted.
     *
     * @return mixed The response from the client after attempting to delete the DNS record.
     * @throws GuzzleException
     */
    public function deleteDNSRecord(string $domain, int $record_id): mixed
    {
        return $this->client->delete("domains/{$domain}/dns/remove", ['record_id' => $record_id]);
    }

    /**
     * Updates a DNS record for a given domain.
     *
     * @param string $domain The domain name for which the DNS record will be updated.
     * @param int $record_id The ID of the DNS record to update.
     * @param string|null $type The type of the DNS record (optional).
     * @param string|null $name The name of the DNS record (optional).
     * @param string|null $data The data associated with the DNS record (optional).
     *
     * @return mixed The response from the client after updating the DNS record.
     * @throws GuzzleException
     */
    public function updateDNSRecord(string $domain, int $record_id, ?string $type = null, ?string $name = null, ?string $data = null): mixed
    {
        return $this->client->post("domains/{$domain}/dns/update", [
            'record_id' => $record_id,
            'type' => $type,
            'name' => $name,
            'data' => $data
        ]);
    }

    /**
     * Retrieves the handle information from the client.
     *
     * @param string $handle The handle identifier to retrieve information for.
     *
     * @return mixed The data associated with the specified handle.
     * @throws GuzzleException
     */
    public function getHandle(string $handle): mixed
    {
        return $this->client->get("domain/handle/{$handle}/info");
    }

    /**
     * Creates a handle with the given personal and address details.
     *
     * @param string $gender The gender of the individual.
     * @param string $firstname The first name of the individual.
     * @param string $lastname The last name of the individual.
     * @param string $street The street name of the address.
     * @param string $number The house or building number of the address.
     * @param string $zipcode The postal code of the address.
     * @param string $city The city of the address.
     * @param string $region The region or state of the address.
     * @param string $countrycode The country code in ISO format.
     * @param string $email The email address of the individual.
     *
     * @return mixed The response from the client after creating the handle.
     * @throws GuzzleException
     */
    public function createHandle(
        string $gender,
        string $firstname,
        string $lastname,
        string $street,
        string $number,
        string $zipcode,
        string $city,
        string $region,
        string $countrycode,
        string $email
    ): mixed
    {
        return $this->client->put("domain/handle/create", [
            'gender' => $gender,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'street' => $street,
            'number' => $number,
            'zipcode' => $zipcode,
            'city' => $city,
            'region' => $region,
            'countrycode' => $countrycode,
            'email' => $email
        ]);
    }

    /**
     * Updates the details of a handle with the specified parameters.
     *
     * @param int $handleId The unique identifier of the handle to be updated.
     * @param string|null $gender Optional. The gender to update.
     * @param string|null $firstname Optional. The first name to update.
     * @param string|null $lastname Optional. The last name to update.
     * @param string|null $street Optional. The street address to update.
     * @param string|null $number Optional. The house or apartment number to update.
     * @param string|null $zipcode Optional. The postal code to update.
     * @param string|null $city Optional. The city to update.
     * @param string|null $region Optional. The region or state to update.
     * @param string|null $countrycode Optional. The country code to update.
     * @param string|null $email Optional. The email address to update.
     *
     * @return mixed The response from the client after updating the handle.
     * @throws GuzzleException
     */
    public function updateHandle(
        int $handleId,
        ?string $gender = null,
        ?string $firstname = null,
        ?string $lastname = null,
        ?string $street = null,
        ?string $number = null,
        ?string $zipcode = null,
        ?string $city = null,
        ?string $region = null,
        ?string $countrycode = null,
        ?string $email = null
    ): mixed
    {
        $data = array_filter([
            'gender' => $gender,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'street' => $street,
            'number' => $number,
            'zipcode' => $zipcode,
            'city' => $city,
            'region' => $region,
            'countrycode' => $countrycode,
            'email' => $email
        ], fn($value) => $value !== null);

        return $this->client->post("domain/handle/{$handleId}/update", $data);
    }

    /**
     * Retrieves a list of countries associated with the specified domain handle.
     *
     * @return mixed The response from the client containing the countries data.
     * @throws GuzzleException
     */
    public function countries(): mixed
    {
        return $this->client->get("domain/handle/countries");
    }

    /**
     * Retrieves a list of all domains.
     *
     * @return mixed The response from the client containing the domain list.
     * @throws GuzzleException
     */
    public function getAll(): mixed
    {
        return $this->client->get("domain/list");
    }

    /**
     * Registers a new domain with the specified parameters.
     *
     * @param string $domain The domain name to be registered.
     * @param string $handle The handle or identifier for the owner of the domain.
     * @param string|null $authcode An optional authorization code required for registration.
     * @param string|null $ns1 Optional nameserver 1 for the domain.
     * @param string|null $ns2 Optional nameserver 2 for the domain.
     *
     * @return mixed The response from the client after attempting the domain registration.
     * @throws GuzzleException
     */
    public function register(string $domain, string $handle, ?string $authcode = null, ?string $ns1 = null, ?string $ns2 = null): mixed
    {
        $data = array_filter([
            'domain' => $domain,
            'handle' => $handle,
            'authcode' => $authcode,
            'ns1' => $ns1,
            'ns2' => $ns2
        ], fn($value) => $value !== null);

        return $this->client->post("domain/register", $data);
    }

    /**
     * Deletes a specified domain.
     *
     * @param string $domain The domain to be deleted.
     * @return mixed The response from the client after the deletion operation.
     * @throws GuzzleException
     */
    public function delete(string $domain): mixed
    {
        return $this->client->delete("domain/{$domain}/delete");
    }

    /**
     * Restores a previously deleted domain.
     *
     * @param string $domain The domain name to be restored.
     * @return mixed The response from the client after attempting to undelete the domain.
     * @throws GuzzleException
     */
    public function undelete(string $domain): mixed
    {
        return $this->client->post("domain/{$domain}/undelete");
    }

    /**
     * Retrieves the authorization code for the specified domain.
     *
     * @param string $domain The domain name for which the authorization code is requested.
     * @return mixed The response from the client containing the authorization code.
     * @throws GuzzleException
     */
    public function getAuthCode(string $domain): mixed
    {
        return $this->client->post("domain/{$domain}/authcode");
    }

    /**
     * Retrieves the pricing details for domains.
     *
     * @return mixed The response from the client containing domain pricing information.
     * @throws GuzzleException
     */
    public function getPrices(): mixed
    {
        return $this->client->get("domain/pricings");
    }

    /**
     * Retrieves detailed information for a specific domain.
     *
     * @param string $domain The domain name for which information is being requested.
     * @return mixed The response from the client containing the domain information.
     * @throws GuzzleException
     */
    public function info(string $domain): mixed
    {
        return $this->client->get("domain/{$domain}/info");
    }

    /**
     * @param string $domain
     * @return mixed
     * @throws GuzzleException
     */
    public function check(string $domain): mixed
    {
        return $this->client->get("domain/{$domain}/check");
    }

    /**
     * Updates the nameservers for the specified domain.
     *
     * @param string $domain The domain name for which the nameservers need to be updated.
     * @param string $ns1 The primary nameserver.
     * @param string $ns2 The secondary nameserver.
     * @param string|null $ns3 An optional tertiary nameserver.
     * @param string|null $ns4 An optional quaternary nameserver.
     * @param string|null $ns5 An optional quinary nameserver.
     *
     * @return mixed The response from the client after updating the nameservers.
     * @throws GuzzleException
     */
    public function updateNameservers(string $domain, string $ns1, string $ns2, ?string $ns3 = null, ?string $ns4 = null, ?string $ns5 = null): mixed
    {
        return $this->client->post("domain/{$domain}/update", [
            'ns1' => $ns1,
            'ns2' => $ns2,
            'ns3' => $ns3,
            'ns4' => $ns4,
            'ns5' => $ns5
        ]);
    }
}