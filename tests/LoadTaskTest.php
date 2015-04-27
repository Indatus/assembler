<?php
namespace Indatus\Assembler\Test;

use Mockery as m;

class LoadTaskTest extends \PHPUnit_Framework_TestCase
{
    public function testRunCallsVolumeTwice()
    {
        $mockReturn    = m::mock(
            'mock_return'
        );
        $containerName = "some_container";
        $shelfPath     = "/srv/root";
        $customPath    = "pillars/";
        $loader        = m::mock(
            'Indatus\Assembler\Tasks\LoadTask',
            [$containerName, $shelfPath, $customPath]
        )->makePartial()->shouldAllowMockingProtectedMethods();
        $loader->shouldReceive('taskDockerRun')
            ->with($containerName)
            ->once()
            ->andReturn($mockReturn);
        $mockReturn->shouldReceive('volume')
            ->with($shelfPath, '/srv/salt/')
            ->once()
            ->andReturn($mockReturn);
        $mockReturn->shouldReceive('volume')
            ->with($customPath, '/srv/pillar/')
            ->once()
            ->andReturn($mockReturn);
        $mockReturn->shouldReceive('exec')
            ->once()
            ->with('salt-call --local state.highstate')
            ->andReturn($mockReturn);
        $mockReturn->shouldReceive('run')
            ->once()
            ->andReturn($mockReturn);
        $loader->run();
    }

    public function testRunCallsRun()
    {
        $mockReturn    = m::mock(
            'mock_return'
        );
        $containerName = "some_container";
        $shelfPath     = "/srv/root";
        $loader        = m::mock(
            'Indatus\Assembler\Tasks\LoadTask',
            [$containerName, $shelfPath]
        )->makePartial()->shouldAllowMockingProtectedMethods();
        $loader->shouldReceive('taskDockerRun')
            ->with($containerName)
            ->once()
            ->andReturn($mockReturn);
        $mockReturn->shouldReceive('volume')
            ->with($shelfPath, '/srv/salt/')
            ->once()
            ->andReturn($mockReturn);
        $mockReturn->shouldReceive('exec')
            ->once()
            ->with('salt-call --local state.highstate')
            ->andReturn($mockReturn);
        $mockReturn->shouldReceive('run')
            ->once()
            ->andReturn($mockReturn);
        $loader->run();
    }
}
