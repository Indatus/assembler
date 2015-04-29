<?php
namespace Indatus\Assembler\Tasks;

use Indatus\Assembler\Shipper;
use Robo\Contract\TaskInterface;
use Robo\Tasks;

class ShipTask extends Tasks implements TaskInterface
{
    /** @var  string  Docker image to be pulled and run */
    protected $image;

    /** @var  string  Docker host IP address */
    protected $ip;

    /** @var  string  Ports to open on the Docker host */
    protected $ports;

    /** @var  string  Command to run after container is started */
    protected $remote_command;

    /** @var  string  Remote user to execute tasks */
    protected $remote_user;

    /** @var  bool  Run command as sudo */
    protected $sudo;

    /** @var \Assembler\Shipper */
    protected $shipper;

    /**
     * @param string $image
     * @param string $ip
     * @param array  $ports
     * @param string $remote_command
     * @param bool   $sudo
     */
    public function __construct(
        $image,
        $ip,
        $ports,
        $remote_command,
        $remote_user,
        $sudo
    ) {
        $this->image = $image;
        $this->ip = $ip;
        $this->ports = $ports;
        $this->remote_command = $remote_command;
        $this->remote_user = $remote_user;
        $this->sudo = $sudo;

        $this->shipper = new Shipper();
    }

    /**
     * @return \Robo\Result
     */
    public function run()
    {
        $var = $this->shipper->runContainer(
            $this->image,
            $this->ports,
            $this->remote_command,
            $this->sudo);

        return $this->taskSshExec($this->ip, $this->remote_user)
            //->exec('sudo docker run -d --name leftyhitchens_mysql_5.2_1430327937 -p 3306:3306 leftyhitchens/mysql:5.2 mysqld_safe')
            ->exec($this->shipper->pullImage($this->image))
            ->exec($var)
            ->exec('history')
            ->run();
    }
}
