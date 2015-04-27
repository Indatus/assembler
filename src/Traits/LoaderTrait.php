<?php
namespace Indatus\Assembler\Traits;

use Indatus\Assembler\Tasks\LoadTask;

trait LoaderTrait
{
    public function taskLoadContainer(
        $containerName,
        $shelfPath,
        $customPath = null
    ) {
        return new LoadTask(
            $containerName,
            $shelfPath,
            $customPath
        );
    }
}
