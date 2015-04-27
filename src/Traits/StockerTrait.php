<?php
namespace Indatus\Assembler\Traits;

use Indatus\Assembler\Tasks\Stocker\StockTask;

trait StockerTrait
{
    /**
     * @param string $goodsPath
     * @param array  $manifest
     * @param bool   $clean
     * @param bool   $force
     * @return StockTask
     */
    public function taskStockShelf(
        $goodsPath,
        $manifest,
        $clean = false,
        $force = false
    ) {
        return new StockTask(
            $goodsPath,
            $manifest,
            $clean,
            $force
        );
    }
}
