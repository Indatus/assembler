<?php namespace Indatus\Assembler\Traits;

use Indatus\Assembler\Tasks\ShipTask;

trait ShipperTrait
{
    /**
     * @param $image
     * @param $ip
     * @param $ports
     * @param $remote_command
     * @param $remote_user
     * @param $sudo
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
