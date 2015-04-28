<?php
namespace Indatus\Assembler\Contracts;

interface CloudAdapterInterface
{
    public function create($hostname, $region, $size, $image, $backups, $ipv6, $privateNetworking, array $sshKeys);

    public function drop($id);

}