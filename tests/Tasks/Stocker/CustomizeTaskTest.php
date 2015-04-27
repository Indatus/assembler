<?php
namespace Indatus\Assembler\Test\Tasks\Stocker;

use Mockery as m;
use ReflectionClass;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class CustomizeTaskTest extends \PHPUnit_Framework_TestCase
{
    /** @var string */
    protected $tmpDir;

    /** @var \Symfony\Component\Filesystem\Filesystem */
    protected $filesystem;

    /** @var string */
    protected $productionLine;

    public function setUp()
    {
        $this->tmpDir = realpath("./") . "/tmp";
        $this->productionLine = "my_prod_line";
        $this->filesystem = new Filesystem();
        $this->_unProtect('Indatus\Assembler\Tasks\Stocker\CustomizeTask', 'writeDataToFile');
    }

    protected function _unProtect($class, $method_name)
    {
        $reflection_class = new ReflectionClass($class);
        $method = $reflection_class->getMethod($method_name);
        $method->setAccessible(true);
    }

    public function tearDown()
    {
        if (file_exists($this->tmpDir)) {
            $this->filesystem->remove($this->tmpDir);
        }
        m::close();
    }

    public function testRunWhenPathDoesNotExist()
    {
        $pathExists = false;
        $customEata = ['user' => 'value'];
        $customPath = $this->tmpDir;
        /**
         * @var $customizeTask \Mockery\Mock|\Indatus\Assembler\Tasks\CustomizeTask
         */
        $customizeTask = m::mock(
            'Indatus\Assembler\Tasks\Stocker\CustomizeTask',
            [
                $customEata,
                $customPath,
                $this->productionLine,
            ]
        )->makePartial()->shouldAllowMockingProtectedMethods();
        $customizeTask->shouldNotHaveReceived("_cleanDir");
        /** @var $actual_result \Robo\Result */
        $customizeTask->writeDataToFile($pathExists);
        $productionLineData =  $this->tmpDir . "/" . $this->productionLine .  ".sls";
        $this->assertTrue(
            file_exists(
                $productionLineData
            )
        );
        $this->assertEquals(
            $customEata['user'],
            file_get_contents($productionLineData)
        );
        $topFile = $this->tmpDir . "/top.sls";
        $this->assertTrue(
            file_exists($topFile)
        );
        $top = [
            'base' => [
                '*' => []
            ]
        ];
        $top['base']['*'][] = $this->productionLine;
        $expectedTop = Yaml::dump($top, 10, 2);
        $actualTop = file_get_contents($topFile);
        $this->assertEquals(
            $expectedTop,
            $actualTop
        );
    }


    /**
     * Tests the run method when a path is specified that already exists
     * and the clean flag is true
     * and the user decides to empty the directory
     */
    public function testRunWhenPathExistsCleanIsTrue()
    {
        $path_exists = true;
        $this->filesystem->mkdir($this->tmpDir);
        $custom_data = ['user' => 'value'];
        $custom_path = $this->tmpDir;
        $clean_dir   = true;
        $customize_task = m::mock(
            'Indatus\Assembler\Tasks\Stocker\CustomizeTask',
            [
                $custom_data,
                $custom_path,
                $this->productionLine,
                $clean_dir
            ]
        )->makePartial()->shouldAllowMockingProtectedMethods();
        $customize_task->shouldReceive("_cleanDir")
            ->once()
            ->with($this->tmpDir);
        /** @var $actual_result \Robo\Result */
        $customize_task->writeDataToFile($path_exists, $clean_dir);
        $production_line_data =  $this->tmpDir . "/" . $this->productionLine .  ".sls";
        $this->assertTrue(
            file_exists(
                $production_line_data
            )
        );
        $this->assertEquals(
            $custom_data['user'],
            file_get_contents($production_line_data)
        );
        $top_file = $this->tmpDir . "/top.sls";
        $this->assertTrue(
            file_exists($top_file)
        );
        $top = [
            'base' => [
                '*' => []
            ]
        ];
        $top['base']['*'][] = $this->productionLine;
        $expected_top = Yaml::dump($top, 10, 2);
        $actual_top = file_get_contents($top_file);
        $this->assertEquals(
            $expected_top,
            $actual_top
        );
    }
}
