<?php

use Page\SpamExpertsEmailSecurityPage;
use Page\SettingsPage;
use Step\Acceptance\SettingsSteps;
use Codeception\Util\Locator;

class T01_SettingsCest
{
    public function _before(SettingsSteps $I)
    {
        $I->loginAsAdminstrator();
        $I->openEmailSecurityExtension();
    }

    public function _after(SettingsSteps $I)
    {
        /*Remove all created accounts*/
        // $I->removeCreatedAccounts();
    }

    public function _failed(SettingsSteps $I)
    {
        $this->_after($I);
    }

    /**
     * Verify the 'Settings page' layout and functionality
     *
     * @param \SettingsSteps $I
     */
    public function checkSettingsPage(SettingsSteps $I)
    {
        $I->wantTo("Verify settings page layout and basic functionality");

        /*Verify configuration page layout*/
        $I->goToPage(SpamExpertsEmailSecurityPage::SETTINGS_TAB);
        $I->verifyPageLayout();

        /*Verify mandatory fields are required*/
        $I->verifyMandatoryFields();

        /*Fill configuration fields with valid cases*/
        $I->setFieldApiUrl(ExtensionConfig::getApiUrl());
        $I->setFieldApiHostname(ExtensionConfig::getApiHostname());
        $I->setFieldApiUsernameIfEmpty(ExtensionConfig::getApiUsername());
        $I->setFieldApiPassword(ExtensionConfig::getApiPassword());
        $I->setFieldPrimaryMX(ExtensionConfig::getPrimaryMX());
        $I->setFieldSecondaryMX(ExtensionConfig::getSecondaryMX());
        $I->setFieldSupportEmail("test@plesk.extension.example.com");
        /*Submit settings*/
        $I->submitSettingForm();

        /*Check if configuration was saved*/
        $I->seeSuccessMessage(
            'Information: Configuration options were successfully saved.');
    }

    /**
     * Verify automatic action for a new domain
     * when it is added to this server
     *
      * @param \SettingsSteps $I
     */
    public function checkAutomaticActionsForNewDomain(SettingsSteps $I)
    {
        $I->wantTo("Verify automatic actions for a new domain");
        $I->goToPage(SpamExpertsEmailSecurityPage::SETTINGS_TAB);

        $I->selectActionForNewDomains("Protect");
        $I->selectActionForNewDomains("Skip");
    }

    /**
     * Verify automatic action for a domain when
     * it is deleted from this server
     *
    * @param \SettingsSteps $I
     */
    public function checkAutomaticActionsForDeletedDomain(SettingsSteps $I)
    {
        $I->wantTo("Verify automatic actions for deleted domains");
        $I->goToPage(SpamExpertsEmailSecurityPage::SETTINGS_TAB);

        $I->selectActionForDeletedDomains("Unprotect");
        $I->selectActionForDeletedDomains("Skip");
    }

    /**
     * Verify action on the MX records for
     * protected/unprotected domains
     *
     * @param \SettingsSteps $I
     */
    public function checkActionsOnMXRecords(SettingsSteps $I)
    {
        $I->wantTo("Verify action on MX records domains");
        $I->goToPage(SpamExpertsEmailSecurityPage::SETTINGS_TAB);

        $I->selectActionForMXRecords("Update");
        $I->selectActionForMXRecords("Skip");
    }

    /**
     * Verify action on primary contact email for protected domains
     *
     * @param \SettingsSteps $I
     */
    public function checkActionsOnPrimaryContactEmail(SettingsSteps $I)
    {
        $I->wantTo("Verify action on primary contact email");
        $I->goToPage(SpamExpertsEmailSecurityPage::SETTINGS_TAB);

        $I->selectActionForPrimaryContactEmail("Set");
        $I->selectActionForPrimaryContactEmail("Skip");
    }
}
