# Assembler

[![Latest Version](https://img.shields.io/github/release/indatus/assembler.svg?style=flat-square)](https://github.com/indatus/assembler/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/Indatus/assembler/master.svg?style=flat-square)](https://travis-ci.org/Indatus/assembler)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/indatus/assembler.svg?style=flat-square)](https://scrutinizer-ci.com/g/indatus/assembler/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/indatus/assembler.svg?style=flat-square)](https://scrutinizer-ci.com/g/indatus/assembler)
[![Total Downloads](https://img.shields.io/packagist/dt/indatus/assembler.svg?style=flat-square)](https://packagist.org/packages/indatus/assembler)

Assembler allows you to easily construct and package docker containers using existing config scripts. 
Currently you can build containers using saltstack only, but there are plans to add the ability to provision 
using any configuration management tool such as puppet, chef, and ansible.

## Requirements
- Docker
- PHP 5.4+
- Salt

## Install

Via Composer

``` bash
$ composer create-project indatus/assembler
```

## Usage

Assembler uses [Robo](http://robo.li/) to run tasks on the command line. This allows Assembler to wrap docker 
commands, so you will need to have docker installed wherever you run it. There is a vagrant file in the root 
of this repo that will bootstrap `ubuntu/trusty64` with docker and php5 which will allow you to run assembler. 
Additionally you will need to configure an ssh forward agent by:
``` bash
vi ~/.ssh/config
```
then append:
``` bash
host 172.17.8.150
    ForwardAgent yes
```
then run if you do not already have an id_rsa key:
``` bash
ssh-add
```
then just call `vagrant up`.

If everything goes according to plan, you should be able to start running assembler.

## Terminology

This project extends a metaphor from manufacturing to building and deploying applications. Docker containers 
are [stocked](#stocking), [fabricated](#fabricating), [loaded](#loading), and [packaged](#packaging) based on
settings specified in a [product line](#product-line).

### Product Line

A product line is simply a yaml file specifying the raw goods or base services that your product needs to run.

Consider the following example:
``` yaml
raw_goods:
  - mysql:
    - user: root
    - password: password
  - php
suppliers:
  - git@github.com:saltstack-formulas/mysql.git
  - git@github.com:saltstack-formulas/php.git
```

The above yaml represents a product line that tells assembler you want a container with mysql that has a root 
user with a password of password. Additionally it tells assembler where to find those raw goods as suppliers. You will 
need salt states available for each of the raw goods to be provisioned onto the Docker container.

### Stocking
You stock goods by running `assembler:stock` and specifying a product line to stock example below:

``` bash
$ robo assembler:stock my_webserver --goods-path=./goods
```

The above command will find the my_webserver.yml file git clone the raw goods and automatically generate a
top.sls file for use by salt

### Fabricating
You 'fabricate' containers based on a base image derived from a docker file for example:

``` bash
$ robo assembler:fabricate /path/to/dockerfile/ mybaseimagename
```

The above command will build a base image based on the specified docker file.

### Loading
You 'load' containers with the raw goods specified in your production line for example:

``` bash
$ robo assembler:load mybaseimagename /path/to/goods/
```

The above command will load all of the goods from the specified path. The '/path/to/goods' would be the same
path specified by --goods-path in the stock task.

### Packaging
You 'package' containers that have been stocked, fabricated, and loaded. For example:

``` bash
$ robo assembler:package somecontainerid myrepo/name
```

This will commit the specified containerid into a docker image additionally if you specify the --push flag
the package command will actually push the committed image to myrepo/name. To push you will need to specify
your username, password, and email for the dockerregistry you are pushing to.

### Making
The make command will do everything in one fail swoop. For example:

``` bash
$ robo assembler:make my_base_product
```

will stock, fabricate, load, and package your containers

### Shipping
The ship command will login to a Docker host server via SSH and pull a Docker image and run the container instance.

```
robo ship leftyhitchens/mysql:5.2 192.168.1.100 --ports="3306:3306" --remote_command="mysqld_safe" --sudo
```

The above command will login to the server `192.168.1.100` via SSH and execute the following commands as `sudo`:

```
sudo docker pull leftyhitchens/mysql:5.2
sudo docker run -d --name leftyhitchens_mysql_5.2_<unit_timestamp> -p 3306:3306 mysqld_safe
```

# Provisioning

## Configuration
You can specify which cloud provider assembler needs to ship the container to via the `provisioning.yaml`
file. You will keep your public api token for individual providers here, along with any default ssh keys you
like to be installed on the newly provisioned box. There is an example file in the `config` directory here,
or you can look at the example below.
``` yaml
provider: digitalocean
tokens:
   digitalocean: yoursuperlongapitoken111
ssh:
  keys:
    - 00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00
```
## Running

You can provision a server on digital ocean using the 'provision' command as below:

```
robo provision my_host_name
```

The above will use the information in your config file to create a droplet on digital
ocean with the specified hostname as 'my_host_name'.

You can destroy the cloud server using the 'destroy' command as below:

```
robo destroy 553f9e1334fa9
```

Where the value '553f9e1334fa9' is the unique id of the cloud server to destroy.

## Testing
``` bash
$ phpunit
```

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security
If you discover any security related issues, please email tbell@indatus.com, mbunch@indatus.com, or jlaswell@indatus.com instead of using the issue tracker.

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
