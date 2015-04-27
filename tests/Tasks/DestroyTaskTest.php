<?php
/**
 * Created by PhpStorm.
 * User: tbell
 * Date: 4/27/15
 * Time: 1:54 PM
 */

namespace Indatus\Assembler\Test\Tasks;

use Indatus\Assembler\Tasks\DestroyTask;
use Mockery as m;
class DestroyTaskTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testRun()
    {
        /**
         * @var $cloudAdapter \Indatus\Assembler\Contracts\CloudAdapterInterface|\Mockery\MockInterface
         */
        $cloudAdapter = m::mock(
            "Indatus\Assembler\Contracts\CloudAdapterInterface"
        );
        $id = uniqid();
        $cloudAdapter->shouldReceive('drop')
            ->once()
            ->with($id);
        $taskUnderTest = new DestroyTask($id, $cloudAdapter);
        $result = $taskUnderTest->run();
        $this->assertEquals($result->getExitCode(), 0);
    }
}
