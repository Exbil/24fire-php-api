<?php

namespace FireAPI\RootServer;

use FireAPI\Client;
use FireAPI\Traits\VMIdValidator;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;

class RootServer
{
    use VMIdValidator;
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
     * Retrieves a list of virtual machines from the client.
     *
     * This method sends a request to the client's 'vm/list' endpoint,
     * and returns the response containing the virtual machine data.
     *
     * @return mixed The response from the client's 'vm/list' endpoint.
     * @throws GuzzleException
     */
    public function getAll(): mixed
    {
        return $this->client->get('vm/list');
    }

    /**
     * Retrieves a list of host systems from the client.
     *
     * This method communicates with the 'vm/list/hosts' endpoint of the client
     * and returns the response containing the host system information.
     *
     * @return mixed The response from the client's 'vm/list/hosts' endpoint.
     * @throws GuzzleException
     */
    public function getHostSystems(): mixed
    {
        return $this->client->get('vm/list/hosts');
    }

    /**
     * Retrieves a list of operating systems from the client.
     *
     * This method makes a request to the client's 'vm/list/os' endpoint
     * and returns the response containing the available operating system data.
     *
     * @return mixed The response from the client's 'vm/list/os' endpoint.
     * @throws GuzzleException
     */
    public function getOsList(): mixed
    {
        return $this->client->get('vm/list/os');
    }

    /**
     * Retrieves a list of ISOs from the client.
     *
     * This method sends a request to the client to fetch
     * the available ISOs in the virtual machine list.
     *
     * @return mixed The response from the client containing the list of ISOs.
     * @throws GuzzleException
     */
    public function getIsos(): mixed
    {
        return $this->client->get('vm/list/iso');
    }

    /**
     * @param int $vm_id
     * @param string $iso
     * @return mixed
     * @throws GuzzleException
     */
    public function mountIso(int $vm_id, string $iso): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->put("vm/{$vm_id}/iso", ['iso' => $iso]);
    }

    /**
     * @param int $vm_id
     * @return mixed
     * @throws GuzzleException
     */
    public function unmountIso(int $vm_id): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->delete("vm/{$vm_id}/iso");
    }

    /**
     * Retrieves DDoS protection details for a specified virtual machine.
     *
     * This method sends a request to the client to fetch information
     * about the DDoS protection settings for the given virtual machine ID.
     *
     * @param int $vm_id The ID of the virtual machine to retrieve DDoS protection details for.
     * @return mixed The response from the client containing the DDoS protection details.
     * @throws GuzzleException
     */
    public function getDDoSProtection(int $vm_id): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->get("vm/{$vm_id}/ddos");
    }

    /**
     * Configures DDoS protection settings for a specified virtual machine.
     *
     * This method updates the DDoS protection configuration, including Layer 4 and Layer 7 settings,
     * for a specific virtual machine using the provided IP address.
     *
     * @param int $vm_id The ID of the virtual machine.
     * @param bool $layer4 Whether to enable Layer 4 DDoS protection.
     * @param bool $layer7 Whether to enable Layer 7 DDoS protection.
     * @param string $ip_address The IP address to apply the DDoS protection settings to.
     * @return mixed The response from the client after setting the DDoS protection.
     * @throws GuzzleException
     */
    public function setDDoSProtection(int $vm_id, bool $layer4, bool $layer7, string $ip_address): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->post("vm/{$vm_id}/ddos", ['layer4' => $layer4, 'layer7' => $layer7, 'ip_address' => $ip_address]);
    }

    /**
     * Retrieves a list of backups for a specified virtual machine.
     *
     * This method sends a request to the client to fetch
     * the available backups associated with the given virtual machine ID.
     *
     * @param int $vm_id The ID of the virtual machine for which to fetch the backups.
     * @return mixed The response from the client containing the list of backups.
     * @throws GuzzleException
     */
    public function getBackups(int $vm_id): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->get("vm/{$vm_id}/backup/list");
    }

    /**
     * Initiates the creation of a backup for a specified virtual machine.
     *
     * This method sends a request to create a backup for the virtual machine
     * identified by its unique ID.
     *
     * @param int $vm_id The unique identifier of the virtual machine for which the backup is created.
     * @return mixed The response from the client after initiating the backup creation.
     * @throws GuzzleException
     */
    public function createBackup(int $vm_id): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->post("vm/{$vm_id}/backup/create");
    }

    /**
     * Retrieves the backup state for a specific virtual machine and backup ID.
     *
     * This method sends a request to the client to fetch the state
     * of a backup operation associated with the given virtual machine ID
     * and backup ID.
     *
     * @param int $vm_id The ID of the virtual machine.
     * @param int $backup_id The ID of the backup.
     * @return mixed The response from the client containing the backup state.
     * @throws GuzzleException
     */
    public function getBackupState(int $vm_id, int $backup_id): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->get("vm/{$vm_id}/backup/create/status", ['backup_id' => $backup_id]);
    }

    /**
     * Restores a specific backup for a virtual machine.
     *
     * This method sends a request to the client to restore a backup
     * associated with the given virtual machine and backup IDs.
     *
     * @param int $vm_id The ID of the virtual machine for which the backup will be restored.
     * @param int $backup_id The ID of the backup to be restored.
     * @return mixed The response from the client after processing the restore request.
     * @throws GuzzleException
     */
    public function restoreBackup(int $vm_id, int $backup_id): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->post("vm/{$vm_id}/backup/restore", ['backup_id' => $backup_id]);
    }

    /**
     * Restores the backup state for a specific virtual machine.
     *
     * This method sends a request to the client to initiate
     * the process of restoring the state of a virtual machine
     * from a specified backup.
     *
     * @param int $vm_id The ID of the virtual machine to restore.
     * @param int $backup_id The ID of the backup to restore from.
     * @return mixed The response from the client regarding the backup restore status.
     * @throws GuzzleException
     */
    public function restoreBackupState(int $vm_id, int $backup_id): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->post("vm/{$vm_id}/backup/restore/status", ['backup_id' => $backup_id]);
    }

    /**
     * Deletes a specific backup for a virtual machine.
     *
     * This method sends a request to the client to delete a backup
     * identified by its ID for the given virtual machine.
     *
     * @param int $vm_id The ID of the virtual machine.
     * @param int $backup_id The ID of the backup to be deleted.
     * @return mixed The response from the client after deleting the backup.
     * @throws GuzzleException
     */
    public function deleteBackup(int $vm_id, int $backup_id): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->delete("vm/{$vm_id}/backup/delete", ['backup_id' => $backup_id]);
    }

    /**
     * Updates the monitoring settings for a specified virtual machine.
     *
     * @param int $vm_id The ID of the virtual machine.
     * @param bool $enabled A flag indicating whether monitoring should be enabled or disabled.
     * @param int $port The port number to be used for monitoring.
     * @return mixed The response from the API call.
     * @throws GuzzleException
     */
    public function updateMonitoring(int $vm_id, bool $enabled, int $port): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->post("vm/{$vm_id}/monitoring/change", ['enabled' => $enabled, 'port' => $port]);
    }

    /**
     * Retrieves the monitoring timings for a specified virtual machine.
     *
     * @param int $vm_id The ID of the virtual machine.
     * @return mixed The response containing the monitoring timings data.
     * @throws GuzzleException
     */
    public function getMonitoringTimings(int $vm_id): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->get("vm/{$vm_id}/monitoring/timings");
    }

    /**
     * Retrieves the list of monitoring incidences for a specified virtual machine.
     *
     * @param int $vm_id The ID of the virtual machine.
     * @return mixed The response containing the monitoring incidences.
     * @throws GuzzleException
     */
    public function getIncidences(int $vm_id): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->get("vm/{$vm_id}/monitoring/incidences");
    }

    /**
     * Retrieves the traffic data for a specified virtual machine.
     *
     * @param int $vm_id The ID of the virtual machine.
     * @return mixed The traffic data returned from the API call.
     * @throws GuzzleException
     */
    public function getTraffic(int $vm_id): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->get("vm/{$vm_id}/traffic/current");
    }

    /**
     * @param int $vm_id
     * @return mixed
     * @throws GuzzleException
     */
    public function getTrafficLog(int $vm_id): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->get("vm/{$vm_id}/traffic/current/log");
    }

    /**
     * Retrieves traffic chart data for a specific virtual machine.
     *
     * @param int $vm_id The ID of the virtual machine
     * @param string $type The traffic type (from traffic.type enum: incoming, outgoing, both)
     * @param string $summary The data summary type (from traffic.summary enum: none, hourly, daily)
     * @param string $output The output format (from traffic.output enum: ChartJS, ApexCharts, Base64 Image)
     * @param array $options Additional optional parameters:
     *      - dataset_in_label: string (default: "Incoming Traffic")
     *      - dataset_out_label: string (default: "Outgoing Traffic")
     *      - dataset_in_color: string (default: "#0077FF", 6-digit hex code with #)
     *      - dataset_out_color: string (default: "#FF6347", 6-digit hex code with #)
     *      - axes_y_label: string (default: "Traffic in {unit}", use {unit} as placeholder)
     *      - data points: int (2-n)
     *          - For summary = DAILY: max 90
     *          - For summary = HOURLY: max 48
     *          - For summary = NONE: max 72
     *      - size: string (format: "middleweight", default: "900x300")
     *          - width: min 200, max 3000
     *          - height: min 200, max 3000
     * @return mixed The chart data in the specified output format
     * @throws GuzzleException
     */
    public function getTrafficChart(
        int $vm_id,
        string $type,
        string $summary,
        string $output,
        array $options = []
    ): mixed {
        $this->validateVMId($vm_id);

        $params = array_filter([
            'type' => $type,
            'summary' => $summary,
            'output' => $output,
            'dataset_in_label' => $options['dataset_in_label'] ?? null,
            'dataset_out_label' => $options['dataset_out_label'] ?? null,
            'dataset_in_color' => $options['dataset_in_color'] ?? null,
            'dataset_out_color' => $options['dataset_out_color'] ?? null,
            'axes_y_label' => $options['axes_y_label'] ?? null,
            'datapoints' => $options['datapoints'] ?? null,
            'size' => $options['size'] ?? null
        ]);

        return $this->client->post("vm/{$vm_id}/traffic/chart", $params);
    }

    /**
     * Retrieves traffic addon details for a specified virtual machine.
     *
     * @param int $vm_id The ID of the virtual machine.
     * @return mixed The response containing traffic addon data.
     * @throws GuzzleException
     */
    public function getTrafficAddons(int $vm_id): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->get("vm/{$vm_id}/traffic/addons");
    }

    /**
     * Orders a traffic addon for a specified virtual machine.
     *
     * @param int $vm_id The ID of the virtual machine.
     * @param int $addon The ID of the traffic addon to be ordered.
     * @return mixed The response from the API call.
     * @throws GuzzleException
     */
    public function orderTrafficAddon(int $vm_id, int $addon): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->post("vm/{$vm_id}/traffic/addons/order", ['addon' => $addon]);
    }

    /**
     * Retrieves the list of SSH keys associated with a specified virtual machine.
     *
     * @param int $vm_id The ID of the virtual machine.
     * @return mixed The response containing the list of SSH keys.
     * @throws GuzzleException
     */
    public function getSSHKeys(int $vm_id): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->get("vm/{$vm_id}/sshkey/list");
    }

    /**
     * Generates and creates a new SSH key for the specified virtual machine.
     *
     * @param int $vm_id The ID of the virtual machine for which the SSH key will be created.
     * @param string $displayname The display name for the newly created SSH key.
     *
     * @return mixed The response from the client after creating the SSH key.
     * @throws GuzzleException
     */
    public function createSSHKey(int $vm_id, string $displayname): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->post("vm/{$vm_id}/sshkey/generate", ['displayname' => $displayname]);
    }

    /**
     * Uploads a public SSH key to the specified virtual machine.
     *
     * @param int $vm_id The ID of the virtual machine to which the SSH key will be uploaded.
     * @param string $public_key The public SSH key to be uploaded.
     * @param string $displayname The display name for the uploaded SSH key.
     *
     * @return mixed The response from the client after uploading the SSH key.
     * @throws GuzzleException
     */
    public function uploadSSHKey(int $vm_id, string $public_key, string $displayname): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->post("vm/{$vm_id}/sshkey/upload", ['public_key' => $public_key, 'displayname' => $displayname]);
    }

    /**
     * Deletes an existing SSH key from the specified virtual machine.
     *
     * @param int $vm_id The ID of the virtual machine from which the SSH key will be deleted.
     * @param int $sshkey_id The ID of the SSH key to be deleted.
     *
     * @return mixed The response from the client after deleting the SSH key.
     * @throws GuzzleException
     */
    public function deleteSSHKey(int $vm_id, int $sshkey_id): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->delete("vm/{$vm_id}/sshkey/remove", ['sshkey_id' => $sshkey_id]);
    }

    /**
     * Reinstall the operating system on the specified virtual machine.
     *
     * @param int $vm_id The ID of the virtual machine to be reinstalled.
     * @param string|null $os The operating system to install. If null, the default OS will be reinstalled.
     *
     * @return mixed The response from the client after initiating the installation process.
     * @throws GuzzleException
     */
    public function reinstall(int $vm_id, ?string $os = null): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->post("vm/{$vm_id}/reinstall", ['os' => $os]);
    }

    /**
     * Creates a new virtual machine with the specified configuration.
     *
     * @param int $cores Number of CPU cores for the virtual machine
     * @param int $ram Amount of RAM in GB for the virtual machine
     * @param int $disk Storage space in GB for the virtual machine
     * @param int $ipv4 Number of IPv4 addresses to assign
     * @param int $ipv6 Number of IPv6 addresses to assign
     * @param int $os_id Operating system ID to install
     * @param int $backups Number of backup slots to allocate
     * @param string|null $host_name The hostname for the virtual machine (optional)
     * @param string|null $custom_name Custom name for the virtual machine (optional)
     * @param string|null $root_password Root password for the virtual machine (optional)
     * @return mixed Response from the API containing the created virtual machine details
     * @throws GuzzleException
     */
    public function create(
        int $cores,
        int $ram,
        int $disk,
        int $ipv4,
        int $ipv6,
        int $os_id,
        int $backups,
        ?string $host_name = null,
        ?string $custom_name = null,
        ?string $root_password = null
    ): mixed {
        $params = array_filter([
            'cores' => $cores,
            'ram' => $ram,
            'disk' => $disk,
            'ipv4' => $ipv4,
            'ipv6' => $ipv6,
            'os_id' => $os_id,
            'backups' => $backups,
            'hostname' => $host_name,
            'custom_name' => $custom_name,
            'root_password' => $root_password
        ]);

        return $this->client->post('vm/create', $params);
    }

    /**
     * Updates the configuration of a virtual machine.
     *
     * @param string $vm_id Five-digit identifier of the VM
     * @param array $options Configuration options:
     *      - cores: int (1-n) Number of virtual cores
     *      - mem: int (1024-n) RAM in MB
     *      - disk: int (10-n) NVMe storage in GB
     *      - storage: int (0, 500-n) Additional HDD storage (NTT VMs only, use 0 to remove)
     *      - backup_slots: int (1-n) Number of backup slots (default and minimum: 2)
     *      - network_speed: int (1000-n) Network speed in Mbit/s
     *      - allowFallbackIPs: bool Whether to allow fallback IPs outside own subnets when using custom IP subnets (default: false)
     * @return mixed Response from the API containing the updated VM configuration
     * @throws GuzzleException
     * @throws InvalidArgumentException If vmid is not a five-digit number
     */
    public function updateConfiguration(string $vm_id, array $options = []): mixed
    {
        $this->validateVMId($vm_id);

        // Filter out null values and create params array
        $params = array_filter([
            'cores' => $options['cores'] ?? null,
            'mem' => $options['mem'] ?? null,
            'disk' => $options['disk'] ?? null,
            'storage' => $options['storage'] ?? null,
            'backup_slots' => $options['backup_slots'] ?? null,
            'network_speed' => $options['network_speed'] ?? null,
            'allowFallbackIPs' => $options['allowFallbackIPs'] ?? null
        ], function($value) {
            return $value !== null;
        });

        return $this->client->put("vm/{$vm_id}/config", $params);
    }

    /**
     * Retrieves the configuration details of the specified virtual machine.
     *
     * @param string $vm_id The ID of the virtual machine whose configuration details are to be retrieved.
     *
     * @return mixed The response from the client containing the virtual machine's configuration details.
     * @throws GuzzleException
     */
    public function getVM(string $vm_id): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->get("vm/{$vm_id}/config");
    }

    /**
     * Updates the settings of the specified virtual machine.
     *
     * @param int $vm_id The ID of the virtual machine to be updated.
     * @param bool $root_password_login Indicates whether root password login should be enabled or disabled.
     *
     * @return mixed The response from the client after updating the virtual machine settings.
     * @throws GuzzleException
     */
    public function updateVMSettings(int $vm_id, bool $root_password_login): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->post("vm/{$vm_id}/settings", ['root_password_login' => $root_password_login]);
    }

    /**
     * Configures the reverse DNS (RDNS) settings for the specified virtual machine.
     *
     * @param int $vm_id The ID of the virtual machine for which the RDNS will be configured.
     * @param string $domain The domain name to set for the RDNS.
     * @param string|null $ip_address Optional. The IP address to associate with the RDNS setting. Defaults to null.
     *
     * @return mixed The response from the client after setting the RDNS.
     * @throws GuzzleException
     */
    public function setRDNS(int $vm_id, string $domain, ?string $ip_address = null): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->post("vm/{$vm_id}/rdns", ['domain' => $domain, 'ip_address' => $ip_address]);
    }

    /**
     * Initiates a noVNC session for the specified virtual machine.
     *
     * @param int $vm_id The ID of the virtual machine for which the noVNC session will be started.
     *
     * @return mixed The response from the client after initiating the noVNC session.
     * @throws GuzzleException
     */
    public function getVNC(int $vm_id)
    {
        $this->validateVMId($vm_id);
        return $this->client->post("vm/{$vm_id}/novnc");
    }

    /**
     * Deletes a virtual machine with the specified ID.
     *
     * @param int $vm_id The ID of the virtual machine to be deleted.
     *
     * @return mixed The response from the client after attempting to delete the virtual machine.
     * @throws GuzzleException
     */
    public function delete(int $vm_id): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->delete("vm/{$vm_id}/delete");
    }

    /**
     * Retrieves the current state or status of the specified virtual machine.
     *
     * @param int $vm_id The ID of the virtual machine whose state will be retrieved.
     *
     * @return mixed The response from the client containing the virtual machine's state or status.
     * @throws GuzzleException
     */
    public function getState(int $vm_id): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->get("vm/{$vm_id}/status");
    }

    /**
     * Retrieves the installation status of the specified virtual machine.
     *
     * @param int $vm_id The ID of the virtual machine for which the installation status is being retrieved.
     *
     * @return mixed The response containing the installation status from the client.
     * @throws GuzzleException
     */
    public function getInstallationStatus(int $vm_id): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->get("vm/{$vm_id}/status/installation");
    }

    /**
     * Resets the password for the specified virtual machine.
     *
     * @param int $vm_id The ID of the virtual machine for which the password will be reset.
     *
     * @return mixed The response from the client after initiating the password reset process.
     * @throws GuzzleException
     */
    public function resetPassword(int $vm_id): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->post("vm/{$vm_id}/password-reset");
    }

    /**
     * Manage the power state of a virtual machine by specifying the action to be performed.
     *
     * @param int $vm_id The unique identifier of the virtual machine.
     * @param string $action The action to perform on the virtual machine's power state (e.g., start, stop, restart).
     * @return mixed The response from the client after performing the power action.
     * @throws GuzzleException
     */
    public function power(int $vm_id, string $action): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->post("vm/{$vm_id}/power", ['mode' => $action]);
    }

    /**
     * Retrieves abuse data for a specific virtual machine (VM) based on the provided VM ID.
     *
     * @param int $vm_id The unique identifier of the VM for which abuse data is to be retrieved.
     * @return mixed The response from the client containing abuse data for the specified VM.
     * @throws GuzzleException
     */
    public function getAbuse(int $vm_id): mixed
    {
        $this->validateVMId($vm_id);
        return $this->client->get("vm/{$vm_id}/abuses");
    }
}