<?php
namespace Tests\Assembler;

use Mockery;
use Assembler\Shipper;

class ShipperTest extends \PHPUnit_Framework_TestCase
{
    protected $shipper;

    protected $image;

    public function __construct()
    {
        $this->shipper = new Shipper();

        $this->image = 'nginx';
    }

    public function testPullImage()
    {
        $this->assertEquals(
            $this->shipper->pullImage($this->image),
            'docker pull '.$this->image
        );
    }

    public function testPullImageAsSudo()
    {
        $this->assertEquals(
            $this->shipper->pullImage($this->image, true),
            'sudo docker pull '.$this->image
        );
    }

    public function testBuildSinglePort()
    {
        $ports = '3306:3306';
        $this->assertEquals(
            $this->shipper->buildPorts($ports),
            ' -p 3306:3306'
        );
    }

    public function testBuildMultiplePorts()
    {
        $ports = '3306:3306,80:80,443:443';
        $this->assertEquals(
            $this->shipper->buildPorts($ports),
            ' -p 3306:3306 -p 80:80 -p 443:443'
        );
    }
}
