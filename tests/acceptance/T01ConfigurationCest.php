<?php

use Page\DomainListPage;
use Page\ConfigurationPage;
use Page\TerminateAccountsPage;
use Page\ProfessionalSpamFilterPage;
use Step\Acceptance\ConfigurationSteps;
use Codeception\Util\Locator;

class C01ConfigurationCest
{
    public function _before(ConfigurationSteps $I)
    {
        // Login as root
        $I->loginAsRoot();

        // Create a default package if no one exists
        // $I->createDefaultPackage();

        // Go to plugin configuration page
        $I->goToPage(ProfessionalSpamFilterPage::CONFIGURATION_BTN, ProfessionalSpamFilterPage::TITLE);
    }


    public function _after(ConfigurationSteps $I)
    {
        // Remove all created accounts
        // $I->removeCreatedAccounts();
    }

    public function _failed(ConfigurationSteps $I)
    {
        $this->_after($I);
    }

    /**
     * Verify the 'Configuration page' layout and functionality
     */
    public function checkConfigurationPage(ConfigurationSteps $I, $scenario)
    {
        $scenario->incomplete('Travis CI is not set up to run acceptance tests yet');

        // Verify configuration page layout
        $I->verifyPageLayout();

        // Fill configuration fields
        $I->setFieldApiUrl(PsfConfig::getApiUrl());
        $I->setFieldApiHostname(PsfConfig::getApiHostname());
        $I->setFieldApiUsernameIfEmpty(PsfConfig::getApiUsername());
        $I->setFieldApiPassword(PsfConfig::getApiPassword());
        $I->setFieldPrimaryMX(PsfConfig::getPrimaryMX());

        // Submit settings
        $I->submitSettingForm();

        // Check if configuration was saved
        $I->seeSubmissionIsSuccessful();
    }
}
