<?php

namespace Step\Acceptance;

use Page\ProfessionalSpamFilterPage;
use Codeception\Util\Locator;

class ConfigurationSteps extends CommonSteps
{
    /**
     * Function used to verify the plugin configuration page layout
     */
    public function verifyPageLayout()
    {
        $this->amGoingTo("\n\n --- Check configuration tab layout --- \n");

        $this->see("Configuration", "//h3[contains(.,'Configuration')]");
        $this->see('You can hover over the options to see more detailed information about what they do.');

        // Setting up configuration
        $this->seeElement(ProfessionalSpamFilterPage::CONFIGURATION_LINK);
        $this->seeElement(ProfessionalSpamFilterPage::BRANDING_LINK);
        $this->seeElement(ProfessionalSpamFilterPage::DOMAIN_LIST_LINK);
        $this->seeElement(ProfessionalSpamFilterPage::BRANDING_LINK);
        $this->seeElement(ProfessionalSpamFilterPage::MIGRATION_LINK);
        $this->seeElement(ProfessionalSpamFilterPage::UPDATE_LINK);
        $this->seeElement(ProfessionalSpamFilterPage::SUPPORT_LINK);

        // Fields
        $this->see('AntiSpam API URL');
        $this->seeElement(Locator::combine(ConfigurationPage::ANTISPAM_API_URL_FIELD_XPATH, ConfigurationPage::ANTISPAM_API_URL_FIELD_CSS));

        $this->see('API hostname');
        $this->seeElement(Locator::combine(ConfigurationPage::API_HOSTNAME_FIELD_XPATH, ConfigurationPage::API_HOSTNAME_FIELD_CSS));

        $this->see('API username');
        $this->seeElement(Locator::combine(ConfigurationPage::API_USERNAME_FIELD_XPATH, ConfigurationPage::API_USERNAME_FIELD_CSS));

        $this->see('API password');
        $this->seeElement(Locator::combine(ConfigurationPage::API_PASSWORD_FIELD_XPATH, ConfigurationPage::API_PASSWORD_FIELD_CSS));

        $this->see('Primary MX');
        $this->seeElement(Locator::combine(ConfigurationPage::MX_PRIMARY_FIELD_XPATH, ConfigurationPage::MX_PRIMARY_FIELD_CSS));

        $this->see('Secondary MX');
        $this->seeElement(Locator::combine(ConfigurationPage::MX_SECONDARY_FIELD_XPATH, ConfigurationPage::MX_SECONDARY_FIELD_CSS));

        $this->see('Tertiary MX');
        $this->seeElement(Locator::combine(ConfigurationPage::MX_TERTIARY_FIELD_XPATH, ConfigurationPage::MX_TERTIARY_FIELD_CSS));

        $this->see('Quaternary MX');
        $this->seeElement(Locator::combine(ConfigurationPage::MX_QUATERNARY_FIELD_XPATH, ConfigurationPage::MX_QUATERNARY_FIELD_CSS));

        $this->see('SPF Record');
        $this->seeElement(Locator::combine(ConfigurationPage::SPF_RECORD_FIELD_XPATH, ConfigurationPage::SPF_RECORD_FIELD_CSS));

        $this->see('TTL to use for MX records');
        $this->seeElement(Locator::combine(ConfigurationPage::TTL_FOR_MX_DROP_DOWN_XPATH, ConfigurationPage::TTL_FOR_MX_DROP_DOWN_CSS));

        $this->see('Language');
        $this->seeElement(Locator::combine(ConfigurationPage::LANGUAGE_DROP_DOWN_XPATH, ConfigurationPage::LANGUAGE_DROP_DOWN_CSS));

        // Checkboxes
        $this->see('Enable SSL for API requests to the spamfilter and Cpanel');
        $this->seeElement(Locator::combine(ConfigurationPage::ENABLE_SSL_FOR_API_OPT_XPATH, ConfigurationPage::ENABLE_SSL_FOR_API_OPT_CSS));

        $this->see('Enable automatic updates');
        $this->seeElement(Locator::combine(ConfigurationPage::ENABLE_AUTOMATIC_UPDATES_OPT_XPATH, ConfigurationPage::ENABLE_AUTOMATIC_UPDATES_OPT_CSS));

        $this->see('Automatically add domains to the SpamFilter');
        $this->seeElement(Locator::combine(ConfigurationPage::AUTOMATICALLY_ADD_DOMAINS_OPT_XPATH, ConfigurationPage::AUTOMATICALLY_ADD_DOMAINS_OPT_XPATH));

        $this->see('Automatically delete domains from the SpamFilter');
        $this->seeElement(Locator::combine(ConfigurationPage::AUTOMATICALLY_DELETE_DOMAINS_OPT_XPATH, ConfigurationPage::AUTOMATICALLY_DELETE_DOMAINS_OPT_CSS));

        $this->see('Automatically change the MX records for domains');
        $this->seeElement(Locator::combine(ConfigurationPage::AUTOMATICALLY_CHANGE_MX_OPT_XPATH, ConfigurationPage::AUTOMATICALLY_CHANGE_MX_OPT_CSS));

        $this->see('Configure the email address for this domain');
        $this->seeElement(Locator::combine(ConfigurationPage::CONFIGURE_EMAIL_ADDRESS_OPT_XPATH, ConfigurationPage::CONFIGURE_EMAIL_ADDRESS_OPT_CSS));

        $this->see('Process addon-, parked and subdomains');
        $this->seeElement(Locator::combine(ConfigurationPage::PROCESS_ADDON_CPANEL_OPT_XPATH, ConfigurationPage::PROCESS_ADDON_CPANEL_OPT_CSS));

        $this->see('Add addon-, parked and subdomains as an alias instead of a normal domain.');
        $this->seeElement(Locator::combine(ConfigurationPage::ADD_ADDON_AS_ALIAS_CPANEL_OPT_XPATH, ConfigurationPage::ADD_ADDON_AS_ALIAS_CPANEL_OPT_CSS));

        $this->see('Use existing MX records as routes in the spamfilter.');
        $this->seeElement(Locator::combine(ConfigurationPage::USE_EXISTING_MX_OPT_XPATH, ConfigurationPage::USE_EXISTING_MX_OPT_CSS));

        $this->see('Do not protect remote domains');
        $this->seeElement(Locator::combine(ConfigurationPage::DO_NOT_PROTECT_REMOTE_DOMAINS_OPT_XPATH, ConfigurationPage::DO_NOT_PROTECT_REMOTE_DOMAINS_OPT_CSS));

        $this->see('Redirect back to Cpanel upon logout');
        $this->seeElement(Locator::combine(ConfigurationPage::REDIRECT_BACK_TO_CPANEL_OPT_XPATH, ConfigurationPage::REDIRECT_BACK_TO_CPANEL_OPT_CSS));

        $this->see('Add the domain to the spamfilter during login if it does not exist');
        $this->seeElement(Locator::combine(ConfigurationPage::ADD_DOMAIN_DURING_LOGIN_OPT_XPATH, ConfigurationPage::ADD_DOMAIN_DURING_LOGIN_OPT_CSS));

        $this->see('Force changing route & MX records, even if the domain exist');
        $this->seeElement(Locator::combine(ConfigurationPage::FORCE_CHANGE_MX_ROUTE_OPT_XPATH, ConfigurationPage::FORCE_CHANGE_MX_ROUTE_OPT_CSS));

        $this->see('Change email routing setting "auto" to "local" in bulk protect.');
        $this->seeElement(Locator::combine(ConfigurationPage::CHANGE_EMAIL_ROUTING_OPT_XPATH, ConfigurationPage::CHANGE_EMAIL_ROUTING_OPT_CSS));

        $this->see('Add/Remove a domain when the email routing is changed in Cpanel');
        $this->seeElement(Locator::combine(ConfigurationPage::ADD_REMOVE_DOMAIN_XPATH, ConfigurationPage::ADD_REMOVE_DOMAIN_CSS));

        $this->see('Disable addon in cPanel for reseller accounts.');
        $this->seeElement(Locator::combine(ConfigurationPage::DISABLE_ADDON_IN_CPANEL_XPATH, ConfigurationPage::DISABLE_ADDON_IN_CPANEL_CSS));

        $this->see('Use IP as destination route instead of domain');
        $this->seeElement(Locator::combine(ConfigurationPage::USE_IP_AS_DESTINATION_OPT_XPATH, ConfigurationPage::USE_IP_AS_DESTINATION_OPT_CSS));

        $this->see('Set SPF record for domains');
        $this->seeElement(Locator::combine(ConfigurationPage::SET_SPF_RECORD_XPATH, ConfigurationPage::SET_SPF_RECORD_CSS));

        // Save setting button
        $this->seeElement(Locator::combine(ConfigurationPage::SAVE_SETTINGS_BTN_XPATH, ConfigurationPage::SAVE_SETTINGS_BTN_CSS));
    }

    /**
     * Function used to fill the AntiSpam API URL field
     * @param string $string - value
     */
    public function setFieldApiUrl($string)
    {
        if ($string)
            $this->fillField(Locator::combine(ConfigurationPage::ANTISPAM_API_URL_FIELD_XPATH, ConfigurationPage::ANTISPAM_API_URL_FIELD_CSS), $string);
    }

    /**
     * Function used to fill the API hostname field
     * @param string $string - value
     */
    public function setFieldApiHostname($string)
    {
        if ($string)
            $this->fillField(Locator::combine(ConfigurationPage::API_HOSTNAME_FIELD_XPATH, ConfigurationPage::API_HOSTNAME_FIELD_CSS), $string);
    }

    /**
     * Function used to fill API username field if is empty
     * @param string $string - value
     */
    public function setFieldApiUsernameIfEmpty($string)
    {
        // Grab the value from field
        $value = $this->grabValueFrom(Locator::combine(ConfigurationPage::API_USERNAME_FIELD_XPATH, ConfigurationPage::API_USERNAME_FIELD_CSS));

        // If field is empty set the new value
        if (!$value)
            if ($string)
                $this->fillField(Locator::combine(ConfigurationPage::API_USERNAME_FIELD_XPATH, ConfigurationPage::API_USERNAME_FIELD_CSS), $string);
    }

    /**
     * Funtion used to fill API password field
     * @param string $string - value
     */
    public function setFieldApiPassword($string)
    {
        if ($string)
            $this->fillField(Locator::combine(ConfigurationPage::API_PASSWORD_FIELD_XPATH, ConfigurationPage::API_PASSWORD_FIELD_CSS), $string);
    }

    /**
     * Function used to fill the Primary MX field
     * @param string $string - value
     */
    public function setFieldPrimaryMX($string)
    {
        if ($string)
            $this->fillField(Locator::combine(ConfigurationPage::MX_PRIMARY_FIELD_XPATH, ConfigurationPage::MX_PRIMARY_FIELD_CSS), $string);
    }

    /**
     * Function used to fill the Secondary MX field
     * @param string $string - value
     */
    public function setFieldSecondaryMX($string)
    {
        if ($string)
            $this->fillField(Locator::combine(ConfigurationPage::MX_SECONDARY_FIELD_XPATH, ConfigurationPage::MX_SECONDARY_FIELD_CSS), $string);
    }

    /**
     * Function used to fill the Tertiary MX field
     * @param string $string - value
     */
    public function setFieldTertiaryMX($string)
    {
        if ($string)
            $this->fillField(Locator::combine(ConfigurationPage::MX_TERTIARY_FIELD_XPATH, ConfigurationPage::MX_TERTIARY_FIELD_CSS), $string);
    }

    /**
     * Function used to fill the Quaternary MX field
     * @param string $string - value
     */
    public function setFieldQuaternaryMX($string)
    {
        if ($string)
            $this->fillField(Locator::combine(ConfigurationPage::MX_QUATERNARY_FIELD_XPATH, ConfigurationPage::MX_QUATERNARY_FIELD_CSS), $string);
    }

    /**
     * Function used to obtain values from MX fields
     * @return array - values found in the field
     */
    public function getMxFields()
    {
        $mxRecords = [];
        $mxRecords[] = $this->grabValueFrom(Locator::combine(ConfigurationPage::MX_PRIMARY_FIELD_XPATH, ConfigurationPage::MX_PRIMARY_FIELD_CSS));
        $mxRecords[] = $this->grabValueFrom(Locator::combine(ConfigurationPage::MX_SECONDARY_FIELD_XPATH, ConfigurationPage::MX_SECONDARY_FIELD_CSS));
        $mxRecords[] = $this->grabValueFrom(Locator::combine(ConfigurationPage::MX_TERTIARY_FIELD_XPATH, ConfigurationPage::MX_TERTIARY_FIELD_CSS));
        $mxRecords[] = $this->grabValueFrom(Locator::combine(ConfigurationPage::MX_QUATERNARY_FIELD_XPATH, ConfigurationPage::MX_QUATERNARY_FIELD_CSS));

        return array_filter($mxRecords);
    }

    /**
     * Function used to fill SPF Record field
     * @param  string $string - value
     */
    public function setFieldSpfRecord($string)
    {
        if ($string)
            $this->fillField(Locator::combine(ConfigurationPage::SPF_RECORD_FIELD_XPATH, ConfigurationPage::SPF_RECORD_FIELD_XPATH), $string);
    }

    /**
     * Function used to submit configuration form
     */
    public function submitSettingForm()
    {
        // Click the save settings button
        $this->click(Locator::combine(ConfigurationPage::SAVE_SETTINGS_BTN_XPATH, ConfigurationPage::SAVE_SETTINGS_BTN_CSS));
    }

    /**
     * Function used to check if the submission was successful
     */
    public function seeSubmissionIsSuccessful()
    {
        // Wait for success alert to pop up
        $this->waitForElement(Locator::combine(ConfigurationPage::SUCCESS_ALERT_XPATH, ConfigurationPage::SUCCESS_ALERT_CSS));

        // Check the success message
        $this->see('The settings have been saved.');
    }
}
