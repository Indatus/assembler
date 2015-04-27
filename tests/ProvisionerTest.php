<?php
namespace Indatus\Assembler\Test;

use Mockery;
use Indatus\Assembler\Provisioner;

class ProvisionerTest extends \PHPUnit_Framework_TestCase
{
    public function testProvisionCallsCreateWithSaneDefaults()
    {
        /** @var \Mockery\Mock $digitalOcean */
        $digitalOcean = Mockery::mock('\DigitalOceanV2\DigitalOceanV2');
        /** @var \Mockery\Mock $digitalOcean */
        $droplet = Mockery::mock('\DigitalOceanV2\Api\Droplet');
        $digitalOcean->shouldReceive('droplet')
            ->andReturn($droplet);
        $droplet->shouldReceive('create')
            ->once()
            ->with(
                'apt-5b',
                'nyc3',
                '512mb',
                'docker',
                false,
                false,
                false,
                array(),
                ""
            )
            ->andReturnSelf();

        $shipper        = new Provisioner('token', array(), $digitalOcean);
        $createdDroplet = $shipper->provision('apt-5b');

        $this->assertSame($droplet, $createdDroplet);
    }
}
