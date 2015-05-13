<?php
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

    public function testCreateWhenIPV4True()
    {
        $hostName = "some_host_name";
        $region = "nyc3";
        $size = "512mb";
        $image = "docker";
        $backups = false;
        $ipv6 = false;
        $privateNetworking = false;
        $sshKeys = ['somekey'];
        $userData = "provision.sh";

        $digitalOceanV2 = m::mock('DigitalOceanV2\DigitalOceanV2');
        $dropletEntityMock = m::mock("DigitalOceanV2\Entity\Droplet");
        $dropletEntityMock->name = $hostName;
        $dropletEntityMock->region = $region;
        $dropletEntityMock->size = $size;
        $dropletEntityMock->id = 1232456;
        $network = m::mock(
            'DigitalOceanV2\Entity\Network'
        );
        $network->ipAddress = "192.168.2.1";
        $dropletEntityMock->networks = [
            $network
        ];
        $dropletMock = m::mock("DigitalOceanV2\Api\Droplet");
        $adapterUnderTest = new DigitalOceanAdapter($digitalOceanV2);
        $digitalOceanV2->shouldReceive('droplet')
            ->once()
            ->andReturn($dropletMock);
        $dropletMock->shouldReceive('create')
            ->once()
            ->with(
                $hostName,
                $region,
                $size,
                $image,
                $backups,
                $ipv6,
                $privateNetworking,
                $sshKeys,
                $userData
            )->andReturn($dropletEntityMock);
        $dropletMock->shouldReceive('getById')
            ->once()
            ->with($dropletEntityMock->id)
            ->andReturn($dropletEntityMock);

        $result = $adapterUnderTest->create(
            $hostName,
            $region,
            $size,
            $image,
            $backups,
            $ipv6,
            $privateNetworking,
            $sshKeys,
            $userData
        );
        $this->assertInstanceOf(
            '\Indatus\Assembler\Adapters\MachineObject',
            $result
        );
        $this->assertEquals($result->hostname, $hostName);
        $this->assertEquals($result->region, $region);
        $this->assertEquals($result->size, $size);
        $this->assertEquals($result->ip, $network->ipAddress);
    }
}
