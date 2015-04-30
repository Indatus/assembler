<?php namespace Indatus\Assembler\Traits;

use Indatus\Assembler\Tasks\ShipTask;

trait ShipperTrait
{
    /**
     * Ship a Docker container to a host
     *
     * @param  string  $image
     * @param  string  $ip
     * @param  string  $ports
     * @param  string  $remote_command
     * @param  string  $remote_user
     * @param  bool    $sudo
     * @return ShipTask
     */
    public function taskShipContainer(
        $image,
        $ip,
        $ports,
        $remote_command,
        $remote_user,
        $sudo
    ) {
        return new ShipTask(
            $image,
            $ip,
            $ports,
            $remote_command,
            $remote_user,
            $sudo
        );
    }
}
