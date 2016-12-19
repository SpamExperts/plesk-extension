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
     */
    public function checkSettingsPage(SettingsSteps $I)
    {
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
        /*Submit settings*/
        $I->submitSettingForm();

        /*Check if configuration was saved*/
        $I->seeSuccessMessage(
            'Information: Configuration options were successfully saved.');
    }
}
