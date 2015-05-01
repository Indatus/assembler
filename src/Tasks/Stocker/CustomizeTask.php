<?php
namespace Indatus\Assembler\Tasks\Stocker;

use Robo\Result;
use Robo\Contract\TaskInterface;
use Symfony\Component\Yaml\Yaml;
use Indatus\Assembler\Tasks\Stocker\StockBaseTask;

class CustomizeTask extends StockBaseTask implements TaskInterface
{
    /** @var string */
    protected $customData;

    /** @var string */
    protected $productionLine;

    /** @var bool */
    protected $clean;

    /** @var bool */
    protected $force;

    /**
     * @param string $customData
     * @param string $customDataPath
     * @param string $productionLine
     * @param bool   $clean          true if you want to empty the dir before stocking
     * @param bool   $force          true if you want to clean dir without prompt
     */
    public function __construct(
        $customData,
        $customDataPath,
        $productionLine,
        $clean = false,
        $force = false
    ) {
        parent::__construct($customDataPath, $clean, $force);
        $this->customData     = $customData;
        $this->productionLine = $productionLine;
    }

    /**
     * Writes the custom data to pillars and adds a top.sls
     *
     * @param bool $pathExists
     * @param bool $clean true if you want to empty existing directories
     */
    protected function writeDataToFile($pathExists = false, $clean = false)
    {
        if ($clean && $pathExists) {
            $this->_cleanDir($this->stockPath);
        }
        if (!$pathExists) {
            $this->_mkdir($this->stockPath);
        }
        file_put_contents(
            $this->stockPath .
            "/" . $this->productionLine . '.sls',
            $this->customData
        );
        $top                = [
            'base' => [
                '*' => []
            ]
        ];
        $top['base']['*'][] = $this->productionLine;
        file_put_contents(
            $this->stockPath .
            '/top.sls',
            Yaml::dump($top, 10, 2)
        );
    }

    /**
     * Writes the pillar data
     *
     * @return Result
     */
    public function run()
    {
        $pathExists = (bool) file_exists($this->stockPath);

        return parent::_doRun($pathExists);
    }
}
