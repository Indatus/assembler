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
     * @return \DigitalOceanV2\Api\Droplet
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
        $droplet = $this->digitalOceanV2->droplet();
        return $droplet->create(
            $hostName,
            $region,
            $size,
            $image,
            $backups,
            $ipv6,
            $privateNetworking,
            $sshKeys
        );
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