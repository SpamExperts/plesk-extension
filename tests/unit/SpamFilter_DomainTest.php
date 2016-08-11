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
        $parentPleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain'])->getMock();
        $parentPleskDomainMock->expects($this->once())
            ->method('getDomain')
            ->will($this->returnValue('parent.example.com'));

        $pleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain', 'getParent'])->getMock();
        $pleskDomainMock->expects($this->once())
            ->method('getParent')
            ->will($this->returnValue($parentPleskDomainMock));
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
        $parentPleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain'])->getMock();
        $parentPleskDomainMock->expects($this->once())
            ->method('getDomain')
            ->will($this->returnValue('parent.example.com'));

        $pleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain', 'getParent'])->getMock();
        $pleskDomainMock->expects($this->once())
            ->method('getParent')
            ->will($this->returnValue($parentPleskDomainMock));
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

    public function testProtectWithoutUpdatingDns()
    {
        $domain = 'example.com';
        $destinations = ['primmary.destination.host', 'secondary.destination.host'];
        $aliases = [];

        $pleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain'])->getMock();
        $pleskDomainMock->expects($this->once())
            ->method('getDomain')
            ->will($this->returnValue($domain));

        $spamfilterApiMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'addDomain', 'setContact'])
            ->disableOriginalConstructor()
            ->getMock();
        $spamfilterApiMock->expects($this->once())
            ->method('addDomain')
            ->with(
                $this->equalTo($domain),
                $this->equalTo($destinations),
                $this->equalTo($aliases)
            )
            ->will($this->returnValue(true));

        $pleskDnsMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Dns')
            ->setMethods(['getDomainsMxRecords', 'replaceDomainsMxRecords'])
            ->getMock();
        $pleskDnsMock->expects($this->once())
            ->method('getDomainsMxRecords')
            ->with(
                $this->equalTo($pleskDomainMock)
            )
            ->will($this->returnValue($destinations));
        $pleskDnsMock->expects($this->never())
            ->method('replaceDomainsMxRecords');

        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Domain')
            ->setMethods(['getSpamfilterMxs'])
            ->setConstructorArgs([$pleskDomainMock, $spamfilterApiMock, $pleskDnsMock])
            ->getMock();
        $sut->expects($this->never())
            ->method('getSpamfilterMxs');

        /** @var Modules_SpamexpertsExtension_SpamFilter_Domain $sut */
        $sut->protect(false);
    }

    public function testProtectWithUpdatingDns()
    {
        $domain = 'example.com';
        $destinations = ['primmary.destination.host', 'secondary.destination.host'];
        $mxrecords = ['mx.spamfilter.test', 'fallbackmx.spamfilter.test'];
        $aliases = [];

        $pleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain'])->getMock();
        $pleskDomainMock->expects($this->once())
            ->method('getDomain')
            ->will($this->returnValue($domain));

        $spamfilterApiMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'addDomain', 'setContact'])
            ->disableOriginalConstructor()
            ->getMock();
        $spamfilterApiMock->expects($this->once())
            ->method('addDomain')
            ->with(
                $this->equalTo($domain),
                $this->equalTo($destinations),
                $this->equalTo($aliases)
            )
            ->will($this->returnValue(true));

        $pleskDnsMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Dns')
            ->setMethods(['getDomainsMxRecords', 'replaceDomainsMxRecords'])
            ->getMock();
        $pleskDnsMock->expects($this->once())
            ->method('getDomainsMxRecords')
            ->with(
                $this->equalTo($pleskDomainMock)
            )
            ->will($this->returnValue($destinations));
        $pleskDnsMock->expects($this->once())
            ->method('replaceDomainsMxRecords')
            ->with(
                $this->equalTo($pleskDomainMock),
                $this->equalTo($mxrecords)
            );

        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Domain')
            ->setMethods(['getSpamfilterMxs'])
            ->setConstructorArgs([$pleskDomainMock, $spamfilterApiMock, $pleskDnsMock])
            ->getMock();
        $sut->expects($this->once())
            ->method('getSpamfilterMxs')
            ->will($this->returnValue($mxrecords));

        /** @var Modules_SpamexpertsExtension_SpamFilter_Domain $sut */
        $sut->protect(true);
    }

    public function testProtectSkipsUpdatingDnsIfAddDomainFails()
    {
        $domain = 'example.com';
        $destinations = ['primmary.destination.host', 'secondary.destination.host'];
        $mxrecords = ['mx.spamfilter.test', 'fallbackmx.spamfilter.test'];
        $aliases = [];

        $pleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain'])->getMock();
        $pleskDomainMock->expects($this->once())
            ->method('getDomain')
            ->will($this->returnValue($domain));

        $spamfilterApiMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'addDomain', 'setContact'])
            ->disableOriginalConstructor()
            ->getMock();
        $spamfilterApiMock->expects($this->once())
            ->method('addDomain')
            ->with(
                $this->equalTo($domain),
                $this->equalTo($destinations),
                $this->equalTo($aliases)
            )
            ->will($this->returnValue(false));

        $pleskDnsMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Dns')
            ->setMethods(['getDomainsMxRecords', 'replaceDomainsMxRecords'])
            ->getMock();
        $pleskDnsMock->expects($this->once())
            ->method('getDomainsMxRecords')
            ->with(
                $this->equalTo($pleskDomainMock)
            )
            ->will($this->returnValue($destinations));
        $pleskDnsMock->expects($this->never())
            ->method('replaceDomainsMxRecords');

        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Domain')
            ->setMethods(['getSpamfilterMxs'])
            ->setConstructorArgs([$pleskDomainMock, $spamfilterApiMock, $pleskDnsMock])
            ->getMock();
        $sut->expects($this->once())
            ->method('getSpamfilterMxs')
            ->will($this->returnValue($mxrecords));

        /** @var Modules_SpamexpertsExtension_SpamFilter_Domain $sut */
        $sut->protect(true);
    }

    public function testProtectThrowsExceptionIfNoClusterMxRecordsFound()
    {
        $this->expectException(RuntimeException::class);

        $mxrecords = [];

        $pleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')->getMock();
        $spamfilterApiMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct'])
            ->disableOriginalConstructor()
            ->getMock();
        $pleskDnsMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Dns')->getMock();

        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Domain')
            ->setMethods(['getSpamfilterMxs'])
            ->setConstructorArgs([$pleskDomainMock, $spamfilterApiMock, $pleskDnsMock])
            ->getMock();
        $sut->expects($this->once())
            ->method('getSpamfilterMxs')
            ->will($this->returnValue($mxrecords));

        /** @var Modules_SpamexpertsExtension_SpamFilter_Domain $sut */
        $sut->protect(true);
    }

    public function testProtectSetsUpDomainContact()
    {
        $domain = 'example.com';
        $destinations = ['primmary.destination.host', 'secondary.destination.host'];
        $mxrecords = ['mx.spamfilter.test', 'fallbackmx.spamfilter.test'];
        $aliases = [];
        $contact = 'info@simplyspamfree.com';

        $pleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain'])->getMock();
        $pleskDomainMock->expects($this->any())
            ->method('getDomain')
            ->will($this->returnValue($domain));

        $spamfilterApiMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'addDomain', 'setContact'])
            ->disableOriginalConstructor()
            ->getMock();
        $spamfilterApiMock->expects($this->once())
            ->method('addDomain')
            ->with(
                $this->equalTo($domain),
                $this->equalTo($destinations),
                $this->equalTo($aliases)
            )
            ->will($this->returnValue(true));
        $spamfilterApiMock->expects($this->once())
            ->method('setContact')
            ->with(
                $this->equalTo($domain),
                $this->equalTo($contact)
            );

        $pleskDnsMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Dns')
            ->setMethods(['getDomainsMxRecords', 'replaceDomainsMxRecords'])
            ->getMock();
        $pleskDnsMock->expects($this->once())
            ->method('getDomainsMxRecords')
            ->with(
                $this->equalTo($pleskDomainMock)
            )
            ->will($this->returnValue($destinations));
        $pleskDnsMock->expects($this->once())
            ->method('replaceDomainsMxRecords')
            ->with(
                $this->equalTo($pleskDomainMock),
                $this->equalTo($mxrecords)
            );

        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Domain')
            ->setMethods(['getSpamfilterMxs'])
            ->setConstructorArgs([$pleskDomainMock, $spamfilterApiMock, $pleskDnsMock])
            ->getMock();
        $sut->expects($this->once())
            ->method('getSpamfilterMxs')
            ->will($this->returnValue($mxrecords));

        /** @var Modules_SpamexpertsExtension_SpamFilter_Domain $sut */
        $sut->protect(true, $aliases, $contact);
    }

    public function testProtectDoesntSetUpDomainContactIfDomainAddFailed()
    {
        $domain = 'example.com';
        $destinations = ['primmary.destination.host', 'secondary.destination.host'];
        $mxrecords = ['mx.spamfilter.test', 'fallbackmx.spamfilter.test'];
        $aliases = [];
        $contact = 'info@simplyspamfree.com';

        $pleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain'])->getMock();
        $pleskDomainMock->expects($this->any())
            ->method('getDomain')
            ->will($this->returnValue($domain));

        $spamfilterApiMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'addDomain', 'setContact'])
            ->disableOriginalConstructor()
            ->getMock();
        $spamfilterApiMock->expects($this->once())
            ->method('addDomain')
            ->with(
                $this->equalTo($domain),
                $this->equalTo($destinations),
                $this->equalTo($aliases)
            )
            ->will($this->returnValue(false));
        $spamfilterApiMock->expects($this->never())
            ->method('setContact');

        $pleskDnsMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Dns')
            ->setMethods(['getDomainsMxRecords', 'replaceDomainsMxRecords'])
            ->getMock();
        $pleskDnsMock->expects($this->once())
            ->method('getDomainsMxRecords')
            ->with(
                $this->equalTo($pleskDomainMock)
            )
            ->will($this->returnValue($destinations));
        $pleskDnsMock->expects($this->never())
            ->method('replaceDomainsMxRecords');

        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Domain')
            ->setMethods(['getSpamfilterMxs'])
            ->setConstructorArgs([$pleskDomainMock, $spamfilterApiMock, $pleskDnsMock])
            ->getMock();
        $sut->expects($this->once())
            ->method('getSpamfilterMxs')
            ->will($this->returnValue($mxrecords));

        /** @var Modules_SpamexpertsExtension_SpamFilter_Domain $sut */
        $sut->protect(true, $aliases, $contact);
    }

    public function testProtectDoesntSetUpDomainContactIfEmptyValueWasGiven()
    {
        $domain = 'example.com';
        $destinations = ['primmary.destination.host', 'secondary.destination.host'];
        $mxrecords = ['mx.spamfilter.test', 'fallbackmx.spamfilter.test'];
        $aliases = [];
        $contact = '';

        $pleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain'])->getMock();
        $pleskDomainMock->expects($this->any())
            ->method('getDomain')
            ->will($this->returnValue($domain));

        $spamfilterApiMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'addDomain', 'setContact'])
            ->disableOriginalConstructor()
            ->getMock();
        $spamfilterApiMock->expects($this->once())
            ->method('addDomain')
            ->with(
                $this->equalTo($domain),
                $this->equalTo($destinations),
                $this->equalTo($aliases)
            )
            ->will($this->returnValue(true));
        $spamfilterApiMock->expects($this->never())
            ->method('setContact');

        $pleskDnsMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Dns')
            ->setMethods(['getDomainsMxRecords', 'replaceDomainsMxRecords'])
            ->getMock();
        $pleskDnsMock->expects($this->once())
            ->method('getDomainsMxRecords')
            ->with(
                $this->equalTo($pleskDomainMock)
            )
            ->will($this->returnValue($destinations));
        $pleskDnsMock->expects($this->once())
            ->method('replaceDomainsMxRecords')
            ->with(
                $this->equalTo($pleskDomainMock),
                $this->equalTo($mxrecords)
            );

        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Domain')
            ->setMethods(['getSpamfilterMxs'])
            ->setConstructorArgs([$pleskDomainMock, $spamfilterApiMock, $pleskDnsMock])
            ->getMock();
        $sut->expects($this->once())
            ->method('getSpamfilterMxs')
            ->will($this->returnValue($mxrecords));

        /** @var Modules_SpamexpertsExtension_SpamFilter_Domain $sut */
        $sut->protect(true, $aliases, $contact);
    }

    public function testProtectAliasWithoutUpdatingDns()
    {
        $domain = 'example.com';
        $parentDomain = 'parent.example.com';

        $parentPleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain'])->getMock();
        $parentPleskDomainMock->expects($this->once())
            ->method('getDomain')
            ->will($this->returnValue($parentDomain));

        $pleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain', 'getParent'])->getMock();
        $pleskDomainMock->expects($this->once())
            ->method('getParent')
            ->will($this->returnValue($parentPleskDomainMock));
        $pleskDomainMock->expects($this->once())
            ->method('getDomain')
            ->will($this->returnValue($domain));

        $spamfilterApiMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'addAlias'])
            ->disableOriginalConstructor()
            ->getMock();
        $spamfilterApiMock->expects($this->once())
            ->method('addAlias')
            ->with(
                $this->equalTo($parentDomain),
                $this->equalTo($domain)
            )
            ->will($this->returnValue(true));

        $pleskDnsMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Dns')
            ->setMethods(['getDomainsMxRecords', 'replaceDomainsMxRecords'])
            ->getMock();
        $pleskDnsMock->expects($this->never())
            ->method('getDomainsMxRecords');
        $pleskDnsMock->expects($this->never())
            ->method('replaceDomainsMxRecords');

        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Domain')
            ->setMethods(['getSpamfilterMxs'])
            ->setConstructorArgs([$pleskDomainMock, $spamfilterApiMock, $pleskDnsMock])
            ->getMock();
        $sut->expects($this->never())
            ->method('getSpamfilterMxs');

        /** @var Modules_SpamexpertsExtension_SpamFilter_Domain $sut */
        $sut->protectAlias(false);
    }

    public function testProtectAliasWithUpdatingDns()
    {
        $domain = 'example.com';
        $parentDomain = 'parent.example.com';
        $mxrecords = ['mx.spamfilter.test', 'fallbackmx.spamfilter.test'];

        $parentPleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain'])->getMock();
        $parentPleskDomainMock->expects($this->once())
            ->method('getDomain')
            ->will($this->returnValue($parentDomain));

        $pleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain', 'getParent'])->getMock();
        $pleskDomainMock->expects($this->once())
            ->method('getParent')
            ->will($this->returnValue($parentPleskDomainMock));
        $pleskDomainMock->expects($this->once())
            ->method('getDomain')
            ->will($this->returnValue($domain));

        $spamfilterApiMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'addAlias'])
            ->disableOriginalConstructor()
            ->getMock();
        $spamfilterApiMock->expects($this->once())
            ->method('addAlias')
            ->with(
                $this->equalTo($parentDomain),
                $this->equalTo($domain)
            )
            ->will($this->returnValue(true));

        $pleskDnsMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Dns')
            ->setMethods(['getDomainsMxRecords', 'replaceDomainsMxRecords'])
            ->getMock();
        $pleskDnsMock->expects($this->never())
            ->method('getDomainsMxRecords');
        $pleskDnsMock->expects($this->once())
            ->method('replaceDomainsMxRecords')
            ->with(
                $this->equalTo($pleskDomainMock),
                $this->equalTo($mxrecords)
            );

        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Domain')
            ->setMethods(['getSpamfilterMxs'])
            ->setConstructorArgs([$pleskDomainMock, $spamfilterApiMock, $pleskDnsMock])
            ->getMock();
        $sut->expects($this->once())
            ->method('getSpamfilterMxs')
            ->will($this->returnValue($mxrecords));

        /** @var Modules_SpamexpertsExtension_SpamFilter_Domain $sut */
        $sut->protectAlias(true);
    }

    public function testProtectAliasSkipsUpdatingDnsIfAddAliasFails()
    {
        $domain = 'example.com';
        $parentDomain = 'parent.example.com';
        $mxrecords = ['mx.spamfilter.test', 'fallbackmx.spamfilter.test'];

        $parentPleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain'])->getMock();
        $parentPleskDomainMock->expects($this->once())
            ->method('getDomain')
            ->will($this->returnValue($parentDomain));

        $pleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain', 'getParent'])->getMock();
        $pleskDomainMock->expects($this->once())
            ->method('getParent')
            ->will($this->returnValue($parentPleskDomainMock));
        $pleskDomainMock->expects($this->once())
            ->method('getDomain')
            ->will($this->returnValue($domain));

        $spamfilterApiMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'addAlias'])
            ->disableOriginalConstructor()
            ->getMock();
        $spamfilterApiMock->expects($this->once())
            ->method('addAlias')
            ->with(
                $this->equalTo($parentDomain),
                $this->equalTo($domain)
            )
            ->will($this->returnValue(false));

        $pleskDnsMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Dns')
            ->setMethods(['getDomainsMxRecords', 'replaceDomainsMxRecords'])
            ->getMock();
        $pleskDnsMock->expects($this->never())
            ->method('getDomainsMxRecords');
        $pleskDnsMock->expects($this->never())
            ->method('replaceDomainsMxRecords');

        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Domain')
            ->setMethods(['getSpamfilterMxs'])
            ->setConstructorArgs([$pleskDomainMock, $spamfilterApiMock, $pleskDnsMock])
            ->getMock();
        $sut->expects($this->once())
            ->method('getSpamfilterMxs')
            ->will($this->returnValue($mxrecords));

        /** @var Modules_SpamexpertsExtension_SpamFilter_Domain $sut */
        $sut->protectAlias(true);
    }

    public function testProtectAliasThrowsExceptionIfNoClusterMxRecordsFound()
    {
        $this->expectException(RuntimeException::class);

        $mxrecords = [];

        $pleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')->getMock();
        $spamfilterApiMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct'])
            ->disableOriginalConstructor()
            ->getMock();
        $pleskDnsMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Dns')->getMock();

        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Domain')
            ->setMethods(['getSpamfilterMxs'])
            ->setConstructorArgs([$pleskDomainMock, $spamfilterApiMock, $pleskDnsMock])
            ->getMock();
        $sut->expects($this->once())
            ->method('getSpamfilterMxs')
            ->will($this->returnValue($mxrecords));

        /** @var Modules_SpamexpertsExtension_SpamFilter_Domain $sut */
        $sut->protectAlias(true);
    }

    public function testUnrotectWithoutUpdatingDns()
    {
        $domain = 'example.com';

        $pleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain'])->getMock();
        $pleskDomainMock->expects($this->once())
            ->method('getDomain')
            ->will($this->returnValue($domain));

        $spamfilterApiMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'removeDomain', 'getRoutes'])
            ->disableOriginalConstructor()
            ->getMock();
        $spamfilterApiMock->expects($this->once())
            ->method('removeDomain')
            ->with(
                $this->equalTo($domain)
            )
            ->will($this->returnValue(true));
        $spamfilterApiMock->expects($this->never())
            ->method('getRoutes');

        $pleskDnsMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Dns')
            ->setMethods(['replaceDomainsMxRecords'])
            ->getMock();
        $pleskDnsMock->expects($this->never())
            ->method('replaceDomainsMxRecords');

        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Domain')
            ->setMethods(null)
            ->setConstructorArgs([$pleskDomainMock, $spamfilterApiMock, $pleskDnsMock])
            ->getMock();

        /** @var Modules_SpamexpertsExtension_SpamFilter_Domain $sut */
        $sut->unprotect(false);
    }

    public function testUnprotectWithUpdatingDns()
    {
        $domain = 'example.com';
        $destinations = ['primmary.destination.host', 'secondary.destination.host'];

        $pleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain'])->getMock();
        $pleskDomainMock->expects($this->any())
            ->method('getDomain')
            ->will($this->returnValue($domain));

        $spamfilterApiMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'removeDomain', 'getRoutes'])
            ->disableOriginalConstructor()
            ->getMock();
        $spamfilterApiMock->expects($this->once())
            ->method('removeDomain')
            ->with(
                $this->equalTo($domain)
            )
            ->will($this->returnValue(true));
        $spamfilterApiMock->expects($this->once())
            ->method('getRoutes')
            ->with(
                $this->equalTo($domain)
            )
            ->will($this->returnValue($destinations));

        $pleskDnsMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Dns')
            ->setMethods(['replaceDomainsMxRecords'])
            ->getMock();
        $pleskDnsMock->expects($this->once())
            ->method('replaceDomainsMxRecords')
            ->with(
                $this->equalTo($pleskDomainMock),
                $this->equalTo($destinations)
            );

        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Domain')
            ->setMethods(null)
            ->setConstructorArgs([$pleskDomainMock, $spamfilterApiMock, $pleskDnsMock])
            ->getMock();

        /** @var Modules_SpamexpertsExtension_SpamFilter_Domain $sut */
        $sut->unprotect(true);
    }

    public function testUnprotectSkipsUpdatingDnsIfRemoveDomainFails()
    {
        $domain = 'example.com';
        $destinations = ['primmary.destination.host', 'secondary.destination.host'];

        $pleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain'])->getMock();
        $pleskDomainMock->expects($this->any())
            ->method('getDomain')
            ->will($this->returnValue($domain));

        $spamfilterApiMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'removeDomain', 'getRoutes'])
            ->disableOriginalConstructor()
            ->getMock();
        $spamfilterApiMock->expects($this->once())
            ->method('removeDomain')
            ->with(
                $this->equalTo($domain)
            )
            ->will($this->returnValue(false));
        $spamfilterApiMock->expects($this->once())
            ->method('getRoutes')
            ->with(
                $this->equalTo($domain)
            )
            ->will($this->returnValue($destinations));

        $pleskDnsMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Dns')
            ->setMethods(['replaceDomainsMxRecords'])
            ->getMock();
        $pleskDnsMock->expects($this->never())
            ->method('replaceDomainsMxRecords');

        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Domain')
            ->setMethods(null)
            ->setConstructorArgs([$pleskDomainMock, $spamfilterApiMock, $pleskDnsMock])
            ->getMock();

        /** @var Modules_SpamexpertsExtension_SpamFilter_Domain $sut */
        $sut->unprotect(true);
    }

    public function testUnprotectSkipsUpdatingDnsIfRemtrievingDestinationRoutesFails()
    {
        $domain = 'example.com';
        $destinations = [];

        $pleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain'])->getMock();
        $pleskDomainMock->expects($this->any())
            ->method('getDomain')
            ->will($this->returnValue($domain));

        $spamfilterApiMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'removeDomain', 'getRoutes'])
            ->disableOriginalConstructor()
            ->getMock();
        $spamfilterApiMock->expects($this->once())
            ->method('removeDomain')
            ->with(
                $this->equalTo($domain)
            )
            ->will($this->returnValue(false));
        $spamfilterApiMock->expects($this->once())
            ->method('getRoutes')
            ->with(
                $this->equalTo($domain)
            )
            ->will($this->returnValue($destinations));

        $pleskDnsMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Dns')
            ->setMethods(['replaceDomainsMxRecords'])
            ->getMock();
        $pleskDnsMock->expects($this->never())
            ->method('replaceDomainsMxRecords');

        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Domain')
            ->setMethods(null)
            ->setConstructorArgs([$pleskDomainMock, $spamfilterApiMock, $pleskDnsMock])
            ->getMock();

        /** @var Modules_SpamexpertsExtension_SpamFilter_Domain $sut */
        $sut->unprotect(true);
    }

    public function testUnprotectAliasWithoutUpdatingDns()
    {
        $domain = 'example.com';
        $parentDomain = 'parent.example.com';

        $parentPleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain'])->getMock();
        $parentPleskDomainMock->expects($this->once())
            ->method('getDomain')
            ->will($this->returnValue($parentDomain));

        $pleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain', 'getParent'])->getMock();
        $pleskDomainMock->expects($this->once())
            ->method('getParent')
            ->will($this->returnValue($parentPleskDomainMock));
        $pleskDomainMock->expects($this->once())
            ->method('getDomain')
            ->will($this->returnValue($domain));

        $spamfilterApiMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'removeAlias', 'getRoutes'])
            ->disableOriginalConstructor()
            ->getMock();
        $spamfilterApiMock->expects($this->once())
            ->method('removeAlias')
            ->with(
                $this->equalTo($parentDomain),
                $this->equalTo($domain)
            )
            ->will($this->returnValue(true));
        $spamfilterApiMock->expects($this->never())
            ->method('getRoutes');

        $pleskDnsMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Dns')
            ->setMethods(['replaceDomainsMxRecords'])
            ->getMock();
        $pleskDnsMock->expects($this->never())
            ->method('replaceDomainsMxRecords');

        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Domain')
            ->setMethods(null)
            ->setConstructorArgs([$pleskDomainMock, $spamfilterApiMock, $pleskDnsMock])
            ->getMock();

        /** @var Modules_SpamexpertsExtension_SpamFilter_Domain $sut */
        $sut->unprotectAlias(false);
    }

    public function testUnprotectAliasWithUpdatingDns()
    {
        $domain = 'example.com';
        $parentDomain = 'parent.example.com';
        $destinations = ['primmary.destination.host', 'secondary.destination.host'];

        $parentPleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain'])->getMock();
        $parentPleskDomainMock->expects($this->any())
            ->method('getDomain')
            ->will($this->returnValue($parentDomain));

        $pleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain', 'getParent'])->getMock();
        $pleskDomainMock->expects($this->once())
            ->method('getParent')
            ->will($this->returnValue($parentPleskDomainMock));
        $pleskDomainMock->expects($this->any())
            ->method('getDomain')
            ->will($this->returnValue($domain));

        $spamfilterApiMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'removeAlias', 'getRoutes'])
            ->disableOriginalConstructor()
            ->getMock();
        $spamfilterApiMock->expects($this->once())
            ->method('removeAlias')
            ->with(
                $this->equalTo($parentDomain),
                $this->equalTo($domain)
            )
            ->will($this->returnValue(true));
        $spamfilterApiMock->expects($this->once())
            ->method('getRoutes')
            ->with(
                $this->equalTo($parentDomain)
            )
            ->will($this->returnValue($destinations));

        $pleskDnsMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Dns')
            ->setMethods(['replaceDomainsMxRecords'])
            ->getMock();
        $pleskDnsMock->expects($this->once())
            ->method('replaceDomainsMxRecords')
            ->with(
                $this->equalTo($pleskDomainMock),
                $destinations
            );

        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Domain')
            ->setMethods(null)
            ->setConstructorArgs([$pleskDomainMock, $spamfilterApiMock, $pleskDnsMock])
            ->getMock();

        /** @var Modules_SpamexpertsExtension_SpamFilter_Domain $sut */
        $sut->unprotectAlias(true);
    }

    public function testUnprotectAliasSkipsUpdatingDnsIfRemoveAliasFails()
    {
        $domain = 'example.com';
        $parentDomain = 'parent.example.com';
        $destinations = ['primmary.destination.host', 'secondary.destination.host'];

        $parentPleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain'])->getMock();
        $parentPleskDomainMock->expects($this->any())
            ->method('getDomain')
            ->will($this->returnValue($parentDomain));

        $pleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain', 'getParent'])->getMock();
        $pleskDomainMock->expects($this->once())
            ->method('getParent')
            ->will($this->returnValue($parentPleskDomainMock));
        $pleskDomainMock->expects($this->any())
            ->method('getDomain')
            ->will($this->returnValue($domain));

        $spamfilterApiMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'removeAlias', 'getRoutes'])
            ->disableOriginalConstructor()
            ->getMock();
        $spamfilterApiMock->expects($this->once())
            ->method('removeAlias')
            ->with(
                $this->equalTo($parentDomain),
                $this->equalTo($domain)
            )
            ->will($this->returnValue(false));
        $spamfilterApiMock->expects($this->once())
            ->method('getRoutes')
            ->with(
                $this->equalTo($parentDomain)
            )
            ->will($this->returnValue($destinations));

        $pleskDnsMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Dns')
            ->setMethods(['replaceDomainsMxRecords'])
            ->getMock();
        $pleskDnsMock->expects($this->never())
            ->method('replaceDomainsMxRecords');

        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Domain')
            ->setMethods(null)
            ->setConstructorArgs([$pleskDomainMock, $spamfilterApiMock, $pleskDnsMock])
            ->getMock();

        /** @var Modules_SpamexpertsExtension_SpamFilter_Domain $sut */
        $sut->unprotectAlias(true);
    }

    public function testUnprotectAliasSkipsUpdatingDnsIfDestinationRoutesAreEmpty()
    {
        $domain = 'example.com';
        $parentDomain = 'parent.example.com';
        $destinations = [];

        $parentPleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain'])->getMock();
        $parentPleskDomainMock->expects($this->any())
            ->method('getDomain')
            ->will($this->returnValue($parentDomain));

        $pleskDomainMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Domain')
            ->setMethods(['getDomain', 'getParent'])->getMock();
        $pleskDomainMock->expects($this->once())
            ->method('getParent')
            ->will($this->returnValue($parentPleskDomainMock));
        $pleskDomainMock->expects($this->any())
            ->method('getDomain')
            ->will($this->returnValue($domain));

        $spamfilterApiMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Api')
            ->setMethods(['__construct', 'removeAlias', 'getRoutes'])
            ->disableOriginalConstructor()
            ->getMock();
        $spamfilterApiMock->expects($this->once())
            ->method('removeAlias')
            ->with(
                $this->equalTo($parentDomain),
                $this->equalTo($domain)
            )
            ->will($this->returnValue(true));
        $spamfilterApiMock->expects($this->once())
            ->method('getRoutes')
            ->with(
                $this->equalTo($parentDomain)
            )
            ->will($this->returnValue($destinations));

        $pleskDnsMock = $this->getMockBuilder('\Modules_SpamexpertsExtension_Plesk_Dns')
            ->setMethods(['replaceDomainsMxRecords'])
            ->getMock();
        $pleskDnsMock->expects($this->never())
            ->method('replaceDomainsMxRecords');

        $sut = $this->getMockBuilder('\Modules_SpamexpertsExtension_SpamFilter_Domain')
            ->setMethods(null)
            ->setConstructorArgs([$pleskDomainMock, $spamfilterApiMock, $pleskDnsMock])
            ->getMock();

        /** @var Modules_SpamexpertsExtension_SpamFilter_Domain $sut */
        $sut->unprotectAlias(true);
    }

}