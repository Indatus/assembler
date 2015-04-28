<?php
namespace Indatus\Assembler\Test;

use Indatus\Assembler\AdapterFactory;
use Mockery as m;
class AdapterFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testMakeWhenProviderIsDigitalOcean()
    {
        $expectedProvider = "digitalocean";

        /**
         * @var $config \Indatus\Assembler\Configuration|\Mockery\MockInterface
         */
        $config = m::mock("Indatus\Assembler\Configuration");
        $config->shouldReceive('provider')
            ->once()
            ->andReturn($expectedProvider);
        $config->shouldReceive(
            'apiToken'
        )->andReturn(uniqid());

        $adapter = AdapterFactory::make($config);
        $this->assertInstanceOf(
            'Indatus\Assembler\Adapters\DigitalOceanAdapter',
            $adapter
        );
    }

    public function testMakeWhenProviderIsNotValid()
    {
        $this->setExpectedException("Indatus\Assembler\Exceptions\InvalidProviderException");
        $provider = uniqid();
        $config = m::mock("Indatus\Assembler\Configuration");
        $config->shouldReceive('provider')
            ->once()
            ->andReturn($provider);
        AdapterFactory::make($config);
    }
}