<?php
namespace Indatus\Assembler\Test;

use Indatus\Assembler\Traits\CustomizerTrait;

class CustomizerTraitTest extends \PHPUnit_Framework_TestCase
{
    use CustomizerTrait;

    public function testTaskCustomizeDataReturnsTask()
    {
        $result = $this->taskCustomizeData(
            [],
            "/path/to/customdata",
            "prod_line",
            false
        );
        $this->assertInstanceOf(
            'Indatus\Assembler\Tasks\Stocker\CustomizeTask',
            $result
        );
    }
}
