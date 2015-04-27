<?php
namespace Tests\Assembler\Traits;

use Indatus\Assembler\Traits\StockerTrait;

class StockerTraitTest extends \PHPUnit_Framework_TestCase
{

    use StockerTrait;

    public function testStockerTrait()
    {
        $result = $this->taskStockShelf(
            'goods_path',
            [
                'suppliers' => [],
                'raw_goods' => 'something'
            ]
        );
        $this->assertInstanceOf(
            'Indatus\Assembler\Tasks\Stocker\StockTask',
            $result
        );
    }
}
