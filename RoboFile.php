<?php

use Indatus\Assembler\Configuration;
use Indatus\Assembler\Exceptions\NoProviderTokenException;
use Indatus\Assembler\Traits\FormatProductLine;
use Indatus\Assembler\Traits\FabricatorTrait;
use Indatus\Assembler\Traits\LoaderTrait;
use Indatus\Assembler\Traits\CustomizerTrait;
use Indatus\Assembler\Traits\StockerTrait;
use Indatus\Assembler\Traits\ProvisionTrait;
use Indatus\Assembler\Traits\PackagerTrait;
use Indatus\Assembler\Traits\DestroyerTrait;
use Robo\Result;
use Robo\Tasks;

/**
 * Assemblers's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends Tasks
{
    use FormatProductLine;
    use FabricatorTrait;
    use CustomizerTrait;
    use StockerTrait;
    use LoaderTrait;
    use ProvisionTrait;
    use PackagerTrait;
    use DestroyerTrait;


    /**
     * Retrieve, organize, and create instructions for materials related to a product line
     *
     * @param  string  $productLine      represents the product line being stocked
     * @param  array   $opts
     * @option boolean $clean            when this flag is set then prior to the goods-path and the custom-data-path
     *                                   being written to they will be emptied if they already exist
     * @option string  $goodsPath        path to where each of the suppliers will be written to
     * @option string  $customDataPath   path to where any custom data should be stored
     *
     * @return array|\Robo\Result
     */
    public function assemblerStock(
        $productLine,
        $opts = [
            'clean'          => false,
            'force'          => false,
            'goodsPath'      => 'repos',
            'customDataPath' => 'custom_data'
        ]
    ) {
        $manifest = $this->taskFormatProductLine($productLine)
            ->forSalt()
            ->run();
        $this->say("Created the manifest...");
        $stockerResult = $this->taskStockShelf(
            $opts['goodsPath'],
            $manifest->getData(),
            $opts['clean'],
            $opts['force']
        )->run();
        if ($stockerResult->getExitCode() > 0) {
            $this->say("Unable to stock shelves.");
            return $stockerResult;
        }

        // If we have any special instructions for this productLine, put them in the custom data folder
        if (!is_null($manifest->getData()['custom-data'])) {
            $customizer = $this->taskCustomizeData(
                $manifest->getData()['custom-data'],
                $opts['customDataPath'],
                $productLine,
                $opts['clean'],
                $opts['force']
            );
            $result = $customizer->run();
            if ($result->getExitCode() > 0) {
                $this->say("Unable to process custom data.");
                return $result;
            }
        }

        $this->say("Shelves have been stocked!");
        $data = [
            'goodsPath'      => realpath($opts['goodsPath']),
            'customDataPath' => realpath($opts['customDataPath']),
        ];
        return new Result($stockerResult->getTask(), 0,"Successfully stocked", $data);
    }

    /**
     * Fabricates the specified docker container
     *
     * @param string $pathToDockerFile path to the directory where the docker file lives
     * @param string $tag              repository name (and optionally a tag) for the image
     *
     * @return \Robo\Result
     */
    public function assemblerFabricate($pathToDockerFile, $tag)
    {
        $this->say("Building the docker container located at $pathToDockerFile.");
        $result = $this->taskFabricateContainer($pathToDockerFile, $tag)
            ->run();
        $this->say("Finished fabricating container!");
        return $result;
    }

    /**
     * Loads the specified container with goods
     *
     * @param string $containerId id of the image to load or the image name/repository
     * @param string $shelfPath   path to the raw_goods to be loaded
     * @param string $customPath  path to the custom data
     *
     * @return \Robo\Result
     */
    public function assemblerLoad($containerId, $shelfPath, $customPath = null)
    {
        $this->say("Loading container with goods from shelf at $shelfPath...");
        $result = $this->taskLoadContainer($containerId, $shelfPath, $customPath)
            ->run();
        $this->say('Loaded the container with an id of: ' . $result->getCid());
        return $result;
    }

    /**
     * Commits and pushes a specified container
     *
     * @param  string  $containerId id of the container being packaged for shipping
     * @param  string  $repository  repository being pushed to
     * @param  array   $opts
     * @option boolean $push        true if you want to push to a hub
     * @option string  $username    your user name on the repository
     * @option string  $password    your password for the repository being pushed to
     * @email  string  $email       your email for the repository being pushed to
     *
     * @return \Robo\Result
     */
    public function assemblerPackage(
        $containerId,
        $repository,
        $opts = [
            'push'     => false,
            'username' => null,
            'password' => null,
            'email'    => null
        ]
    ) {
        $push     = $opts['push'];
        $username = $opts['username'];
        $password = $opts['password'];
        $email    = $opts['email'];
        return $this->taskPackage(
            $containerId,
            $repository,
            $push,
            $username,
            $password,
            $email
        )->run();
    }

    /**
     * Stocks the raw_goods, fabricates a base container, loads the conatiner, packages the container
     *
     * @param  string  $productLine
     * @param  array   $opts
     *
     * @option boolean $clean          set this if you want to empty goods and custom paths
     * @option boolean $force          set this if you don't want to be prompted to confirm clean
     * @option string  $goodsPath      set the path where you want your rawgoods to live
     * @option string  $customDataPath set the path where you want your custom-data to be
     * @option string  $dockerfilePath set the path to the base dockerfile default is the current working directory
     * @option string  $repo           repos you want to push to also will be name of image
     * @option boolean $push           set this if you want to push to the remote repo
     * @option string  $username       your username for the docker registry
     * @option string  $password       your password for the docker registry
     * @option string  $email          your email on the docker registry
     *
     * @return array|\Robo\Result
     */
    public function assemblerMake(
        $productLine,
        $opts = [
            'clean'          => false,
            'force'          => false,
            'goodsPath'      => 'repos',
            'customDataPath' => 'custom_data',
            'dockerfilePath' => './',
            'repo'           => null,
            'push'           => false,
            'username'       => null,
            'password'       => null,
            'email'          => null
        ]
    ) {
        $baseName = "base_image".uniqid();
        $stockResult = $this->assemblerStock(
            $productLine,
            $opts
        );
        if ($stockResult->getExitCode() > 0)
        {
            return $stockResult;
        }
        $fabricateResult = $this->assemblerFabricate(
            realpath($opts['dockerfilePath']),
            $baseName
        );
        if ($fabricateResult->getExitCode() > 0)
        {
            return $fabricateResult;
        }
        $loadResult = $this->assemblerLoad(
            $baseName,
            $stockResult->getData()["goodsPath"],
            $stockResult->getData()["customDataPath"]
        );
        if ($loadResult->getExitCode() > 0) {
            return $loadResult;
        }
        return $this->assemblerPackage($loadResult->getCid(), $opts['repo'], $opts);
    }

    /**
     * @param $id the id of the cloud server being destroyed
     * @return Result
     */
    public function destroy($id)
    {
        return $this->taskDestroyServer($id)
            ->run();
    }


    /**
     * Provision a fresh server
     * @param $hostname
     * @param string $region
     * @param string $size
     * @param string $image
     * @param bool $backups
     * @param bool $ipv6
     * @param bool $privateNetworking
     * @return Result
     */
    public function provision(
        $hostname,
        $region = 'nyc3',
        $size = '512mb',
        $image = 'docker',
        $backups = false,
        $ipv6 = false,
        $privateNetworking = false
    ) {
        $result = $this->taskProvisionServer(
            $hostname,
            $region,
            $size,
            $image,
            $backups,
            $ipv6,
            $privateNetworking
        )->run();
        $data = $result->getData();
        $this->say("Provisioned server with id of: $data->id");
        return $result;
    }
}
