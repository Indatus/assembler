<?php
namespace Indatus\Assembler\Traits;

use Indatus\Assembler\Tasks\FabricateTask;

trait FabricatorTrait
{
    public function taskFabricateContainer($pathToDockerfile, $tag)
    {
        return new FabricateTask($pathToDockerfile, $tag);
    }
}
