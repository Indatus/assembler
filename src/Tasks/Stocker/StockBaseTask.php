<?php
namespace Indatus\Assembler\Tasks\Stocker;

use Robo\Tasks;
use Robo\Result;
use Robo\Contract\TaskInterface;

abstract class StockBaseTask extends Tasks implements TaskInterface
{
    /** @var string */
    protected $stockPath;

    /** @var bool */
    protected $clean;

    /** @var bool */
    protected $force;

    /**
     * @param string $stockPath
     * @param bool   $clean
     * @param bool   $force
     */
    public function __construct($stockPath, $clean = false, $force = false)
    {
        $this->stockPath = $stockPath;
        $this->clean     = $clean;
        $this->force     = $force;
    }

    protected abstract function writeDataToFile($pathExists, $clean);

    /**
     * Handles all of the force logic and presenting options to the user
     *
     * @param bool $pathExists true if the path you are writing to exists
     *
     * @return Result
     */
    protected function _doRun($pathExists)
    {
        if ($pathExists) {
            if ($this->clean) {
                if ($this->force) {
                    $this->writeDataToFile($pathExists, $this->clean);

                    return new Result($this, 0);
                }
                $result = strtolower(
                    $this->askDefault(
                        "The path at: $this->stockPath already exists " .
                        "and will be emptied are you sure you wish to proceed?",
                        "y"
                    )
                );
                switch ($result) {
                    case "n":
                        return new Result($this, 1, "user terminated");
                        break;
                    case "y":
                        $this->writeDataToFile($pathExists, $this->clean);

                        return new Result($this, 0);
                }
            }
        }
        $this->writeDataToFile($pathExists, $this->clean);

        return new Result($this, 0);
    }
}
