<?php

namespace Step\Acceptance;

use Page\SettingsPage;
use Page\SpamExpertsEmailSecurityPage;
use Codeception\Util\Locator;

class SettingsSteps extends CommonSteps
{
    /**
     * Function used to verify the extension settings page layout
     */
    public function verifyPageLayout()
    {
        $this->amGoingTo("\n\n --- Check settings page layout --- \n");

        $this->seeElement(SettingsPage::SAVE_BUTTON_XPATH);
        $this->seeElement(SettingsPage::ANTISPAM_API_URL_XPATH);
        $this->seeElement(SettingsPage::SPAMFILTER_API_HOSTNAME_XPATH);
        $this->seeElement(SettingsPage::SPAMFILTER_API_PASSWORD_XPATH);
        $this->seeElement(SettingsPage::PRIMARY_MX_XPATH);
        $this->seeElement(SettingsPage::SECONDARY_MX_XPATH);
        $this->seeElement(SettingsPage::TERTIARY_MX_XPATH);
        $this->seeElement(SettingsPage::QUATERNARY_MX_XPATH);
        $this->seeElement(SettingsPage::PROTECT_AUTO_ACTION_FOR_NEW_DOMAIN);
        $this->seeElement(SettingsPage::SKIP_AUTO_ACTION_FOR_NEW_DOMAIN);
        $this->seeElement(SettingsPage::UNPROTECT_AUTO_ACTION_FOR_DELETED_DOMAINS);
        $this->seeElement(SettingsPage::SKIP_AUTO_ACTION_FOR_DELETED_DOMAINS);
        $this->seeElement(SettingsPage::UPDATE_ACTION_MX_RECORD_PROTECT_UNPROTECT);
        $this->seeElement(SettingsPage::SKIP_ACTION_MX_RECORD_PROTECT_UNPROTECT);
        $this->seeElement(SettingsPage::SET_PRIMARY_CONTACT_EMAIL);
        $this->seeElement(SettingsPage::SKIP_PRIMARY_CONTACT_EMAIL);
        $this->seeElement(SettingsPage::PROTECT_AS_DOMAINS_SECONDARY_DOMAINS);
        $this->seeElement(SettingsPage::PROTECT_AS_ALIASES_SECONDARY_DOMAINS);
        $this->seeElement(SettingsPage::SKIP_ACTIONS_SECONDARY_DOMAINS);
        $this->seeElement(SettingsPage::PROTECT_ACTIONS_REMOTE_DOMAINS);
        $this->seeElement(SettingsPage::SKIP_ACTIONS_REMOTE_DOMAINS);
        $this->seeElement(SettingsPage::REDIRECT_TO_SPAMFILTER_LOGOUT);
        $this->seeElement(SettingsPage::REDIRECT_BACK_TO_PLESK);
        $this->seeElement(SettingsPage::PROTECT_DOMAIN_AND_MAKE_ANOTHER_LOGIN_ATEMPT);
        $this->seeElement(SettingsPage::REPORT_ERROR);
        $this->seeElement(SettingsPage::USE_DETINATION_ROUTES_FOR_HOSTNAMES);
        $this->seeElement(SettingsPage::USE_DETINATION_ROUTES_FOR_IPADDRESSES);
    }

    /**
     * Function used to fill the AntiSpam API URL field
     * @param string $string - value
     */
    public function setFieldApiUrl($string)
    {
        if ($string)
            $this->fillField(Locator::combine(SettingsPage::ANTISPAM_API_URL_XPATH,
                SettingsPage::ANTISPAM_API_URL_CSS), $string);
    }

    /**
     * Function used to fill the API hostname field
     * @param string $string - value
     */
    public function setFieldApiHostname($string)
    {
        if ($string)
            $this->fillField(Locator::combine(SettingsPage::ANTISPAM_API_URL_XPATH,
                SettingsPage::ANTISPAM_API_URL_CSS), $string);
    }

    /**
     * Function used to fill API username field if is empty
     * @param string $string - value
     */
    public function setFieldApiUsernameIfEmpty($string)
    {
        // Grab the value from field
        $value = $this->grabValueFrom(Locator::combine(SettingsPage::SPAMFILTER_API_USERNAME_XPATH,
            SettingsPage::SPAMFILTER_API_USERNAME_CSS));

        // If field is empty set the new value
        if (!$value)
            if ($string)
                $this->fillField(Locator::combine(SettingsPage::SPAMFILTER_API_USERNAME_XPATH,
                    SettingsPage::SPAMFILTER_API_USERNAME_CSS), $string);
    }

    /**
     * Funtion used to fill API password field
     * @param string $string - value
     */
    public function setFieldApiPassword($string)
    {
        if ($string)
            $this->fillField(Locator::combine(SettingsPage::SPAMFILTER_API_PASSWORD_XPATH,
                SettingsPage::SPAMFILTER_API_PASSWORD_CSS), $string);
    }

    /**
     * Function used to fill the Primary MX field
     * @param string $string - value
     */
    public function setFieldPrimaryMX($string)
    {
        if ($string)
            $this->fillField(Locator::combine(SettingsPage::PRIMARY_MX_XPATH,
                SettingsPage::PRIMARY_MX_CSS), $string);
    }

    /**
     * Function used to fill the Secondary MX field
     * @param string $string - value
     */
    public function setFieldSecondaryMX($string)
    {
        if ($string)
            $this->fillField(Locator::combine(SettingsPage::SECONDARY_MX_XPATH,
                SettingsPage::SECONDARY_MX_CSS), $string);
    }

    /**
     * Function used to fill the Tertiary MX field
     * @param string $string - value
     */
    public function setFieldTertiaryMX($string)
    {
        if ($string)
            $this->fillField(Locator::combine(SettingsPage::TERTIARY_MX_XPATH,
                SettingsPage::TERTIARY_MX_CSS), $string);
    }

    /**
     * Function used to fill the Quaternary MX field
     * @param string $string - value
     */
    public function setFieldQuaternaryMX($string)
    {
        if ($string)
            $this->fillField(Locator::combine(SettingsPage::QUATERNARY_MX_XPATH,
                SettingsPage::QUATERNARY_MX_CSS), $string);
    }

    /**
     * Function used to obtain values from MX fields
     * @return array - values found in the field
     */
    public function getMxFields()
    {
        $mxRecords = [];
        $mxRecords[] = $this->grabValueFrom(Locator::combine(SettingsPage::PRIMARY_MX_XPATH,
            SettingsPage::PRIMARY_MX_CSS));
        $mxRecords[] = $this->grabValueFrom(Locator::combine(SettingsPage::SECONDARY_MX_XPATH,
            SettingsPage::SECONDARY_MX_CSS));
        $mxRecords[] = $this->grabValueFrom(Locator::combine(SettingsPage::TERTIARY_MX_XPATH,
            SettingsPage::TERTIARY_MX_CSS));
        $mxRecords[] = $this->grabValueFrom(Locator::combine(SettingsPage::QUATERNARY_MX_XPATH,
            SettingsPage::QUATERNARY_MX_CSS));

        return array_filter($mxRecords);
    }

    /**
     * Function used to submit configuration form
     */
    public function submitSettingForm()
    {
        /*Click the save settings button*/
        $this->click(Locator::combine(SettingsPage::SAVE_BUTTON_XPATH,
            SettingsPage::SAVE_BUTTON_CSS));
    }

    /**
     * Function used to check the success messages
     */
    public function seeSuccessMessage($message)
    {
        /*Wait for success alert to pop up*/
        $this->waitForElement(Locator::combine(SpamExpertsEmailSecurityPage::SUCCESS_ALERT_XPATH, SpamExpertsEmailSecurityPage::SUCCESS_ALERT_CSS));

        /*Check the success message*/
        $this->see($message, Locator::combine(SpamExpertsEmailSecurityPage::SUCCESS_ALERT_XPATH, SpamExpertsEmailSecurityPage::SUCCESS_ALERT_CSS));
    }

    /**
     * Function used to check the error messages
     */
    public function seeErrorMessage($message)
    {
        /*Wait for success alert to pop up*/
        $this->waitForElement(Locator::combine(SpamExpertsEmailSecurityPage::ERROR_ALERT_XPATH, SpamExpertsEmailSecurityPage::ERROR_ALERT_CSS));

        /*Check the success message*/
        $this->see($message, Locator::combine(SpamExpertsEmailSecurityPage::ERROR_ALERT_XPATH, SpamExpertsEmailSecurityPage::ERROR_ALERT_CSS));
    }
}
