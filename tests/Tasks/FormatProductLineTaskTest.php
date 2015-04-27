<?php
namespace Indatus\Assembler\Test\Tasks;

use Indatus\Assembler\Tasks\FormatProductLineTask;
use Mockery as m;

class FormatProductLineTaskTest extends \PHPUnit_Framework_TestCase
{
    /** @var string */
    protected $productLine;

    public function setUp()
    {
        $this->productLine = 'example';
    }

    public function tearDown()
    {
        m::close();
    }

    public function testItFormatsProductLineForSalt()
    {
        $formatter = m::mock('Indatus\Assembler\Formatter');
        $formatter->shouldReceive('format')
            ->once()
            ->with($this->productLine);
        $formatter->shouldReceive('getFormattedGoods')
            ->once()
            ->withNoArgs();
        $formatter->shouldReceive('getFormattedCustomData')
            ->once()
            ->withNoArgs();
        $formatter->shouldReceive('getSuppliers')
            ->once()
            ->withNoArgs();
        $formatProductLineTask = new FormatProductLineTask(
            $this->productLine,
            $formatter
        );
        $formatProductLineTask
            ->forSalt()
            ->run();
    }
}
