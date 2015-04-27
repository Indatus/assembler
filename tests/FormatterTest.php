<?php
namespace Indatus\Assembler\Test;

use Indatus\Assembler\Formatter;

class FormatterTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Indatus\Assembler\Formatter $formatter */
    protected $formatter;

    public function setUp()
    {
        $this->formatter = new Formatter(__DIR__ . DIRECTORY_SEPARATOR);
    }

    public function testInstantiation()
    {
        $this->assertInstanceOf('Indatus\Assembler\Formatter', $this->formatter);
    }

    public function testParseSuppliers()
    {
        $this->formatter->format('example');

        $suppliers         = $this->formatter->getSuppliers();
        $expectedSuppliers = [
            'git@github.com:build-engineering/php.git',
            'git@github.com:build-engineering/nginx.git',
            'git@github.com:build-engineering/redis.git',
        ];

        $this->assertEquals($expectedSuppliers, $suppliers);
    }

    public function testFormattedGoodsForSalt()
    {
        $this->formatter->format('example');

        $formattedRawGoods         = $this->formatter->getFormattedGoods();
        $expectedFormattedRawGoods =
            'base:' . PHP_EOL .
            '  \'*\':' . PHP_EOL .
            '    - php' . PHP_EOL .
            '    - nginx' . PHP_EOL .
            '    - redis' . PHP_EOL;

        $this->assertEquals($expectedFormattedRawGoods, $formattedRawGoods);
    }

    public function testFormattedCustomDataForSalt()
    {
        $this->formatter->format('example');

        $formattedCustomData         = $this->formatter->getFormattedCustomData();
        $expectedFormattedCustomData =
            'redis:' . PHP_EOL .
            '  user: root' . PHP_EOL .
            '  password: redis-password' . PHP_EOL;

        $this->assertEquals($expectedFormattedCustomData, $formattedCustomData);
    }

    public function testNoFormattedCustomDataForSalt()
    {
        $this->formatter->format('good-example-no-custom-data');

        $formattedCustomData = $this->formatter->getFormattedCustomData();

        $this->assertNull($formattedCustomData);
    }

    /**
     * @expectedException \Indatus\Assembler\Exceptions\MissingRawGoodsException
     */
    public function testThrowsWhenMissingRawGoods()
    {
        $this->formatter->format('bad-example-raw');
    }

    /**
     * @expectedException \Indatus\Assembler\Exceptions\MissingSuppliersException
     */
    public function testThrowsWhenMissingSuppliers()
    {
        $this->formatter->format('bad-example-suppliers');
    }

    public function testSanitizingRawGoods()
    {
        $this->formatter->format('example');

        $rawGoods          = $this->formatter->getRawGoods();
        $sanitizedRawGoods = $this->formatter->sanitizeRawGoods($rawGoods);
        $expectedRawGoods  = [
            'goods'       => [
                'php',
                'nginx',
                'redis',
            ],
            'custom-data' => [
                'redis' => [
                    'user'     => 'root',
                    'password' => 'redis-password',
                ],
            ],
        ];

        $this->assertEquals($expectedRawGoods, $sanitizedRawGoods);
    }
}
