<?php

use Page\SpamExpertsEmailSecurityPage;
use Step\Acceptance\DomainsSteps;
use Step\Acceptance\SettingsSteps;

class T02_DomainProtectionCest
{
    /*MX records of the test domain before the test, restored afterwards*/
    private $originalMxRecords = [];

    /*Whether the test switched the destination routes type, restored afterwards*/
    private $destinationRoutesTypeChanged = false;

    public function _before(DomainsSteps $I)
    {
        $I->loginAsAdminstrator();
        $I->openEmailSecurityExtension();
    }

    public function _after(SettingsSteps $I)
    {
        /*Restore the test domain's MX records*/
        if (!empty($this->originalMxRecords)) {
            $I->haveMxRecords(ExtensionConfig::getTestDomain(),
                array_values($this->originalMxRecords));
        }

        /*Restore the default destination routes type*/
        if ($this->destinationRoutesTypeChanged) {
            $I->goToPage(SpamExpertsEmailSecurityPage::SETTINGS_TAB);
            $I->selectDestinationRoutesType("Hostnames");
        }
    }

    public function _failed(SettingsSteps $I)
    {
        $this->_after($I);
    }

    /**
     * Verify that unprotecting a domain protected with IP address
     * destination routes does not write IP addresses as MX records
     *
     * @param \DomainsSteps $I
     * @param \SettingsSteps $settings
     */
    public function checkUnprotectDoesNotWriteIpAddressesAsMxRecords(DomainsSteps $I, SettingsSteps $settings)
    {
        $I->wantTo("Verify unprotect does not write IP addresses as MX records");

        $domain = ExtensionConfig::getTestDomain();
        /*The domain's hosting mail server (by default the Plesk server itself); it must resolve to an IPv4 address*/
        $hostingMx = ExtensionConfig::getHostingMx();

        /*Start with the domain not protected, pointing its MX at the hosting server*/
        $this->originalMxRecords = $I->grabMxRecords($domain);
        $I->removeDomainFromSpamFilter($domain);
        $I->haveMxRecords($domain, [['value' => $hostingMx, 'priority' => 10]]);

        /*Use IP addresses as destination routes and let the extension update MX records*/
        $settings->goToPage(SpamExpertsEmailSecurityPage::SETTINGS_TAB);
        $settings->selectActionForMXRecords("Update");
        $this->destinationRoutesTypeChanged = true;
        $settings->selectDestinationRoutesType("IP addresses");

        /*Protect and unprotect the domain*/
        $I->goToPage(SpamExpertsEmailSecurityPage::DOMAINS_TAB);
        $I->protectDomain($domain);
        $I->unprotectDomain($domain);

        /*Check that no MX record points at an IP address*/
        $ipMxRecords = [];
        foreach ($I->grabMxRecords($domain) as $record) {
            if (false !== filter_var($record['value'], FILTER_VALIDATE_IP)) {
                $ipMxRecords[] = $record['value'];
            }
        }
        $I->assertEmpty($ipMxRecords,
            "Unprotect wrote IP addresses as MX records: " . implode(', ', $ipMxRecords));
    }
}
