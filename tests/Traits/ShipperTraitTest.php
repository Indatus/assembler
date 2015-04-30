<?php
namespace Tests\Assembler\Traits;

use Indatus\Assembler\Traits\ShipperTrait;

class ShipperTraitTest extends \PHPUnit_Framework_TestCase
{

    use ShipperTrait;

    public function testShipperTrait()
    {
        $result = $this->taskShipContainer(
            'nginx',
            '192.168.1.100',
            '80:80',
            'start',
            'root',
            true
        );
        $this->assertInstanceOf(
            'Indatus\Assembler\Tasks\ShipTask',
            $result
        );
    }
}
