<?php
namespace Indatus\Assembler\Traits;

use Indatus\Assembler\Tasks\Stocker\CustomizeTask;

trait CustomizerTrait
{
    /**
     * @param array  $customData
     * @param string $customDataPath
     * @param string $productionLine
     * @param bool   $clean
     * @param bool   $force
     *
     * @return \Indatus\Assembler\Tasks\Stocker\CustomizeTask
     */
    public function taskCustomizeData(
        $customData,
        $customDataPath,
        $productionLine,
        $clean = false,
        $force = false
    ) {
        return new CustomizeTask(
            $customData,
            $customDataPath,
            $productionLine,
            $clean,
            $force
        );
    }
}
