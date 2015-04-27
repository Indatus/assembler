<?php
namespace Indatus\Assembler\Traits;

use Indatus\Assembler\Tasks\ProvisionTask;

trait ProvisionTrait
{
    public function taskProvisionServer(
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
        return new ProvisionTask(
            $token,
            $sshKeys,
            $hostname,
            $region,
            $size,
            $image,
            $backups,
            $ipv6,
            $privateNetworking
        );
    }
}
