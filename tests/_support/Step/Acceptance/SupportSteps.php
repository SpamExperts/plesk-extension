<?php

namespace Step\Acceptance;

use Page\SupportPage;
use Page\SpamExpertsEmailSecurityPage;
use Codeception\Util\Locator;

class SupportSteps extends CommonSteps
{
    /**
     * Function used to verify the extension support page layout
     */
    public function verifyPageLayout()
    {
        $this->amGoingTo("\n\n --- Check support page layout --- \n");

        // Grab the value from field
        $value = $this->grabValueFrom(Locator::combine(
            SupportPage::SUPPORT_REPLY_TO_XPATH,
            SupportPage::SUPPORT_REPLY_TO_CSS));

        $this->seeElement(SupportPage::PLESK_INFORMATION);
        $this->seeElement(SupportPage::PHP_VERSION_INFORMATION);
        $this->seeElement(SupportPage::EXTENSION_VERSION_INFORMATION);
        $this->seeElement(SupportPage::PLESK_DIAGNOSTICS);
        $this->seeElement(SupportPage::PHP_VERSION_DIAGNOSTICS);
        $this->seeElement(SupportPage::EXTENSION_VERSION_DIAGNOSTICS);

        $this->seeElement(SupportPage::SUPPORT_SUBJECT_XPATH);
        $this->seeElement(SupportPage::SUPPORT_REPLY_TO_XPATH);
        $this->seeElement(SupportPage::SUPPORT_MESSAGE_XPATH);
        $this->seeElement(SupportPage::SEND_BTN_XPATH);

        $this->seeInField(SupportPage::SUPPORT_REPLY_TO_XPATH, $value);
    }

    /**
     * Function used to verify the mandatory fields
     * @param string $string - value
     */
    public function verifyMandatoryFields()
    {
        $this->amGoingTo("\n\n --- Check mandatory fieds are required --- \n");

        $this->setFieldSupportSubject(" ");
        $this->setFieldSupportReplyTo(" ");
        $this->setFieldSupportMessage(" ");
        $this->submitSupportForm();
        $this->seeErrorMessage(
            "Error: Please enter correct values into the form.");

        $this->reloadPage();
        $this->setFieldSupportSubject("Test-subject");
        $this->setFieldSupportReplyTo(" ");
        $this->setFieldSupportMessage(" ");
        $this->submitSupportForm();
        $this->seeErrorMessage(
            "Error: Please enter correct values into the form.");

        $this->reloadPage();
        $this->setFieldSupportSubject(" ");
        $this->setFieldSupportReplyTo("test@example.com");
        $this->setFieldSupportMessage(" ");
        $this->submitSupportForm();
        $this->seeErrorMessage(
            "Error: Please enter correct values into the form.");

        $this->reloadPage();
        $this->setFieldSupportSubject(" ");
        $this->setFieldSupportReplyTo(" ");
        $this->setFieldSupportMessage("big angry message here");
        $this->submitSupportForm();
        $this->seeErrorMessage(
            "Error: Please enter correct values into the form.");
    }

    /**
     * Function used to fill the support subject field
     * @param string $string - value
     */
    public function setFieldSupportSubject($string)
    {
        if ($string)
            $this->fillField(Locator::combine(
                SupportPage::SUPPORT_SUBJECT_XPATH,
                SupportPage::SUPPORT_SUBJECT_CSS), $string);
    }

    /**
     * Function used to fill the support reply-to field
     * @param string $string - value
     */
    public function setFieldSupportReplyTo($string)
    {
        if ($string)
            $this->fillField(Locator::combine(
                SupportPage::SUPPORT_REPLY_TO_XPATH,
                SupportPage::SUPPORT_REPLY_TO_CSS), $string);
    }

    /**
     * Function used to fill the the support message field
     * @param string $string - value
     */
    public function setFieldSupportMessage($string)
    {
        if ($string)
            $this->fillField(Locator::combine(
                SupportPage::SUPPORT_MESSAGE_XPATH,
                SupportPage::SUPPORT_MESSAGE_CSS), $string);
    }

    /**
     * Function used to submit support request form
     */
    public function submitSupportForm()
    {
        /*Click the save settings button*/
        $this->click(Locator::combine(
            SupportPage::SEND_BTN_XPATH,
            SupportPage::SEND_BTN_CSS));
    }
}
