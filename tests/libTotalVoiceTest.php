<?php

require_once('../libTotalVoice.php');

class libTotalVoiceTest extends PHPUnit_Framework_TestCase
{
    private $accessToken = '63bc3b5bddefb8a04fac29347fd665b6';

    public function testConstruct()
    {
        $api = new libTotalVoice($this->accessToken);
        $this->assertNotEmpty((array)$api);
    }
    public function testGetErrors()
    {
        $api = new libTotalVoice($this->accessToken);
        $error = $api->getErrors(41);
        $this->assertEquals('Saldo insuficiente', $error);
    }

    public function testGetOnlyNumber()
    {
        $api = new libTotalVoice($this->accessToken);
        $number = $api->getOnlyNumber('(48) 99644-7783');
        $this->assertEquals('48996447783', $number);
    }

    public function testIsCellNumber()
    {
        $api = new libTotalVoice($this->accessToken);
        $number = $api->isCellNumber('48996447783');
        $this->assertTrue($number);
    }

    public function testRequest()
    {
        $api = new libTotalVoice($this->accessToken);
        $account = $api->request('conta', 'GET');
        $this->assertEquals('200', $account['status']);
    }

    public function testSendSMS()
    {
        $api = new libTotalVoice($this->accessToken);
        $objSMS = $api->sendSMS('(48) 99644-7783', 'teste');
        $this->assertArrayHasKey('status', $objSMS);
    }
}
