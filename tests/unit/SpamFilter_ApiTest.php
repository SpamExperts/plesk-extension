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

    public function testAddDomainConsidersDomainArgument()
    {
        $domain = 'example.com';

        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'call', 'logDebug'])
            ->disableOriginalConstructor()
            ->getMock();
        $sut->expects($this->once())
            ->method('call')
            ->with($this->equalTo("/api/domain/add/domain/$domain/"));

        /** @var Modules_SpamexpertsExtension_SpamFilter_Api $sut */
        $sut->addDomain($domain);
    }

    public function testAddDomainConsidersDestinationsArgument()
    {
        $domain = 'example.com';
        $destinations = ['primmary.destination.host', 'secondary.destination.host'];

        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'call', 'logDebug'])
            ->disableOriginalConstructor()
            ->getMock();
        $sut->expects($this->once())
            ->method('call')
            ->with($this->equalTo("/api/domain/add/domain/$domain/destinations/" . json_encode($destinations) . "/"));

        /** @var Modules_SpamexpertsExtension_SpamFilter_Api $sut */
        $sut->addDomain($domain, $destinations);
    }

    public function testAddDomainConsidersAliasesArgument()
    {
        $domain = 'example.com';
        $aliases = ['alias.example.net', 'alias.example.org'];

        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'call', 'logDebug'])
            ->disableOriginalConstructor()
            ->getMock();
        $sut->expects($this->once())
            ->method('call')
            ->with($this->equalTo("/api/domain/add/domain/$domain/aliases/" . json_encode($aliases) . "/"));

        /** @var Modules_SpamexpertsExtension_SpamFilter_Api $sut */
        $sut->addDomain($domain, [], $aliases);
    }

    public function testAddDomainSuccessful()
    {
        $domain = 'example.com';

        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'call', 'logDebug'])
            ->disableOriginalConstructor()
            ->getMock();
        $sut->expects($this->once())
            ->method('call')
            ->with($this->equalTo("/api/domain/add/domain/$domain/"))
            ->will($this->returnValue("SUCCESS: Domain '$domain' added"));

        /** @var Modules_SpamexpertsExtension_SpamFilter_Api $sut */
        $this->assertTrue($sut->addDomain($domain));
    }

    public function testAddExistingDomain()
    {
        $domain = 'example.com';

        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'call', 'logDebug'])
            ->disableOriginalConstructor()
            ->getMock();
        $sut->expects($this->once())
            ->method('call')
            ->with($this->equalTo("/api/domain/add/domain/$domain/"))
            ->will($this->returnValue("ERROR: Domain already exists.\nERROR: Failed to add domain '$domain'"));

        /** @var Modules_SpamexpertsExtension_SpamFilter_Api $sut */
        $this->assertTrue($sut->addDomain($domain));
    }

    public function testAddDomainFailed()
    {
        $domain = 'example.com';

        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'call', 'logDebug'])
            ->disableOriginalConstructor()
            ->getMock();
        $sut->expects($this->once())
            ->method('call')
            ->with($this->equalTo("/api/domain/add/domain/$domain/"))
            ->will($this->returnValue("ERROR: Database connection error.\nERROR: Failed to add domain '$domain'"));

        /** @var Modules_SpamexpertsExtension_SpamFilter_Api $sut */
        $this->assertFalse($sut->addDomain($domain));
    }

}