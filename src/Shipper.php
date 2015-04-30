<?php namespace Indatus\Assembler;

class Shipper
{
    /**
     * Build the remote command for running a container
     *
     * @param string $image  Image to run
     * @param string $ports  Ports to open on the host
     * @param string $remote_command  Command to run after container loaded
     * @param bool   $sudo  Should command be run as sudo
     * @return string
     */
    public function runContainer(
        $image,
        $ports,
        $remote_command = '',
        $sudo  = false
    )
    {
        // Run container detached
        $command = 'docker run -d';

        // Name the container
        $command = $command.' --name '.$this->buildContainerName($image);

        // Assign ports
        $command = $command.$this->buildPorts($ports);

        // Image the container should load
        $command = $command.' '.$image;

        // Command(s) to run after load
        $command = $command.' '.$remote_command;

        // Should we run as sudo
        if ($sudo) {
            $command = 'sudo '.$command;
        }

        return $command;
    }

    /**
     * Build the command for pulling an image
     *
     * @param  string  $image  Image to pull
     * @param  bool    $sudo   Run command as `sudo`
     * @return string
     */
    public function pullImage($image, $sudo = false)
    {
        $command = 'docker pull '.$image;

        // Should we run as sudo
        if ($sudo) {
            $command = 'sudo '.$command;
        }

        return $command;
    }

    /**
     * Build the list of ports to be mapped to the Docker host
     *
     * @param  string  $ports  Comma seperated list of ports
     * @return string
     */
    public function buildPorts($ports)
    {
        $ports = explode(',', $ports);
        $portParam = '';

        foreach ($ports as $port) {
            $portParam .= ' -p '.trim($port);
        }

        return $portParam;
    }

    /**
     * Build the name of the Docker container based on the image name and unix timestamp
     * according to Docker naming requirements.
     *
     * @param   string  $image  Name of the container image
     * @return  mixed
     */
    public function buildContainerName($image)
    {
        $image = $image.'_'.time();

        return preg_replace('/([^a-zA-Z0-9_.-])/', '_', $image);
    }
}
