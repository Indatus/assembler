<?php
namespace Indatus\Assembler;

use Indatus\Assembler\Exceptions\MissingRawGoodsException;
use Indatus\Assembler\Exceptions\MissingSuppliersException;
use Symfony\Component\Yaml\Yaml;

class Formatter
{
    /** @var array Custom data formatted for use by a Loader */
    protected $formattedCustomData;

    /** @var array Custom goods formatted for use by a Loader */
    protected $formattedGoods;

    /** @var string Path to the product line to be formatted */
    protected $productLinePath;

    /** @var array Raw goods to be formatted */
    protected $rawGoods;

    /** @var array Sanitized raw goods containing both goods and custom data */
    protected $sanitizedGoods;

    /** @var array Listing of suppliers */
    protected $suppliers;

    /**
     * @param string $productLinePath
     */
    public function __construct($productLinePath = 'product_lines/')
    {
        $this->productLinePath = realpath($productLinePath);
    }

    /**
     * @param $product
     * @throws MissingRawGoodsException
     * @throws MissingSuppliersException
     */
    public function format($product)
    {
        $productLine = Yaml::parse(file_get_contents($this->productLinePath . "/" . $product . '.yaml'));
        if (array_key_exists('raw_goods', $productLine)) {
            $this->rawGoods = $productLine['raw_goods'];
        } else {
            throw new MissingRawGoodsException('Missing raw_goods within product_line "' . $product . '"');
        }

        if (array_key_exists('suppliers', $productLine)) {
            $this->suppliers = $productLine['suppliers'];
        } else {
            throw new MissingSuppliersException('Missing suppliers within product_line "' . $product . '"');
        }

        $this->sanitizedGoods      = $this->sanitizeRawGoods($this->getRawGoods());
        $this->formattedGoods      = $this->formatGoodsForSalt($this->getSanitizedGoods());
        $this->formattedCustomData = $this->formatCustomDataForSalt($this->getSanitizedCustomData());
    }

    /**
     * @param $rawGoods
     * @return array
     */
    public function sanitizeRawGoods($rawGoods)
    {
        $sanitizedGoods = [
            'goods'       => null,
            'custom-data' => null,
        ];
        foreach ($rawGoods as $rawGood) {
            // Convert non-array goods to arrays so that we can grab a key
            if (!is_array($rawGood)) {
                $rawGood = [$rawGood => null];
            } else {
                // We're using all of these key($foo) functions to prevent int indexes
                $rawGoodName = key($rawGood);
                foreach ($rawGood[$rawGoodName] as $customData) {
                    $customDataName                                               = key($customData);
                    $sanitizedGoods['custom-data'][$rawGoodName][$customDataName] = $customData[$customDataName];
                }
            }
            $sanitizedGoods['goods'][] = key($rawGood);
        }

        return $sanitizedGoods;
    }

    /**
     * @param $sanitizedGoods
     * @return string
     */
    protected function formatGoodsForSalt($sanitizedGoods)
    {
        $top = [
            'base' => [
                '*' => []
            ]
        ];
        $top['base']['*'] = $sanitizedGoods;

        // Format as far as 10 key value pairs with an indent of 2 spaces
        return Yaml::dump($top, 10, 2);
    }

    /**
     * @param $sanitizedCustomData
     * @return string
     */
    protected function formatCustomDataForSalt($sanitizedCustomData)
    {
        if (is_null($sanitizedCustomData)) {
            return null;
        }

        return Yaml::dump($sanitizedCustomData, 10, 2);
    }

    /**
     * @return array
     */
    public function getRawGoods()
    {
        return $this->rawGoods;
    }

    /**
     * @return mixed
     */
    public function getSanitizedGoods()
    {
        return $this->sanitizedGoods['goods'];
    }

    /**
     * @return mixed
     */
    public function getSanitizedCustomData()
    {
        return $this->sanitizedGoods['custom-data'];
    }

    /**
     * @return array
     */
    public function getFormattedGoods()
    {
        return $this->formattedGoods;
    }

    /**
     * @return array
     */
    public function getFormattedCustomData()
    {
        return $this->formattedCustomData;
    }

    /**
     * @return array
     */
    public function getSuppliers()
    {
        return $this->suppliers;
    }
}
