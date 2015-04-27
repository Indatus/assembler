<?php
namespace Indatus\Assembler\Tasks;

use Indatus\Assembler\Formatter;
use Robo\Contract\TaskInterface;
use Robo\Result;

class FormatProductLineTask implements TaskInterface
{

    /** @var \Indatus\Assembler\Formatter|null */
    protected $formatter;

    /** @var string */
    protected $productLine;

    /** @var string */
    protected $rawGoodsPath;

    /** @var string */
    protected $suppliersPath;

    /**
     * @param string                            $productLine
     * @param \Indatus\Assembler\Formatter|null $formatter
     */
    public function __construct($productLine, Formatter $formatter = null)
    {
        $this->formatter   = is_null($formatter) ? new Formatter : $formatter;
        $this->productLine = $productLine;
    }

    public function forSalt()
    {
        $this->formatter->format($this->productLine);

        return $this;
    }

    /**
     * @return \Robo\Result
     */
    function run()
    {
        $formattedData                = [];
        $formattedData['raw_goods']   = $this->formatter->getFormattedGoods();
        $formattedData['custom-data'] = $this->formatter->getFormattedCustomData();
        $formattedData['suppliers']   = $this->formatter->getSuppliers();

        return new Result($this, 0, '', $formattedData);
    }
}
