<?php
namespace Indatus\Assembler\Traits;


use Indatus\Assembler\AdapterFactory;
use Indatus\Assembler\Configuration;
use Indatus\Assembler\Tasks\DestroyTask;

trait DestroyerTrait
{
    public function taskDestroyServer($id)
    {
        return new DestroyTask($id, AdapterFactory::make(new Configuration()));
    }
}