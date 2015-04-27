<?php
namespace Indatus\Assembler;

use DigitalOceanV2\Adapter\Guzzle5Adapter;
use DigitalOceanV2\DigitalOceanV2;

class Provisioner
{

    /** @var \DigitalOceanV2\DigitalOceanV2|null */
    protected $digitalOcean;

    /** @var array */
    protected $sshKeys;

    /**
     * @param string                         $token
     * @param array                          $sshKeys
     * @param \DigitalOceanV2\DigitalOceanV2 $digitalOcean
     */
    public function __construct($token, array $sshKeys = array(), DigitalOceanV2 $digitalOcean = null)
    {
        $this->digitalOcean = is_null($digitalOcean) ? new DigitalOceanV2(new Guzzle5Adapter($token)) : $digitalOcean;
        $this->sshKeys      = $sshKeys;
    }

    /**
     * @param        $hostname
     * @param string $region
     * @param string $size
     * @param string $image
     * @param bool   $backups
     * @param bool   $ipv6
     * @param bool   $privateNetworking
     * @param string $userData
     * @return \DigitalOceanV2\Entity\Droplet
     */
    public function provision(
        $hostname,
        $region = 'nyc3',
        $size = '512mb',
        $image = 'docker',
        $backups = false,
        $ipv6 = false,
        $privateNetworking = false
    ) {
        $droplet = $this->digitalOcean->droplet();

        $created = $droplet->create(
            $hostname,
            $region,
            $size,
            $image,
            $backups,
            $ipv6,
            $privateNetworking,
            $this->sshKeys,
            $userData = ""
        );

        return $created;
    }
}
