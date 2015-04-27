<?php
namespace Indatus\Assembler\Tasks;

use Robo\Contract\TaskInterface;
use Robo\Tasks;

class PackageTask extends Tasks implements TaskInterface
{
    /**
     * @var string the id of the container to commit and push
     */
    protected $containerId;
    /**
     * @var string the repository being pushed to
     */
    protected $repository;
    /**
     * @var bool true if we should push the container to the repository
     */
    protected $push;
    /**
     * @var string the username for the docker registry
     */
    protected $userName;
    /**
     * @var string the password for the docker registry
     */
    protected $password;

    /**
     * @var string the email for the docker registry
     */
    protected $email;

    /**
     * @param string      $containerId
     * @param string      $repository
     * @param bool        $push
     * @param string|null $username
     * @param string|null $password
     * @param string|null $email
     */
    public function __construct(
        $containerId,
        $repository,
        $push = false,
        $username = null,
        $password = null,
        $email = null
    ) {
        $this->containerId = $containerId;
        $this->repository  = $repository;
        $this->push        = $push;
        $this->userName    = $username;
        $this->password    = $password;
        $this->email       = $email;
    }

    public function run()
    {
        $this->say("committing docker container with id: $this->containerId");
        $commitResult = $this->taskDockerCommit($this->containerId)
            ->name($this->repository)
            ->run();
        /**
         *  1. commit the container specified by the containerId with the specified
         *     repository
         *  2. If push is true:
         *         -- login
         *         -- push the container to the repository
         *  3. return
         */
        if ($this->push) {
            if ($commitResult->getExitCode() > 0) {
                return $commitResult;
            }
            $this->say("successfully committed container");
            $this->say("logging into docker repository as: $this->userName");
            $loginResult = $this->taskExec("docker login -e $this->email -p $this->password -u $this->userName")
                ->run();
            if ($loginResult->getExitCode() > 0) {
                /**
                 * We couldn't login nothing left to do.
                 */
                return $loginResult;
            }
            $this->say("successfully logged into the docker repository");
            $this->say("pushing $this->repository");

            return $this->taskExec("docker push $this->repository")
                ->run();
        }

        return $commitResult;
    }
}
