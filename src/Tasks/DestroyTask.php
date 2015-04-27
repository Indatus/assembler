<?php


namespace Indatus\Assembler\Tasks;


use Indatus\Assembler\Contracts\CloudAdapterInterface;
use Robo\Contract\TaskInterface;
use Robo\Result;

class DestroyTask implements TaskInterface
{
    protected $cloudAdapter;

    protected $id;

    public function __construct($id, CloudAdapterInterface $cloudAdapter)
    {
        $this->id = $id;
        $this->cloudAdapter = $cloudAdapter;
    }

    public function run()
    {
        $this->cloudAdapter->drop($this->id);
        return new Result($this,0);
    }
}