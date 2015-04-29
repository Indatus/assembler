<?php
namespace Tests\Indatus\Assembler;

use Mockery;
use Indatus\Assembler\Shipper;

class ShipperTest extends \PHPUnit_Framework_TestCase
{
    protected $shipper;

    protected $shipperMock;

    protected $image;

    public function __construct()
    {
        $this->shipper = new Shipper();

        $this->image = 'nginx';
    }

    public function setUp()
    {
        $this->shipperMock = Mockery::mock('Indatus\Assembler\Shipper[buildPorts,buildContainerName]');
    }

    public function testRunContainer()
    {
        $image_name = 'nginx_123456789';
        $ports = '80:80';

        $this->shipperMock
            ->shouldReceive('buildContainerName')
            ->with($this->image)
            ->once()
            ->andReturn($image_name);

        $this->shipperMock
            ->shouldReceive('buildPorts')
            ->with('80:80')
            ->once()
            ->andReturn(' -p 80:80');

        $command = $this->shipperMock->runContainer(
            $this->image,
            $ports
        );

        $this->assertEquals(
            $command,
            'docker run -d --name '.$image_name.' -p '.$ports.' '.$this->image.' '
        );
    }

    public function testRunContainerWithRemoteCommand()
    {
        $image_name = 'nginx_123456789';
        $ports = '80:80';

        $this->shipperMock
            ->shouldReceive('buildContainerName')
            ->with($this->image)
            ->once()
            ->andReturn($image_name);

        $this->shipperMock
            ->shouldReceive('buildPorts')
            ->with('80:80')
            ->once()
            ->andReturn(' -p 80:80');

        $command = $this->shipperMock->runContainer(
            $this->image,
            $ports,
            'start'
        );

        $this->assertEquals(
            $command,
            'docker run -d --name '.$image_name.' -p '.$ports.' '.$this->image.' start'
        );
    }

    public function testRunContainerAsSudo()
    {
        $image_name = 'nginx_123456789';
        $ports = '80:80';

        $this->shipperMock
            ->shouldReceive('buildContainerName')
            ->with($this->image)
            ->once()
            ->andReturn($image_name);

        $this->shipperMock
            ->shouldReceive('buildPorts')
            ->with('80:80')
            ->once()
            ->andReturn(' -p 80:80');

        $command = $this->shipperMock->runContainer(
            $this->image,
            $ports,
            '',
            true
        );

        $this->assertEquals(
            $command,
            'sudo docker run -d --name '.$image_name.' -p '.$ports.' '.$this->image.' '
        );
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
