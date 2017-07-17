<?php

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 *
 * @method getValue($name)
 * @method isValid($data)
 */
class Modules_SpamexpertsExtension_Form_Settings extends pm_Form_Simple
{
    const OPTION_USE_CONFIG_FROM_LICENSE = 'use_config_from_license';
    const OPTION_SPAMPANEL_URL = 'spampanel_url';
    const OPTION_SPAMPANEL_API_HOST = 'apihost';
    const OPTION_SPAMPANEL_API_USER = 'apiuser';
    const OPTION_SPAMPANEL_API_PASS = 'apipass';
    const OPTION_SPAMFILTER_MX1 = 'mx1';
    const OPTION_SPAMFILTER_MX2 = 'mx2';
    const OPTION_SPAMFILTER_MX3 = 'mx3';
    const OPTION_SPAMFILTER_MX4 = 'mx4';
    const OPTION_AUTO_ADD_DOMAINS = 'auto_add_domain';
    const OPTION_AUTO_DEL_DOMAINS = 'auto_del_domain';
    const OPTION_AUTO_PROVISION_DNS = 'provision_dns';
    const OPTION_AUTO_SET_CONTACT = 'set_contact';
    const OPTION_EXTRA_DOMAINS_HANDLING = 'handle_extra_domains';
    const OPTION_SKIP_REMOTE_DOMAINS = 'handle_only_localdomains';
    const OPTION_LOGOUT_REDIRECT = 'redirectback';
    const OPTION_AUTO_ADD_DOMAIN_ON_LOGIN = 'add_domain_loginfail';
    const OPTION_USE_IP_DESTINATION_ROUTES = 'use_ip_address_as_destination_routes';
    const OPTION_SUPPORT_EMAIL = 'support_email';

    const LICENSE_CONFIGURATION_ID = 'ext-spamexperts-extension';

    /**
     * Class constructor.
     *
     * @param array|mixed $options
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function __construct($options)
    {
        parent::__construct($options);

        $configFromLicenseUsed = self::useSettingsFromLicense();

        if (! $configFromLicenseUsed) {
            $apiUrlFieldOptions = [
                'label' => 'AntiSpam API URL',
                'value' => $this->getSetting(self::OPTION_SPAMPANEL_URL),
                'required' => true,
                'validators' => [
                    ['NotEmpty', true],
                ],
            ];
            $this->addElement('text', self::OPTION_SPAMPANEL_URL, $apiUrlFieldOptions);

            $apiHostFieldOptions = [
                'label' => 'SpamFilter API hostname',
                'value' => $this->getSetting(self::OPTION_SPAMPANEL_API_HOST),
                'required' => true,
                'validators' => [
                    ['NotEmpty', true],
                ],
            ];
            $this->addElement('text', self::OPTION_SPAMPANEL_API_HOST, $apiHostFieldOptions);

            $apiuserOptions = [
                'label' => 'SpamFilter API username',
                'value' => $this->getSetting(self::OPTION_SPAMPANEL_API_USER),
                'required' => true,
                'validators' => [
                    ['NotEmpty', true],
                ],
            ];

            $apiUserWasSetUp = !empty($this->getSetting(self::OPTION_SPAMPANEL_API_USER));
            if ($apiUserWasSetUp) {
                $apiuserOptions['disabled'] = true;
                unset($apiuserOptions['required']);
            }
            $this->addElement('text', self::OPTION_SPAMPANEL_API_USER, $apiuserOptions);

            $apipassOptions = [
                'label' => 'SpamFilter API password',
                'required' => true,
                'validators' => [
                    ['NotEmpty', true],
                ],
            ];

            $apiPassWasSetUp = !empty($this->getSetting(self::OPTION_SPAMPANEL_API_PASS));
            if ($apiPassWasSetUp) {
                $apipassOptions['disabled'] = true;
                unset($apipassOptions['required']);
            }
            $this->addElement('password', self::OPTION_SPAMPANEL_API_PASS, $apipassOptions);

            $mx1FieldOptions = [
                'label' => 'Primary MX',
                'value' => $this->getSetting(self::OPTION_SPAMFILTER_MX1),
                'required' => true,
                'validators' => [
                    ['NotEmpty', true],
                ],
            ];
            $this->addElement('text', self::OPTION_SPAMFILTER_MX1, $mx1FieldOptions);

            $mx2FieldOptions = [
                'label' => 'Secondary MX',
                'value' => $this->getSetting(self::OPTION_SPAMFILTER_MX2),
                'required' => true,
                'validators' => [
                    ['NotEmpty', true],
                ],
            ];
            $this->addElement('text', self::OPTION_SPAMFILTER_MX2, $mx2FieldOptions);

            $mx3FieldOptions = [
                'label' => 'Tertiary MX',
                'value' => $this->getSetting(self::OPTION_SPAMFILTER_MX3),
                'validators' => [
                    ['NotEmpty', true],
                ],
            ];
            $this->addElement('text', self::OPTION_SPAMFILTER_MX3, $mx3FieldOptions);

            $mx4FieldOptions = [
                'label' => 'Quaternary MX',
                'value' => $this->getSetting(self::OPTION_SPAMFILTER_MX4),
                'validators' => [
                    ['NotEmpty', true],
                ],
            ];
            $this->addElement('text', self::OPTION_SPAMFILTER_MX4, $mx4FieldOptions);

            $supportEmailFieldOptions = [
                'label' => 'Support email',
                'value' => $this->getSetting(self::OPTION_SUPPORT_EMAIL),
                'required' => true,
                'validators' => [
                    ['EmailAddress', true],
                ],
            ];
            $this->addElement('text', self::OPTION_SUPPORT_EMAIL, $supportEmailFieldOptions);
        }

        $autoAddDomains = $this->getSetting(self::OPTION_AUTO_ADD_DOMAINS);
        $this->addElement('radio', self::OPTION_AUTO_ADD_DOMAINS, [
            'label' => 'Automatic action for a new domain when it is added to this server',
            'multiOptions' => ['1' => 'Protect', '0' => 'Skip'],
            'value' => null !== $autoAddDomains ? $autoAddDomains : 1,
        ]);

        $autoDelDomains = $this->getSetting(self::OPTION_AUTO_DEL_DOMAINS);
        $this->addElement('radio', self::OPTION_AUTO_DEL_DOMAINS, [
            'label' => 'Automatic action for a domain when it is deleted from this server',
            'multiOptions' => ['1' => 'Unprotect', '0' => 'Skip'],
            'value' => null !== $autoDelDomains ? $autoDelDomains : 1,
        ]);

        $autoProvisionDns = $this->getSetting(self::OPTION_AUTO_PROVISION_DNS);
        $this->addElement('radio', self::OPTION_AUTO_PROVISION_DNS, [
            'label' => 'Action on the MX records for protected/unprotected domains',
            'multiOptions' => ['1' => 'Update', '0' => 'Skip'],
            'value' => null !== $autoProvisionDns ? $autoProvisionDns : 1,
        ]);

        $autoSetContact = $this->getSetting(self::OPTION_AUTO_SET_CONTACT);
        $this->addElement('radio', self::OPTION_AUTO_SET_CONTACT, [
            'label' => 'Primary contact email for protected domains',
            'multiOptions' => ['1' => 'Set', '0' => 'Skip'],
            'value' => null !== $autoSetContact ? $autoSetContact : 0,
        ]);

        $extraDomainsHandling = $this->getSetting(self::OPTION_EXTRA_DOMAINS_HANDLING);
        $this->addElement('radio', self::OPTION_EXTRA_DOMAINS_HANDLING, [
            'label' => 'Action on secondary domains (domain aliases)',
            'multiOptions' => [
                Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Abstract::SECONDARY_DOMAIN_ACTION_PROTECT_AS_DOMAIN
                    => 'Protect as Domains',
                Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Abstract::SECONDARY_DOMAIN_ACTION_PROTECT_AS_ALIAS
                    => 'Protect as Aliases',
                Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Abstract::SECONDARY_DOMAIN_ACTION_SKIP
                    => 'Skip'
            ],
            'value' => null !== $extraDomainsHandling ? $extraDomainsHandling : 0,
        ]);

        $skipRemoteDomains = $this->getSetting(self::OPTION_SKIP_REMOTE_DOMAINS);
        $this->addElement('radio', self::OPTION_SKIP_REMOTE_DOMAINS, [
            'label' => 'Action on "remote" domains (hosted on external DNS servers)',
            'multiOptions' => ['0' => 'Protect', '1' => 'Skip'],
            'value' => null !== $skipRemoteDomains ? $skipRemoteDomains : 1,
        ]);

        $logoutRedirect = $this->getSetting(self::OPTION_LOGOUT_REDIRECT);
        $this->addElement('radio', self::OPTION_LOGOUT_REDIRECT, [
            'label' => 'Redirect users upon logout',
            'multiOptions' => ['0' => 'To the SpamFilter panel logout page', '1' => 'Back to Plesk'],
            'value' => null !== $logoutRedirect ? $logoutRedirect : 0,
        ]);

        $addDomainsOnLogin = $this->getSetting(self::OPTION_AUTO_ADD_DOMAIN_ON_LOGIN);
        $this->addElement('radio', self::OPTION_AUTO_ADD_DOMAIN_ON_LOGIN, [
            'label' => 'Action upon SpamFilter panel login to not protected domain',
            'multiOptions' => ['0' => 'Protect the domain and make another login attempt', '1' => 'Report error'],
            'value' => null !== $addDomainsOnLogin ? $addDomainsOnLogin : 0,
        ]);

        $useRouteIps = $this->getSetting(self::OPTION_USE_IP_DESTINATION_ROUTES);
        $this->addElement('radio', self::OPTION_USE_IP_DESTINATION_ROUTES, [
            'label' => 'Use as destination routes for clean mail when protecting domains',
            'multiOptions' => ['0' => 'Hostnames', '1' => 'IP addresses'],
            'value' => null !== $useRouteIps ? $useRouteIps : 0,
        ]);

        $this->addControlButtons([
            'cancelHidden' => true,
            'sendTitle'    => 'Save',
        ]);
    }

    /**
     * Checks whenever any settings have been provided or not
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    final static public function areEmpty()
    {
        $manualConfigurationEmpty = (empty(pm_Settings::get(self::OPTION_SPAMPANEL_URL))
            || empty(pm_Settings::get(self::OPTION_SPAMPANEL_API_HOST))
            || empty(pm_Settings::get(self::OPTION_SPAMPANEL_API_USER))
            || empty(pm_Settings::get(self::OPTION_SPAMPANEL_API_PASS)));

        $licenseConfigurationValid = self::useSettingsFromLicense();

        return $manualConfigurationEmpty && ! $licenseConfigurationValid;
    }

    /**
     * Extracts values from Plesk key-value storage
     *
     * @param string $id
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    protected function getSetting($id)
    {
        return pm_Settings::get($id);
    }

    final static public function retrieveFromPleskLicense()
    {
        $keys = pm_License::getAdditionalKeysList(self::LICENSE_CONFIGURATION_ID);

        if (is_array($keys) && $licenseMeta = reset($keys)) {
            if (date('Ymd') > $licenseMeta['lim_date']) {
                pm_Log::debug("Do not use Plesk license data is the license seems to be expired");
            }

            if ($licenseData = json_decode($licenseMeta['key-body'], true)) {
                return $licenseData;
            }
        }

        return '';
    }

    /**
     * @return bool
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    final static function useSettingsFromLicense()
    {
        return self::settingsFromLicenseAvailable()
            && 1 == pm_Settings::get(self::OPTION_USE_CONFIG_FROM_LICENSE);
    }

    /**
     * @return bool
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    final static function settingsFromLicenseAvailable()
    {
        return ! empty(pm_License::getAdditionalKeysList(self::LICENSE_CONFIGURATION_ID));
    }

    final static function getRuntimeConfigOption($key)
    {
        if (self::useSettingsFromLicense()) {
            $licenseConfig = self::retrieveFromPleskLicense();

            return isset($licenseConfig[$key]) ? $licenseConfig[$key] : null;
        }

        return \pm_Settings::get($key);
    }
}
