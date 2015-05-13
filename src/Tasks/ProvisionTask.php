<?php
namespace Indatus\Assembler\Tasks;

use Indatus\Assembler\Contracts;
use Robo\Contract\TaskInterface;
use Indatus\Assembler\Contracts\CloudAdapterInterface;
use Robo\Result;

class ProvisionTask implements TaskInterface
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $repo;

    /** @var string */
    protected $name;

    /** @var \Indatus\Assembler\Contracts\CloudAdapterInterface */
    protected $cloudAdapter;

    /**
     * @var array
     */
    protected $sshKeys;

    /** @var string */
    protected $userData;

    /**
     * @param array  $sshKeys
     * @param string $hostname
     * @param string $region
     * @param string $size
     * @param string $image
     * @param bool   $backups
     * @param bool   $ipv6
     * @param bool   $privateNetworking
     * @param string $userData
     */
    public function __construct(
        $hostname,
        $region,
        $size,
        $image,
        $backups,
        $ipv6,
        $privateNetworking,
        array $sshKeys = [],
        CloudAdapterInterface $cloudAdapter,
        $userData
    ) {
        $this->cloudAdapter      = $cloudAdapter;
        $this->hostname          = $hostname;
        $this->region            = $region;
        $this->size              = $size;
        $this->image             = $image;
        $this->backups           = $backups;
        $this->ipv6              = $ipv6;
        $this->privateNetworking = $privateNetworking;
        $this->sshKeys           = $sshKeys;
        $this->userData          = $userData;
    }

    /**
     * @return \Robo\Result
     */
    public function run()
    {
        $droplet = $this->cloudAdapter->create(
            $this->hostname,
            $this->region,
            $this->size,
            $this->image,
            $this->backups,
            $this->ipv6,
            $this->privateNetworking,
            $this->sshKeys,
            $this->userData
        );

        return new Result($this, 0, '', $droplet);
    }
}
