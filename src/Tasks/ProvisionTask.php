<?php
namespace Indatus\Assembler\Tasks;

use Indatus\Assembler\Provisioner;
use Robo\Contract\TaskInterface;
use Robo\Result;

class ProvisionTask implements TaskInterface
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $repo;

    /** @var string */
    protected $name;

    /** @var \Indatus\Assembler\Provisioner */
    protected $provisioner;

    /**
     * @param string $token
     * @param array  $sshKeys
     * @param string $hostname
     * @param string $region
     * @param string $size
     * @param string $image
     * @param bool   $backups
     * @param bool   $ipv6
     * @param bool   $privateNetworking
     */
    public function __construct(
        $token,
        $sshKeys,
        $hostname,
        $region,
        $size,
        $image,
        $backups,
        $ipv6,
        $privateNetworking
    ) {
        $this->provisioner       = new Provisioner($token, $sshKeys);
        $this->hostname          = $hostname;
        $this->region            = $region;
        $this->size              = $size;
        $this->image             = $image;
        $this->backups           = $backups;
        $this->ipv6              = $ipv6;
        $this->privateNetworking = $privateNetworking;
    }

    /**
     * @return \Robo\Result
     */
    function run()
    {
        $droplet = $this->provisioner->provision(
            $this->hostname,
            $this->region,
            $this->size,
            $this->image,
            $this->backups,
            $this->ipv6,
            $this->privateNetworking
        );

        return new Result($this, 0, '', $droplet);
    }
}