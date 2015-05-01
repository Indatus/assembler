<?php
namespace Indatus\Assembler\Adapters;

use DigitalOceanV2\DigitalOceanV2;
use Indatus\Assembler\Contracts\CloudAdapterInterface;

class DigitalOceanAdapter implements CloudAdapterInterface
{
    protected $digitalOceanV2;

    public function __construct(DigitalOceanV2 $digitalOceanV2)
    {
        $this->digitalOceanV2 = $digitalOceanV2;
    }

    /**
     * @param string $hostName the hostname of the droplet being created
     * @param string $region the region where the droplet should be provisioned
     * @param string $size the size of the droplet being provisioned
     * @param string $image the image being used for the doplet
     * @param bool $backups true if you want backups to be created
     * @param bool $ipv6 true if you want to use ipv6 networking
     * @param bool $privateNetworking true if you want the droplet on a private network
     * @param array $sshKeys an array of keys to be used on the newly created droplet
     * @return MachineObject
     */
    public function create(
        $hostName,
        $region,
        $size,
        $image,
        $backups,
        $ipv6,
        $privateNetworking,
        array $sshKeys = array()
    ) {
        $dropletApi = $this->digitalOceanV2->droplet();
        $droplet = $dropletApi->create(
            $hostName,
            $region,
            $size,
            $image,
            $backups,
            $ipv6,
            $privateNetworking,
            $sshKeys
        );
        $machine = new MachineObject();
        $machine->hostname = $droplet->name;
        $machine->region = $droplet->region;
        $machine->size = $droplet->size;
        $machine->id = $droplet->id;
        // Wait for the network to be established
        // so we can retrieve the droplet's ip address
        sleep(10);
        $droplet = $dropletApi->getById($droplet->id);
        $machine->ip = $droplet->networks[0]->ipAddress;
        return $machine;
    }

    /**
     * Drops the specified droplet
     * @param $id
     */
    public function drop($id)
    {
        return $this->digitalOceanV2->droplet()->delete($id);
    }
}