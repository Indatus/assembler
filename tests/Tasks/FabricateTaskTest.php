<?php
namespace Indatus\Assembler\Test\Tasks;

use Mockery as m;

class FabricateTaskTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Indatus\Assembler\Tasks\FabricateTask|\Mockery\MockInterface */
    protected $fabricator;

    /** @var string */
    protected $pathToDockerfile;

    public function setUp()
    {
        $this->pathToDockerfile = "path/to/dockerfile";
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * Tests that docker build task is called correctly
     */
    public function testItCallsDockerBuildWithPathToDockerFileAndTag()
    {
        $tag              = "some_tag";
        $mockReturn       = m::mock('mock_return');
        $this->fabricator = m::mock(
            'Indatus\Assembler\Tasks\FabricateTask',
            [$this->pathToDockerfile, $tag]
        )->makePartial()->shouldAllowMockingProtectedMethods();
        $this->fabricator->shouldReceive('taskDockerBuild')
            ->once()
            ->with($this->pathToDockerfile)
            ->andReturn($mockReturn);
        $mockReturn->shouldReceive('tag')
            ->once()
            ->with($tag)
            ->andReturn($mockReturn);
        $mockReturn->shouldReceive('run')
            ->once();
        $this->fabricator->run();
    }
}
