<?php
namespace Indatus\Assembler\Tasks;

use Robo\Contract\TaskInterface;
use Robo\Task\Docker\loadTasks as Docker;

class LoadTask implements TaskInterface
{
    use Docker;

    /** @var string */
    protected $containerName;

    /** @var string */
    protected $shelfPath;

    /** @var string|null */
    protected $customPath;

    /**
     * @param string      $containerName
     * @param string      $shelfPath
     * @param string|null $customPath
     */
    public function __construct($containerName, $shelfPath, $customPath = null)
    {
        $this->containerName = $containerName;
        $this->shelfPath     = $shelfPath;
        $this->customPath    = $customPath;
    }

    public function run()
    {
        if (isset($this->customPath)) {
            return $this->taskDockerRun($this->containerName)
                ->volume($this->shelfPath, "/srv/salt/")
                ->volume($this->customPath, "/srv/pillar/")
                ->exec('salt-call --local state.highstate')
                ->run();
        }

        return $this->taskDockerRun($this->containerName)
            ->volume($this->shelfPath, "/srv/salt/")
            ->exec('salt-call --local state.highstate')
            ->run();
    }
}
