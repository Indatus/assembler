<?php
namespace Tests\Assembler\Traits;

use Indatus\Assembler\Traits\LoaderTrait;

class LoaderTraitTest extends \PHPUnit_Framework_TestCase
{
    use LoaderTrait;

    public function testReturnsInstanceOfLoaderTask()
    {
        $result = $this->taskLoadContainer('container_name', '/srv/pillars/');
        $this->assertInstanceOf(
            'Indatus\Assembler\Tasks\LoadTask',
            $result
        );
    }
}
