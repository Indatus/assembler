<?php
namespace Indatus\Assembler\Contracts;

interface CloudAdapterInterface
{
    /**
     * @param string $hostName          the hostname of the droplet being created
     * @param string $region            the region where the droplet should be provisioned
     * @param string $size              the size of the droplet being provisioned
     * @param string $image             the image being used for the droplet
     * @param bool   $backups           true if you want backups to be created
     * @param bool   $ipv6              true if you want to use ipv6 networking
     * @param bool   $privateNetworking true if you want the droplet on a private network
     * @param array  $sshKeys           an array of keys to be used on the newly created droplet
     * @param string $userData          path to a cloud-config script for provisioning
     *
     * @return \Indatus\Assembler\Adapters\MachineObject
     */
    public function create(
        $hostname,
        $region,
        $size,
        $image,
        $backups,
        $ipv6,
        $privateNetworking,
        array $sshKeys,
        $userData
    );

    /**
     * Drops the specified droplet
     *
     * @param $id
     */
    public function drop($id);
}
