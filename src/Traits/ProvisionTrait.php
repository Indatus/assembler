<?php
namespace Indatus\Assembler\Traits;

use Indatus\Assembler\AdapterFactory;
use Indatus\Assembler\Configuration;
use Indatus\Assembler\Tasks\ProvisionTask;

trait ProvisionTrait
{
    public function taskProvisionServer(
        $hostname,
        $region,
        $size,
        $image,
        $backups,
        $ipv6,
        $privateNetworking
    ) {
        $configuration = new Configuration();
        $sshKeys = $configuration->sshKeys();
        $userData = $configuration->userData();
        var_dump($sshKeys);
        return new ProvisionTask(
            $hostname,
            $region,
            $size,
            $image,
            $backups,
            $ipv6,
            $privateNetworking,
            $sshKeys,
            AdapterFactory::make($configuration),
            $userData
        );
    }
}
