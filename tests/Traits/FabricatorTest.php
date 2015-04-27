<?php
namespace Indatus\Assembler\Test;

use Indatus\Assembler\Traits\FabricatorTrait;

class FabricatorTest extends \PHPUnit_Framework_TestCase
{
    use FabricatorTrait;

    public function testTraitReturnsInstanceOfFabricate()
    {
        $expectedPath = 'some/path/to/some/dockerfile';
        $tag          = "4.0";
        $result       = $this->taskFabricateContainer($expectedPath, $tag);
        $this->assertInstanceOf(
            'Indatus\Assembler\Tasks\FabricateTask',
            $result
        );
    }
}
