<?php
namespace Indatus\Assembler;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testApiTokenIsParsedWell()
    {
        $apiToken = (new Configuration(realpath('tests'), 'configuration.yaml'))->apiToken();

        $this->assertEquals('yoursuperlongapitoken111', $apiToken);
    }

    public function testProviderIsParsedWell()
    {
        $provider = (new Configuration(realpath('tests'), 'configuration.yaml'))->provider();
        $this->assertEquals($provider, 'myprovider');
    }


    /**
     * @expectedException \Indatus\Assembler\Exceptions\NoProviderTokenException
     */
    public function testComplainsWithBadProvider()
    {
        (new Configuration(realpath('tests'), 'bad-configuration-provider.yaml'))->apiToken();
    }

    public function testSshKeysAreParsedWell()
    {
        $sshKeys         = (new Configuration(realpath('tests'), 'configuration.yaml'))->sshKeys();
        $expectedSshKeys = [
            '00:00:00:00:00:00:00:00:00:00:00:00:00:00:00:00',
            '11:11:11:11:11:11:11:11:11:11:11:11:11:11:11:11',
        ];

        $this->assertEquals($expectedSshKeys, $sshKeys);
    }

    /**
     * @expectedException \Indatus\Assembler\Exceptions\MalformedSSHKeysException
     */
    public function testComplainsWhenSshKeysAreMalformed()
    {
        $sshKeys = (new Configuration(realpath('tests'), 'bad-configuration-ssh.yaml'))->sshKeys();
    }

    public function testUserDataFileIsLocated()
    {
        $userData = (new Configuration(realpath('tests'), 'configuration.yaml'))->userData();
        $expectedUserData = file_get_contents('tests/provision.sh.example');

        $this->assertEquals($expectedUserData, $userData);
    }

    /**
     * @expectedException \Indatus\Assembler\Exceptions\MissingUserDataFileException
     */
    public function testUserDataFileCanBeMissing()
    {
        $userData = (new Configuration(realpath('tests'), 'bad-configuration-userdata.yaml'))->userData();
    }

    public function testUserDataReturnsBlankWhenOmitted()
    {
        $userData = (new Configuration(realpath('tests'), 'configuration-ommitted-userdata.yaml'))->userData();
        $expectedUserData = "";

        $this->assertEquals($expectedUserData, $userData);
    }
}
