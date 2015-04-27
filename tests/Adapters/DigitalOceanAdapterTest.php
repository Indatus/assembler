<?php
/**
 * Created by PhpStorm.
 * User: tbell
 * Date: 4/27/15
 * Time: 11:54 AM
 */

namespace Indatus\Assembler\Tests\Adapters;

use Indatus\Assembler\Adapters\DigitalOceanAdapter;
use Mockery as m;

class DigitalOceanAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testDrop()
    {
        $id = uniqid();
        $digitalOceanV2 = m::mock('DigitalOceanV2\DigitalOceanV2');
        $adapterUnderTest = new DigitalOceanAdapter($digitalOceanV2);
        $dropletMock = m::mock("DigitalOceanV2\Api\Droplet");
        $digitalOceanV2->shouldReceive('droplet')
            ->once()
            ->andReturn($dropletMock);
        $dropletMock->shouldReceive('delete')
            ->once()
            ->with($id);

        $adapterUnderTest->drop($id);

    }

    public function testCreate()
    {
        $hostName = "some_host_name";
        $region = "nyc3";
        $size = "512mb";
        $image = "docker";
        $backups = false;
        $ipv6 = false;
        $privateNetworking = false;
        $sshKeys = ['somekey'];

        $digitalOceanV2 = m::mock('DigitalOceanV2\DigitalOceanV2');

        $dropletMock = m::mock("DigitalOceanV2\Api\Droplet");
        $adapterUnderTest = new DigitalOceanAdapter($digitalOceanV2);
        $digitalOceanV2->shouldReceive('droplet')
            ->once()
            ->andReturn($dropletMock);
        $dropletMock->shouldReceive('create')
            ->once()
            ->with($hostName, $region, $size, $image, $backups, $ipv6, $privateNetworking, $sshKeys);

        $adapterUnderTest->create(
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
}
