<?php

use Page\SpamExpertsEmailSecurityPage;
use Step\Acceptance\SettingsSteps;
use Codeception\Util\Locator;

class T01_SettingsCest
{
    public function _before(SettingsSteps $I)
    {
        $I->loginAsAdminstrator();
        $I->openEmailSecurityExtension();

        /*Create a default package if no one exists*/
        // $I->createDefaultPackage();
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
        $I->seeErrorMessage('Error: Extension is not configured yet. Please set up configuration options.');
        $I->verifyPageLayout();

        /*Fill configuration fields*/
        $I->setFieldApiUrl(ExtensionConfig::getApiUrl());
        $I->setFieldApiHostname(ExtensionConfig::getApiHostname());
        $I->setFieldApiUsernameIfEmpty(ExtensionConfig::getApiUsername());
        $I->setFieldApiPassword(ExtensionConfig::getApiPassword());
        $I->setFieldPrimaryMX(ExtensionConfig::getPrimaryMX());

        /*Submit settings*/
        $I->submitSettingForm();

        /*Check if configuration was saved*/
        $I->seeSuccessMessage('The settings have been saved.');
    }
}





