<?php
namespace Indatus\Assembler\Tasks\Stocker;

use Indatus\Assembler\Tasks\Stocker\StockBaseTask;

class StockTask extends StockBaseTask
{
    /** @var array */
    protected $supplierData;

    /** @var array */
    protected $raw_goods;

    /**
     * @param string $goodsPath
     * @param array  $manifest
     * @param bool   $clean
     * @param bool   $force
     */
    public function __construct(
        $goodsPath,
        array $manifest,
        $clean = false,
        $force = false
    ) {
        $this->supplierData = $manifest['suppliers'];
        $this->rawGoods     = $manifest['raw_goods'];
        parent::__construct($goodsPath, $clean, $force);
    }

    /**
     * Writes the top.sls and clones all of the supplies
     *
     * @param bool $pathExists true if the path exists
     * @param bool $clean      true if you want existing directories to be cleaned
     */
    protected function writeDataToFile($pathExists = false, $clean = false)
    {
        if ($clean && $pathExists) {
            $this->_cleanDir($this->stockPath);
            $this->say('Cleaned off the shelves.');
        }
        if (!$pathExists) {
            $this->_mkdir($this->stockPath);
        }
        $this->say('Grabbing supplies...');
        $currentDir = getcwd();
        chdir($this->stockPath);
        foreach ($this->supplierData as $supplier) {
            $this->taskGitStack()
                ->cloneRepo($supplier)
                ->run();
        }
        chdir($currentDir);
        // Put all of the goods into a top.sls file for us to pass to the Loader
        file_put_contents(
            $this->stockPath . '/top.sls',
            $this->rawGoods
        );

    }

    public function run()
    {
        $pathExists = (bool) file_exists($this->stockPath);

        return $this->_doRun($pathExists);
    }
}
