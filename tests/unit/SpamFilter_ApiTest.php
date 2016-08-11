<?php

require_once LIB_ROOT . '/SpamFilter/Api.php';

class SpamFilter_ApiTest extends \PHPUnit_Framework_TestCase
{
    // tests
    public function testCheckDomainPresent()
    {
        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'call', 'logDebug'])
            ->disableOriginalConstructor()
            ->getMock();
        $sut->expects($this->once())
            ->method('call')
            ->will($this->returnValue('{"present":1}'));

        /** @var Modules_SpamexpertsExtension_SpamFilter_Api $sut */
        $this->assertTrue($sut->checkDomain('example.com'));
    }

    public function testCheckDomainMisses()
    {
        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'call', 'logDebug'])
            ->disableOriginalConstructor()
            ->getMock();
        $sut->expects($this->once())
            ->method('call')
            ->will($this->returnValue('{"present":0}'));

        /** @var Modules_SpamexpertsExtension_SpamFilter_Api $sut */
        $this->assertFalse($sut->checkDomain('example.com'));
    }

    public function testCheckDomainInvalidJson()
    {
        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'call', 'logDebug'])
            ->disableOriginalConstructor()
            ->getMock();
        $sut->expects($this->once())
            ->method('call')
            ->will($this->returnValue('{"present"'));

        /** @var Modules_SpamexpertsExtension_SpamFilter_Api $sut */
        $this->assertFalse($sut->checkDomain('example.com'));
    }

    public function testCheckDomainPlaintextError()
    {
        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'call', 'logDebug'])
            ->disableOriginalConstructor()
            ->getMock();
        $sut->expects($this->once())
            ->method('call')
            ->will($this->returnValue("ERROR: The domain 'example.com' does not belong to the admin 'admin-username'"));

        /** @var Modules_SpamexpertsExtension_SpamFilter_Api $sut */
        $this->assertFalse($sut->checkDomain('example.com'));
    }

    public function testCheckDomainAliasPresent()
    {
        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'call', 'logDebug'])
            ->disableOriginalConstructor()
            ->getMock();
        $sut->expects($this->once())
            ->method('call')
            ->with($this->equalTo("/api/domainalias/list/domain/example.com/"))
            ->will($this->returnValue('["alias.example.net","alias.example.com","alias.example.org"]'));

        /** @var Modules_SpamexpertsExtension_SpamFilter_Api $sut */
        $this->assertTrue($sut->aliasExists('example.com', 'alias.example.net'));
    }

    public function testCheckDomainAliasMissing()
    {
        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'call', 'logDebug'])
            ->disableOriginalConstructor()
            ->getMock();
        $sut->expects($this->once())
            ->method('call')
            ->with($this->equalTo("/api/domainalias/list/domain/example.com/"))
            ->will($this->returnValue('["alias.example.com","alias.example.org"]'));

        /** @var Modules_SpamexpertsExtension_SpamFilter_Api $sut */
        $this->assertFalse($sut->aliasExists('example.com', 'alias.example.net'));
    }

    public function testCheckDomainAliasInvalidJson()
    {
        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'call', 'logDebug'])
            ->disableOriginalConstructor()
            ->getMock();
        $sut->expects($this->once())
            ->method('call')
            ->with($this->equalTo("/api/domainalias/list/domain/example.com/"))
            ->will($this->returnValue('alias.example.comalias.example.org'));

        /** @var Modules_SpamexpertsExtension_SpamFilter_Api $sut */
        $this->assertFalse($sut->aliasExists('example.com', 'alias.example.net'));
    }

}