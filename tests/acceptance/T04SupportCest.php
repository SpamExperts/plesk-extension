<?php

use Page\SpamExpertsEmailSecurityPage;
use Page\SupportPage;
use Step\Acceptance\SupportSteps;
use Codeception\Util\Locator;

class T04_SupportCest
{
    public function _before(SupportSteps $I)
    {
        $I->loginAsAdminstrator();
        $I->openEmailSecurityExtension();
    }

    public function _after(SupportSteps $I)
    {
        /*Remove all created accounts*/
        // $I->removeCreatedAccounts();
    }

    public function _failed(SupportSteps $I)
    {
        $this->_after($I);
    }

    /**
     * Verify the 'Support page' layout and functionality
     *
     * @param \SupportSteps $I
     */
    public function checkSettingsPage(SupportSteps $I)
    {
        $I->wantTo("Verify settings page layout and basic functionality");

        /*Verify configuration page layout*/
        $I->goToPage(SpamExpertsEmailSecurityPage::SUPPORT_TAB);
        $I->verifyPageLayout();

        /*Verify mandatory fields are required*/
        $I->verifyMandatoryFields();

        /*Fill support request fields with valid cases*/
        $I->setFieldSupportSubject("Test-subject");
        $I->setFieldSupportReplyTo("test@example.com");
        $I->setFieldSupportMessage(
            "big angry message to support");

        /*Submit settings*/
        $I->submitSupportForm();

        /*Check if configuration was saved*/
        $I->seeSuccessMessage(
            'Information: Your message has been queued for delivery.');
    }
}
