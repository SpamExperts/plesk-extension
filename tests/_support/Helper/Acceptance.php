<?php

namespace Helper;

use Page\SpamExpertsEmailSecurityPage;

use Codeception\Util\Locator;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Acceptance extends BaseHelper
{
    /**
     * Function used to check the success messages
     */
    public function seeSuccessMessage($message)
    {
        /*Wait for success alert to pop up*/
        $this->getWebDriver()->waitForElement(Locator::combine(
            SpamExpertsEmailSecurityPage::SUCCESS_ALERT_XPATH,
            SpamExpertsEmailSecurityPage::SUCCESS_ALERT_CSS));

        /*Check the success message*/
        $this->getWebDriver()->see($message, Locator::combine(
            SpamExpertsEmailSecurityPage::SUCCESS_ALERT_XPATH,
            SpamExpertsEmailSecurityPage::SUCCESS_ALERT_CSS));
    }

    /**
     * Function used to check the error messages
     */
    public function seeErrorMessage($message)
    {
        /*Wait for success alert to pop up*/
        $this->getWebDriver()->waitForElement(Locator::combine(
            SpamExpertsEmailSecurityPage::ERROR_ALERT_XPATH,
            SpamExpertsEmailSecurityPage::ERROR_ALERT_CSS));

        /*Check the success message*/
        $this->getWebDriver()->see($message, Locator::combine(
            SpamExpertsEmailSecurityPage::ERROR_ALERT_XPATH,
            SpamExpertsEmailSecurityPage::ERROR_ALERT_CSS));
    }
}
