<?php
namespace Indatus\Assembler\Traits;

use Indatus\Assembler\Tasks\PackageTask;

trait PackagerTrait
{
    public function taskPackage(
        $containerId,
        $repository,
        $push = false,
        $username = null,
        $password = null,
        $email = null
    ) {
        return new PackageTask(
            $containerId,
            $repository,
            $push,
            $username,
            $password,
            $email
        );
    }
}
