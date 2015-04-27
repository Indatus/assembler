<?php
namespace Indatus\Assembler\Test\Tasks;

use Mockery as m;
use Robo\Result;

class PackageTaskTest extends \PHPUnit_Framework_TestCase
{

    public function tearDown()
    {
        m::close();
    }

    public function testRunWhenPushIsTrueAndCommitFails()
    {
        $push           = true;
        $containerId    = uniqid();
        $repository     = "name/image";
        $mockCommitTask = m::mock();
        /**
         * @var \Indatus\Assembler\Tasks\PackageTask|\Mockery\MockInterface
         */
        $packageTask      = m::mock('Indatus\Assembler\Tasks\PackageTask',
            [
                $containerId,
                $repository,
                $push,
            ]
        )->makePartial()->shouldAllowMockingProtectedMethods();
        $failedRoboResult = new Result($packageTask, 1, 'failed to commit');
        $packageTask->shouldReceive('taskDockerCommit')
            ->with($containerId)
            ->once()
            ->andReturn($mockCommitTask);
        $mockCommitTask->shouldReceive('name')
            ->with($repository)
            ->once()
            ->andReturn($mockCommitTask);
        $mockCommitTask->shouldReceive('run')
            ->once()
            ->andReturn($failedRoboResult);

        $actualResult = $packageTask->run();
        $this->assertEquals(
            $actualResult->getExitCode(),
            $failedRoboResult->getExitCode()
        );
        $this->assertEquals(
            $actualResult->getMessage(),
            $failedRoboResult->getMessage()
        );
    }

    public function testRunWhenPushIsTrueAndLoginFails()
    {
        $push        = true;
        $containerId = uniqid();
        $repository  = "name/image";
        $userName    = "someusername";
        $email       = "someemail@domain.com";
        $password    = "password";

        $mockLoginExecTask = m::mock();
        $mockTask          = m::mock();
        /**
         * @var \Indatus\Assembler\Tasks\PackageTask|\Mockery\MockInterface
         */
        $packageTask       = m::mock('Indatus\Assembler\Tasks\PackageTask',
            [
                $containerId,
                $repository,
                $push,
                $userName,
                $password,
                $email
            ]
        )->makePartial()->shouldAllowMockingProtectedMethods();
        $commitResult      = new Result($packageTask, 0);
        $failedLoginResult = new Result($packageTask, 1, 'failed to login');
        $packageTask->shouldReceive('taskDockerCommit')
            ->once()
            ->with($containerId)
            ->andReturn($mockTask);
        $mockTask->shouldReceive('name')
            ->once()
            ->with($repository)
            ->andReturn($mockTask);
        $mockTask->shouldReceive('run')
            ->once()
            ->andReturn($commitResult);
        $packageTask->shouldReceive('taskExec')
            ->once()
            ->with("docker login -e $email -p $password -u $userName")
            ->andReturn($mockLoginExecTask);
        $mockLoginExecTask->shouldReceive('run')
            ->once()
            ->andReturn($failedLoginResult);
        $actualResult = $packageTask->run();
        $this->assertEquals(
            $actualResult->getExitCode(),
            $failedLoginResult->getExitCode()
        );
    }

    public function testRunWhenPushIsTrue()
    {
        $push              = true;
        $containerId       = uniqid();
        $repository        = "name/image";
        $userName          = "someusername";
        $email             = "someemail@domain.com";
        $password          = "password";
        $expectedResult    = uniqid();
        $mockExecLoginTask = m::mock();
        $mockExecPushTask  = m::mock();
        $mockTask          = m::mock();

        /**
         * @var \Indatus\Assembler\Tasks\PackageTask|\Mockery\MockInterface
         */
        $packageTask  = m::mock('Indatus\Assembler\Tasks\PackageTask',
            [
                $containerId,
                $repository,
                $push,
                $userName,
                $password,
                $email
            ]
        )->makePartial()->shouldAllowMockingProtectedMethods();
        $loginResult  = new Result($packageTask, 0);
        $commitResult = new Result($packageTask, 0);
        $packageTask->shouldReceive('taskDockerCommit')
            ->once()
            ->with($containerId)
            ->andReturn($mockTask);
        $mockTask->shouldReceive('name')
            ->once()
            ->with($repository)
            ->andReturn($mockTask);
        $mockTask->shouldReceive('run')
            ->once()
            ->andReturn($commitResult);
        $packageTask->shouldReceive('taskExec')
            ->once()
            ->with("docker login -e $email -p $password -u $userName")
            ->andReturn($mockExecLoginTask);
        $mockExecLoginTask->shouldReceive('run')
            ->once()
            ->andReturn($loginResult);
        $packageTask->shouldReceive('taskExec')
            ->once()
            ->with("docker push $repository")
            ->andReturn($mockExecPushTask);
        $mockExecPushTask->shouldReceive('run')
            ->once()
            ->andReturn($expectedResult);
        $packageTask->shouldReceive('say')
            ->once()
            ->with("logging into docker repository as: $userName");
        $packageTask->shouldReceive('say')
            ->once()
            ->with("successfully logged into the docker repository");
        $packageTask->shouldReceive('say')
            ->once()
            ->with("pushing $repository");
        $actualResult = $packageTask->run();
        $this->assertEquals($actualResult, $expectedResult);
    }

    public function testRunWhePushIsFalse()
    {
        $push           = false;
        $containerId    = uniqid();
        $repository     = "name/image";
        $mockTask       = m::mock();
        $expectedResult = uniqid();
        /**
         * @var \Indatus\Assembler\Tasks\PackageTask|\Mockery\MockInterface
         */
        $packageTask = m::mock('Indatus\Assembler\Tasks\PackageTask',
            [
                $containerId,
                $repository,
                $push
            ]
        )->makePartial()->shouldAllowMockingProtectedMethods();
        $packageTask->shouldReceive('taskDockerCommit')
            ->once()
            ->with($containerId)
            ->andReturn($mockTask);
        $mockTask->shouldReceive('name')
            ->once()
            ->with($repository)
            ->andReturn($mockTask);
        $mockTask->shouldReceive('run')
            ->once()
            ->andReturn($expectedResult);
        $actualResult = $packageTask->run();
        $this->assertEquals($actualResult, $expectedResult);
    }
}
