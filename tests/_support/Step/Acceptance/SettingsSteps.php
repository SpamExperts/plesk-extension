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

        // Grab the attribute from field
        $value = $this->grabAttributeFrom(Locator::combine(
            SettingsPage::SPAMFILTER_API_PASSWORD_XPATH,
            SettingsPage::SPAMFILTER_API_PASSWORD_CSS), "disabled");

        if ($value == false) {
            $this->seeErrorMessage(
                'Error: Extension is not configured yet. Please set up configuration options.');
        }

        $this->seeElement(SettingsPage::SAVE_BUTTON_XPATH);
        $this->seeElement(SettingsPage::ANTISPAM_API_URL_XPATH);
        $this->seeElement(SettingsPage::SPAMFILTER_API_HOSTNAME_XPATH);
        $this->seeElement(SettingsPage::SPAMFILTER_API_USERNAME_XPATH);
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
     * Function used to verify the mandatory fields
     * @param string $string - value
     */
    public function verifyMandatoryFields()
    {
        $this->amGoingTo("\n\n --- Check mandatory fieds are required --- \n");

        $this->setFieldApiUrl(" ");
        $this->setFieldApiHostname(" ");
        $this->setFieldApiUsernameIfEmpty(" ");
        $this->setFieldApiPassword(" ");
        $this->setFieldPrimaryMX(" ");
        $this->setFieldSecondaryMX(" ");

        $this->submitSettingForm();
        $this->waitForText("This required field is empty.");
        $this->see("This required field is empty. You need to specify a value.",
            SettingsPage::ANTISPAM_API_URL_ROW);
        $this->see("This required field is empty. You need to specify a value.",
            SettingsPage::SPAMFILTER_API_HOSTNAME_ROW);

        $value = $this->grabAttributeFrom(Locator::combine(
            SettingsPage::SPAMFILTER_API_USERNAME_XPATH,
            SettingsPage::SPAMFILTER_API_USERNAME_CSS), "disabled");

        if ($value == false) {
            $this->see(
                "This required field is empty. You need to specify a value.",
                SettingsPage::SPAMFILTER_API_USERNAME_ROW);
        }

        $value = $this->grabAttributeFrom(Locator::combine(
            SettingsPage::SPAMFILTER_API_PASSWORD_XPATH,
            SettingsPage::SPAMFILTER_API_PASSWORD_CSS), "disabled");

        if ($value == false) {
            $this->see(
                "This required field is empty. You need to specify a value.",
                SettingsPage::SPAMFILTER_API_PASSWORD_ROW);
        }

        $this->see("This required field is empty. You need to specify a value.",
            SettingsPage::PRIMARY_MX_ROW);
        $this->see("This required field is empty. You need to specify a value.",
            SettingsPage::SECONDARY_MX_ROW);

    }

    /**
     * Function used to fill the AntiSpam API URL field
     * @param string $string - value
     */
    public function setFieldApiUrl($string)
    {
        if ($string)
            $this->fillField(Locator::combine(
                SettingsPage::ANTISPAM_API_URL_XPATH,
                SettingsPage::ANTISPAM_API_URL_CSS), $string);
    }

    /**
     * Function used to fill the API hostname field
     * @param string $string - value
     */
    public function setFieldApiHostname($string)
    {
        if ($string)
            $this->fillField(Locator::combine(
                SettingsPage::SPAMFILTER_API_HOSTNAME_XPATH,
                SettingsPage::SPAMFILTER_API_HOSTNAME_CSS), $string);
    }

    /**
     * Function used to fill API username field if is empty
     * @param string $string - value
     */
    public function setFieldApiUsernameIfEmpty($string)
    {
        // Grab the value from field
        $value = $this->grabAttributeFrom(Locator::combine(
            SettingsPage::SPAMFILTER_API_USERNAME_XPATH,
            SettingsPage::SPAMFILTER_API_USERNAME_CSS), "disabled");

        // If field is empty set the new value
        if (!$value)
            if ($string)
                $this->fillField(Locator::combine(
                    SettingsPage::SPAMFILTER_API_USERNAME_XPATH,
                    SettingsPage::SPAMFILTER_API_USERNAME_CSS), $string);
    }

    /**
     * Funtion used to fill API password field
     * @param string $string - value
     */
    public function setFieldApiPassword($string)
    {
        // Grab the attribute from field
        $value = $this->grabAttributeFrom(Locator::combine(
            SettingsPage::SPAMFILTER_API_PASSWORD_XPATH,
            SettingsPage::SPAMFILTER_API_PASSWORD_CSS), "disabled");

        // If password field is not disabled set the new value
        if ($value == false)
            if ($string)
                $this->fillField(Locator::combine(
                    SettingsPage::SPAMFILTER_API_PASSWORD_XPATH,
                    SettingsPage::SPAMFILTER_API_PASSWORD_CSS), $string);
    }

    /**
     * Function used to fill the Primary MX field
     * @param string $string - value
     */
    public function setFieldPrimaryMX($string)
    {
        if ($string)
            $this->fillField(Locator::combine(
                SettingsPage::PRIMARY_MX_XPATH,
                SettingsPage::PRIMARY_MX_CSS), $string);
    }

    /**
     * Function used to fill the Secondary MX field
     * @param string $string - value
     */
    public function setFieldSecondaryMX($string)
    {
        if ($string)
            $this->fillField(Locator::combine(
                SettingsPage::SECONDARY_MX_XPATH,
                SettingsPage::SECONDARY_MX_CSS), $string);
    }

    /**
     * Function used to fill the Tertiary MX field
     * @param string $string - value
     */
    public function setFieldTertiaryMX($string)
    {
        if ($string)
            $this->fillField(Locator::combine(
                SettingsPage::TERTIARY_MX_XPATH,
                SettingsPage::TERTIARY_MX_CSS), $string);
    }

    /**
     * Function used to fill the Quaternary MX field
     * @param string $string - value
     */
    public function setFieldQuaternaryMX($string)
    {
        if ($string)
            $this->fillField(Locator::combine(
                SettingsPage::QUATERNARY_MX_XPATH,
                SettingsPage::QUATERNARY_MX_CSS), $string);
    }

    /**
     * Function used to obtain values from MX fields
     * @return array - values found in the field
     */
    public function getMxFields()
    {
        $mxRecords = [];
        $mxRecords[] = $this->grabValueFrom(Locator::combine(
            SettingsPage::PRIMARY_MX_XPATH,
            SettingsPage::PRIMARY_MX_CSS));
        $mxRecords[] = $this->grabValueFrom(Locator::combine(
            SettingsPage::SECONDARY_MX_XPATH,
            SettingsPage::SECONDARY_MX_CSS));
        $mxRecords[] = $this->grabValueFrom(Locator::combine(
            SettingsPage::TERTIARY_MX_XPATH,
            SettingsPage::TERTIARY_MX_CSS));
        $mxRecords[] = $this->grabValueFrom(Locator::combine(
            SettingsPage::QUATERNARY_MX_XPATH,
            SettingsPage::QUATERNARY_MX_CSS));

        return array_filter($mxRecords);
    }

    /**
     * Function used to submit configuration form
     */
    public function submitSettingForm()
    {
        /*Click the save settings button*/
        $this->click(Locator::combine(
            SettingsPage::SAVE_BUTTON_XPATH,
            SettingsPage::SAVE_BUTTON_CSS));
    }

    /**
     * Function used to select option for action on new domains
     *
     * @param string $action
     */
    public function selectActionForNewDomains($action)
    {
        /*Select on of the two actions*/
        $this->checkOption(sprintf(SettingsPage::AUTOMATIC_ADD_DOMAIN,
            $action));
        $this->submitSettingForm();
        $this->seeSuccessMessage(
            'Information: Configuration options were successfully saved.');
    }

    /**
     * Function used to select option for action on deleted domains
     *
     * @param string $action
     */
    public function selectActionForDeletedDomains($action)
    {
        /*Select on of the two actions*/
        $this->checkOption(sprintf(SettingsPage::AUTOMATIC_DELETE_DOMAIN,
            $action));
        $this->submitSettingForm();
        $this->seeSuccessMessage(
            'Information: Configuration options were successfully saved.');
    }

    /**
     * Function used to select option for action on MX records
     *
     * @param string $action
     */
    public function selectActionForMXRecords($action)
    {
        /*Select on of the two actions*/
        $this->checkOption(sprintf(SettingsPage::ACTIONS_ON_MXRECORDS,
            $action));
        $this->submitSettingForm();
        $this->seeSuccessMessage(
            'Information: Configuration options were successfully saved.');
    }

    /**
     * Function used to select option for action on primary email contact
     *
     * @param string $action
     */
    public function selectActionForPrimaryContactEmail($action)
    {
        /*Select on of the two actions*/
        $this->checkOption(sprintf(SettingsPage::ACTIONS_ON_CONTACT_EMAIL,
            $action));
        $this->submitSettingForm();
        $this->seeSuccessMessage(
            'Information: Configuration options were successfully saved.');
    }
}
