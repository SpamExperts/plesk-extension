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

        // Create a default package if no one exists
        // $I->createDefaultPackage();

        // Go to plugin configuration page
        $I->goToPage(SpamExpertsEmailSecurityPage::SETTINGS_TAB);
    }


    public function _after(SettingsSteps $I)
    {
        // Remove all created accounts
        // $I->removeCreatedAccounts();
    }

    public function _failed(SettingsSteps $I)
    {
        $this->_after($I);
    }

    /**
     * Verify the 'Configuration page' layout and functionality
     */
    public function checkSettingsPage(SettingsSteps $I)
    {
        // Verify configuration page layout
        $I->verifyPageLayout();

        // Fill configuration fields
        // $I->setFieldApiUrl(PsfConfig::getApiUrl());
        // $I->setFieldApiHostname(PsfConfig::getApiHostname());
        // $I->setFieldApiUsernameIfEmpty(PsfConfig::getApiUsername());
        // $I->setFieldApiPassword(PsfConfig::getApiPassword());
        // $I->setFieldPrimaryMX(PsfConfig::getPrimaryMX());

        // // Submit settings
        // $I->submitSettingForm();

        // // Check if configuration was saved
        // $I->seeSubmissionIsSuccessful();

    }
}





