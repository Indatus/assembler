<?php
namespace Indatus\Assembler\Test\Traits;

use Indatus\Assembler\Traits\PackagerTrait;

class PackagerTraitTest extends \PHPUnit_Framework_TestCase
{
    use PackagerTrait;

    public function testPackageTask()
    {
        $container_id = uniqid();
        $repository   = "name/image";
        $result       = $this->taskPackage($container_id, $repository);
        $this->assertInstanceOf(
            'Indatus\Assembler\Tasks\PackageTask',
            $result
        );
    }
}
