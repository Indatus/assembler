<?php


namespace Indatus\Assembler;

use DigitalOceanV2\Adapter\Guzzle5Adapter;
use DigitalOceanV2\DigitalOceanV2;
use Indatus\Assembler\Adapters\DigitalOceanAdapter;
use Indatus\Assembler\Exceptions\InvalidProviderException;

class AdapterFactory
{
    public static function make(Configuration $config)
    {
        $provider = $config->provider();
        $adapter = null;
        switch($provider) {
            case "digitalocean":
                $digitalOcean = new DigitalOceanV2(new Guzzle5Adapter($config->apiToken()));
                $adapter = new DigitalOceanAdapter($digitalOcean);
                break;
            default:
                throw new InvalidProviderException("$provider is not a valid cloud provider");
                break;
        }
        return $adapter;
    }
}