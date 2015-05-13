<?php
namespace Indatus\Assembler\Test\Tasks;

use Indatus\Assembler\Tasks\ProvisionTask;
use Mockery as m;

class ProvisionTaskTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testRun()
    {
        $hostName = "hostname";
        $region = "nyc3";
        $size = "512mb";
        $image = "docker";
        $backups = true;
        $ipv6   = true;
        $privateNetworking = true;
        $sshKeys = ['keys'];
        $droplet = m::mock('\DigitalOceanV2\Api\Droplet');
        $userData = "";
        /**
         * @var $cloudAdapter \Indatus\Assembler\Contracts\CloudAdapterInterface|\Mocker\MockInterface
         */
        $cloudAdapter = m::mock('Indatus\Assembler\Contracts\CloudAdapterInterface');
        $provision_task = new ProvisionTask(
            $hostName,
            $region,
            $size,
            $image,
            $backups,
            $ipv6,
            $privateNetworking,
            $sshKeys,
            $cloudAdapter,
            $userData
        );
        $cloudAdapter->shouldReceive('create')
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
            )->andReturn($droplet);
        $result = $provision_task->run();
        $this->assertEquals($result->getExitCode(), 0);
        $this->assertEquals($result->getData(), $droplet);
    }
}
