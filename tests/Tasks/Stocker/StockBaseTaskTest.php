<?php
namespace Indatus\Assembler\Test\Tasks\Stocker;

use ReflectionClass;
use Mockery as m;

class StockBaseTaskTest extends \PHPUnit_Framework_TestCase
{
    protected $tmpDir;

    protected $protectedDoRun;

    public function setUp()
    {
        $this->tmpDir = realpath("./") . "/tmp";
        $this->_unProtect('Indatus\Assembler\Tasks\Stocker\StockBaseTask', '_doRun');
    }

    public function tearDown()
    {
        \Mockery::close();
    }

    protected function _unProtect($class, $methodName)
    {
        $reflectionClass      = new ReflectionClass($class);
        $this->protectedDoRun = $reflectionClass->getMethod($methodName);
        $this->protectedDoRun->setAccessible(true);
    }

    public function testDoRunWhenPathDoesNotExist()
    {
        $pathExists = false;
        $stockBase  = m::mock(
            'Indatus\Assembler\Tasks\Stocker\StockBaseTask',
            [
                $this->tmpDir
            ]
        )->makePartial()->shouldAllowMockingProtectedMethods();
        $stockBase->shouldReceive("writeDataToFile")
            ->once()
            ->with($pathExists, false);
        $result = $stockBase->_doRun($pathExists);
        $this->assertEquals($result->getExitCode(), 0);
    }

    public function testRunWhenPathExistsCleanIsTrueAndTheForceFlagIsSet()
    {
        $clean      = true;
        $pathExists = true;
        $stockBase  = m::mock(
            'Indatus\Assembler\Tasks\Stocker\StockBaseTask',
            [
                $this->tmpDir,
                $clean,
                true
            ]
        )->makePartial()->shouldAllowMockingProtectedMethods();
        $stockBase->shouldNotHaveReceived("askDefault");
        $stockBase->shouldReceive("writeDataToFile")
            ->once()
            ->with($pathExists, $clean);
        $result = $stockBase->_doRun($pathExists);
        $this->assertEquals($result->getExitCode(), 0);
    }

    public function testRunWhenPathExistsCleanIsTrueAndUserApproves()
    {
        $pathExists = true;
        $clean      = true;
        $stockBase  = m::mock(
            'Indatus\Assembler\Tasks\Stocker\StockBaseTask',
            [
                $this->tmpDir,
                $clean
            ]
        )->makePartial()->shouldAllowMockingProtectedMethods();
        $stockBase->shouldReceive("askDefault")
            ->once()
            ->with(
                "The path at: $this->tmpDir already exists " .
                "and will be emptied are you sure you wish to proceed?",
                "y"
            )->andReturn("y");
        $stockBase->shouldReceive("writeDataToFile")
            ->once()
            ->with($pathExists, $clean);
        $result = $stockBase->_doRun($pathExists);
        $this->assertEquals($result->getExitCode(), 0);
    }

    public function testRunWhenPathExistsCleanIsTrueAndUserDenies()
    {
        $pathExists = true;
        $stockBase  = m::mock(
            'Indatus\Assembler\Tasks\Stocker\StockBaseTask',
            [
                $this->tmpDir,
                true
            ]
        )->makePartial()->shouldAllowMockingProtectedMethods();
        $stockBase->shouldReceive("askDefault")
            ->once()
            ->with(
                "The path at: $this->tmpDir already exists " .
                "and will be emptied are you sure you wish to proceed?",
                "y"
            )->andReturn("N");
        $actualResult = $stockBase->_doRun($pathExists);
        $this->assertEquals(
            $actualResult->getExitCode(),
            1
        );
        $this->assertEquals(
            $actualResult->getMessage(),
            "user terminated"
        );
    }
}
