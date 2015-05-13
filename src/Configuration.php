<?php
namespace Indatus\Assembler;

use Indatus\Assembler\Exceptions\MalformedSSHKeysException;
use Indatus\Assembler\Exceptions\MissingUserDataFileException;
use Indatus\Assembler\Exceptions\NoProviderTokenException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class Configuration
{
    /** @var array */
    protected $values;

    /**
     * @param string $directory
     * @param string $name
     */
    public function __construct($directory = './config', $name = 'provisioning.yaml')
    {
        $file         = (new FileLocator($directory))->locate($name);
        $this->values = Yaml::parse(file_get_contents($file));
    }

    /**
     * Returns the provider configured in the provisioning.yaml file
     */
    public function provider()
    {
        return $this->values['provider'];
    }

    /**
     * @return mixed
     * @throws NoProviderTokenException
     */
    public function apiToken()
    {
        if (array_key_exists('provider', $this->values)) {
            $provider = $this->values['provider'];
        }
        if (array_key_exists('tokens', $this->values) &&
            array_key_exists($provider, $this->values['tokens'])
        ) {
            return $this->values['tokens'][$provider];
        }

        throw new NoProviderTokenException;
    }

    /**
     * @return array|null Array of ssh keys or null if no keys are present.
     * @throws MalformedSSHKeysException
     */
    public function sshKeys()
    {
        if (array_key_exists('ssh', $this->values) &&
            array_key_exists('keys', $this->values['ssh'])
        ) {
            return $this->values['ssh']['keys'];
        }

        throw new MalformedSSHKeysException('Missing ssh section of configuration.');
    }

    public function userData()
    {
        if (array_key_exists('userData', $this->values)) {
            $userDataPath = $this->values['userData'];
            if ((new Filesystem())->exists($userDataPath)) {
                return file_get_contents($userDataPath);
            }

            throw new MissingUserDataFileException;
        }

        return "";
    }
}
