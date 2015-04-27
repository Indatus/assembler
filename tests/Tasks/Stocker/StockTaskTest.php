<?php
namespace Indatus\Assembler\Test\Tasks\Stocker;

use Indatus\Assembler\Tasks\Stocker;
use Mockery as m;
use ReflectionClass;
use Symfony\Component\Filesystem\Filesystem;

class StockTaskTest extends \PHPUnit_Framework_TestCase
{
    /** @var string */
    protected $tmpDir;

    /** @var \Symfony\Component\Filesystem\Filesystem */
    protected $filesystem;

    public function setUp()
    {
        $this->tmpDir     = realpath('./') . '/tmp';
        $this->filesystem = new Filesystem();
        $this->_unProtect('Indatus\Assembler\Tasks\Stocker\StockTask', 'writeDataToFile');
    }

    public function tearDown()
    {
        if (file_exists($this->tmpDir)) {
            $this->filesystem->remove($this->tmpDir);
        }
        m::close();
    }

    public function testRun()
    {
        $expectedReturn = "return";

        $stockTask = m::mock(
            'Indatus\Assembler\Tasks\Stocker\StockTask',
            [
                $this->tmpDir,
                [
                    "suppliers" => "supplier data",
                    "raw_goods" => "raw goods"
                ]
            ]
        )->makePartial()->shouldAllowMockingProtectedMethods();

        $stockTask->shouldReceive('_doRun')
            ->once()
            ->with(false)
            ->andReturn($expectedReturn);

        $actualResult = $stockTask->run();
        $this->assertEquals($actualResult, $expectedReturn);
    }

    protected function _unProtect($class, $methodName)
    {
        $reflectionClass = new ReflectionClass($class);
        $method          = $reflectionClass->getMethod($methodName);
        $method->setAccessible(true);
    }

    public function testWriteDataWhenPathDoesNotExist()
    {
        $pathExists = false;
        $rawGoods   = 'raw goods';
        $stockTask  = m::mock(
            'Indatus\Assembler\Tasks\Stocker\StockTask',
            [
                $this->tmpDir,
                [
                    "suppliers" => [],
                    "raw_goods" => $rawGoods
                ]
            ]
        )->makePartial()->shouldAllowMockingProtectedMethods();
        $stockTask->shouldReceive('say')
            ->once()
            ->with('Grabbing supplies...');
        $stockTask->writeDataToFile($pathExists, false);
        $topFile = $this->tmpDir . '/top.sls';
        $this->assertTrue((bool) file_exists($topFile));
        $this->assertEquals(file_get_contents($topFile), $rawGoods);
    }

    public function testWriteDataWhenPathExistsAndCleanIsTrue()
    {
        $this->filesystem->mkdir($this->tmpDir);
        $pathExists   = true;
        $clean        = true;
        $supplierData = 'supplier_data';
        $rawGoods     = 'some raw goods';
        $mockGitTask  = m::mock();
        $stockPath    = $this->tmpDir;
        $stockTask    = m::mock(
            'Indatus\Assembler\Tasks\Stocker\StockTask',
            [
                $this->tmpDir,
                [
                    'suppliers' => [$supplierData],
                    'raw_goods' => $rawGoods
                ]
            ]
        )->makePartial()->shouldAllowMockingProtectedMethods();
        $stockTask->shouldReceive('_cleanDir')
            ->once()
            ->with($stockPath);
        $stockTask->shouldReceive('taskGitStack')
            ->once()
            ->andReturn($mockGitTask);
        $mockGitTask->shouldReceive('cloneRepo')
            ->once()
            ->with($supplierData)
            ->andReturn($mockGitTask);
        $mockGitTask->shouldReceive('run')
            ->once();

        $stockTask->shouldReceive('say')
            ->once()
            ->with('Cleaned off the shelves.');
        $stockTask->shouldReceive('say')
            ->once()
            ->with('Grabbing supplies...');
        $stockTask->writeDataToFile($pathExists, $clean);
        $topFile = $this->tmpDir . '/top.sls';
        $this->assertTrue((bool) file_exists($topFile));
        $this->assertEquals(file_get_contents($topFile), $rawGoods);
    }
}
