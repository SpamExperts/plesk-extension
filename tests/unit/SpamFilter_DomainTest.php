<?php

require_once LIB_ROOT . '/SpamFilter/Domain.php';

class SpamFilter_DomainTest extends \PHPUnit_Framework_TestCase
{
    // tests
    public function testStatusPresent()
    {
        $pleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain'])->getMock();
        $pleskDomainMock->expects($this->once())
            ->method('getDomain')
            ->will($this->returnValue('example.com'));

        $spamfilterApiMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'checkDomain'])
            ->disableOriginalConstructor()
            ->getMock();
        $spamfilterApiMock->expects($this->once())
            ->method('checkDomain')
            ->with(
                $this->equalTo('example.com')
            )
            ->will($this->returnValue(true));

        $pleskDnsMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Dns')->getMock();

        $sut = new \Modules_SpamexpertsExtension_SpamFilter_Domain(
            $pleskDomainMock,
            $spamfilterApiMock,
            $pleskDnsMock
        );
        $this->assertTrue($sut->status());
    }

    public function testStatusMissing()
    {
        $pleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain'])->getMock();
        $pleskDomainMock->expects($this->once())
            ->method('getDomain')
            ->will($this->returnValue('example.com'));

        $spamfilterApiMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'checkDomain'])
            ->disableOriginalConstructor()
            ->getMock();
        $spamfilterApiMock->expects($this->once())
            ->method('checkDomain')
            ->with(
                $this->equalTo('example.com')
            )
            ->will($this->returnValue(false));

        $pleskDnsMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Dns')->getMock();

        $sut = new \Modules_SpamexpertsExtension_SpamFilter_Domain(
            $pleskDomainMock,
            $spamfilterApiMock,
            $pleskDnsMock
        );
        $this->assertFalse($sut->status());
    }

    public function testStatusAliasPresent()
    {
        $paretnPleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain'])->getMock();
        $paretnPleskDomainMock->expects($this->once())
            ->method('getDomain')
            ->will($this->returnValue('parent.example.com'));

        $pleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain', 'getParent'])->getMock();
        $pleskDomainMock->expects($this->once())
            ->method('getParent')
            ->will($this->returnValue($paretnPleskDomainMock));
        $pleskDomainMock->expects($this->once())
            ->method('getDomain')
            ->will($this->returnValue('example.com'));

        $spamfilterApiMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'aliasExists'])
            ->disableOriginalConstructor()
            ->getMock();
        $spamfilterApiMock->expects($this->once())
            ->method('aliasExists')
            ->with(
                $this->equalTo('parent.example.com'),
                $this->equalTo('example.com')
            )
            ->will($this->returnValue(true));

        $pleskDnsMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Dns')->getMock();

        $sut = new \Modules_SpamexpertsExtension_SpamFilter_Domain(
            $pleskDomainMock,
            $spamfilterApiMock,
            $pleskDnsMock
        );
        $this->assertTrue($sut->statusAlias());
    }

    public function testStatusAliasMissing()
    {
        $paretnPleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain'])->getMock();
        $paretnPleskDomainMock->expects($this->once())
            ->method('getDomain')
            ->will($this->returnValue('parent.example.com'));

        $pleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain', 'getParent'])->getMock();
        $pleskDomainMock->expects($this->once())
            ->method('getParent')
            ->will($this->returnValue($paretnPleskDomainMock));
        $pleskDomainMock->expects($this->once())
            ->method('getDomain')
            ->will($this->returnValue('example.com'));

        $spamfilterApiMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'aliasExists'])
            ->disableOriginalConstructor()
            ->getMock();
        $spamfilterApiMock->expects($this->once())
            ->method('aliasExists')
            ->with(
                $this->equalTo('parent.example.com'),
                $this->equalTo('example.com')
            )
            ->will($this->returnValue(false));

        $pleskDnsMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Dns')->getMock();

        $sut = new \Modules_SpamexpertsExtension_SpamFilter_Domain(
            $pleskDomainMock,
            $spamfilterApiMock,
            $pleskDnsMock
        );
        $this->assertFalse($sut->statusAlias());
    }
}