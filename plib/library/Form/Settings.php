<?php

class Modules_SpamexpertsExtension_Form_Settings extends pm_Form_Simple
{
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

    public function __construct($options)
    {
        parent::__construct($options);

        $this->addElement('text', self::OPTION_SPAMPANEL_URL, [
            'label' => 'AntiSpam API URL',
            'value' => pm_Settings::get(self::OPTION_SPAMPANEL_URL),
            'required' => true,
            'description' => "This is the URL you use to login to your AntiSpam Web Interface. Please prepend the URL with http:// or https://",
            'validators' => [
                ['NotEmpty', true],
            ],
        ]);

        // API CONFIG
        $this->addElement('text', self::OPTION_SPAMPANEL_API_HOST, [
            'label' => 'SpamFilter API hostname',
            'value' => pm_Settings::get(self::OPTION_SPAMPANEL_API_HOST),
            'required' => true,
            'description' => "This is the hostname of the first antispam server, usually the same as the AntiSpam Web Interface URL unless you're using a CNAME for that.",
            'validators' => [
                ['NotEmpty', true],
            ],
        ]);

        $apiuserOptions = [
            'label' => 'SpamFilter API username',
            'value' => pm_Settings::get(self::OPTION_SPAMPANEL_API_USER),
            'description' => "This is the name of the user that is being used to communicate with the SpamFilter API. You can only change this at the migration page.",
            'validators' => [
                ['NotEmpty', true],
            ],
        ];
        if (!empty(pm_Settings::get(self::OPTION_SPAMPANEL_API_USER))) {
            $apiuserOptions['disabled'] = true;
        } else {
            $apiuserOptions['required'] = true;
        }
        $this->addElement('text', self::OPTION_SPAMPANEL_API_USER, $apiuserOptions);

        $apipassOptions = [
            'label' => 'SpamFilter API password',
            'description' => "This is the password from the user that is being used to communicate with the SpamFilter API. Can be left empty once it has been validated.",
            'validators' => [
                ['NotEmpty', true],
            ],
        ];
        if (!empty(pm_Settings::get(self::OPTION_SPAMPANEL_API_PASS))) {
            $apipassOptions['disabled'] = true;
        } else {
            $apipassOptions['required'] = true;
        }
        $this->addElement('password', self::OPTION_SPAMPANEL_API_PASS, $apipassOptions);


        ////
        // MX records
        ////
        $this->addElement('text', self::OPTION_SPAMFILTER_MX1, [
            'label' => 'Primary MX',
            'value' => pm_Settings::get(self::OPTION_SPAMFILTER_MX1),
            'required' => true,
            'description' => "This is for the first MX record. It can be either your cluster's first server or an other DNS name if you're using Round Robin DNS.",
            'validators' => [
                ['NotEmpty', true],
            ],
        ]);

        $this->addElement('text', self::OPTION_SPAMFILTER_MX2, [
            'label' => 'Secondary MX',
            'value' => pm_Settings::get(self::OPTION_SPAMFILTER_MX2),
            'required' => true,
            'description' => "This is for the second MX record. It can be either your cluster's second server or an other DNS name if you're using Round Robin DNS.",
            'validators' => [
                ['NotEmpty', true],
            ],
        ]);

        $this->addElement('text', self::OPTION_SPAMFILTER_MX3, [
            'label' => 'Tertiary MX',
            'value' => pm_Settings::get(self::OPTION_SPAMFILTER_MX3),
            'description' => "This is for the third MX record. It can be either your cluster's third server or an other DNS name if you're using Round Robin DNS.",
            'validators' => [
                ['NotEmpty', true],
            ],
        ]);

        $this->addElement('text', self::OPTION_SPAMFILTER_MX4, [
            'label' => 'Quaternary MX',
            'value' => pm_Settings::get(self::OPTION_SPAMFILTER_MX4),
            'description' => "This is for the fourth MX record. It can be either your cluster's fourth server or another DNS name if you're using Round Robin DNS.",
            'validators' => [
                ['NotEmpty', true],
            ],
        ]);

        $autoAddDomains = pm_Settings::get(self::OPTION_AUTO_ADD_DOMAINS);
        $this->addElement('radio', self::OPTION_AUTO_ADD_DOMAINS, [
            'label' => 'Automatic action for a new domain when it is added to this server',
            'multiOptions' => ['1' => 'Protect', '0' => 'Skip'],
            'value' => null !== $autoAddDomains ? $autoAddDomains : 1,
        ]);

        $autoDelDomains = pm_Settings::get(self::OPTION_AUTO_DEL_DOMAINS);
        $this->addElement('radio', self::OPTION_AUTO_DEL_DOMAINS, [
            'label' => 'Automatic action for a domain when it is deleted from this server',
            'multiOptions' => ['1' => 'Unprotect', '0' => 'Skip'],
            'value' => null !== $autoDelDomains ? $autoDelDomains : 1,
        ]);

        $autoProvisionDns = pm_Settings::get(self::OPTION_AUTO_PROVISION_DNS);
        $this->addElement('radio', self::OPTION_AUTO_PROVISION_DNS, [
            'label' => 'Action on the MX records for protected/unprotected domains',
            'multiOptions' => ['1' => 'Update', '0' => 'Skip'],
            'value' => null !== $autoProvisionDns ? $autoProvisionDns : 1,
        ]);

        $autoSetContact = pm_Settings::get(self::OPTION_AUTO_SET_CONTACT);
        $this->addElement('radio', self::OPTION_AUTO_SET_CONTACT, [
            'label' => 'Primary contact email for protected domains',
            'multiOptions' => ['1' => 'Set', '0' => 'Skip'],
            'value' => null !== $autoSetContact ? $autoSetContact : 0,
        ]);

        $extraDomainsHandling = pm_Settings::get(self::OPTION_EXTRA_DOMAINS_HANDLING);
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

        $skipRemoteDomains = pm_Settings::get(self::OPTION_SKIP_REMOTE_DOMAINS);
        $this->addElement('radio', self::OPTION_SKIP_REMOTE_DOMAINS, [
            'label' => 'Action on "remote" domains (hosted on external DNS servers)',
            'multiOptions' => ['0' => 'Protect', '1' => 'Skip'],
            'value' => null !== $skipRemoteDomains ? $skipRemoteDomains : 1,
        ]);

        $logoutRedirect = pm_Settings::get(self::OPTION_LOGOUT_REDIRECT);
        $this->addElement('radio', self::OPTION_LOGOUT_REDIRECT, [
            'label' => 'Redirect users upon logout',
            'multiOptions' => ['0' => 'To the SpamFilter panel logout page', '1' => 'Back to Plesk'],
            'value' => null !== $logoutRedirect ? $logoutRedirect : 0,
        ]);

        $addDomainsOnLogin = pm_Settings::get(self::OPTION_AUTO_ADD_DOMAIN_ON_LOGIN);
        $this->addElement('radio', self::OPTION_AUTO_ADD_DOMAIN_ON_LOGIN, [
            'label' => 'Action upon SpamFilter panel login to not protected domain',
            'multiOptions' => ['0' => 'Protect the domain and make another login attempt', '1' => 'Report error'],
            'value' => null !== $addDomainsOnLogin ? $addDomainsOnLogin : 0,
        ]);

        $useRouteIps = pm_Settings::get(self::OPTION_USE_IP_DESTINATION_ROUTES);
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

    final static public function areEmpty()
    {
        return empty(pm_Settings::get(self::OPTION_SPAMPANEL_URL))
            || empty(pm_Settings::get(self::OPTION_SPAMPANEL_API_HOST))
            || empty(pm_Settings::get(self::OPTION_SPAMPANEL_API_USER))
            || empty(pm_Settings::get(self::OPTION_SPAMPANEL_API_PASS));
    }

    final static public function retrieveFromPleskLicense()
    {
        $keys = pm_License::getAdditionalKeysList('spamexperts-extension-beta');

        if (is_array($keys) && $licenseMeta = reset($keys)) {
            if (date('Ymd') < $licenseMeta['lim_date']) {
                pm_Log::debug("Do not use Plesk license data is the license seems to be expired");
            }

            if ($licenseData = json_decode($licenseMeta['key-body'], true)) {
                return $licenseData;
            }
        }

        return '';
    }
}
