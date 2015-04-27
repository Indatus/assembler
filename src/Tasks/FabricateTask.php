<?php
namespace Indatus\Assembler\Tasks;

use Robo\Tasks;
use Robo\Contract\TaskInterface;
use Robo\Task\Docker\loadTasks as Docker;

class FabricateTask extends Tasks implements TaskInterface
{
    use Docker;

    /** @var string */
    protected $pathToDockerfile;

    /** @var string */
    protected $tag;

    /**
     * @param string $pathToDockerfile
     * @param string $tag
     */
    public function __construct($pathToDockerfile, $tag)
    {
        $this->pathToDockerfile = $pathToDockerfile;
        $this->tag              = $tag;
    }

    /**
     * Runs the fabrication task
     *
     * @return \Robo\Result
     */
    public function run()
    {
        return $this->taskDockerBuild($this->pathToDockerfile)
            ->tag($this->tag)
            ->run();
    }
}
