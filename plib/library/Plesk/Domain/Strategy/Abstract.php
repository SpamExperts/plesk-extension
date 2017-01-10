<?php

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 */
abstract class Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Abstract
{
    const SECONDARY_DOMAIN_ACTION_SKIP              = 0;
    const SECONDARY_DOMAIN_ACTION_PROTECT_AS_DOMAIN = 1;
    const SECONDARY_DOMAIN_ACTION_PROTECT_AS_ALIAS  = 2;

    /**
     * Domain name container
     *
     * @var string
     */
    protected $domainName;

    /**
     * Domain type container
     *
     * @var string
     */
    protected $domainType;

    /**
     * Domain ID container
     *
     * @var int
     */
    protected $domainId;

    protected $updateDnsMode = true;

    /**
     * Class contructor
     *
     * @param string $domainName
     * @param string $domainType
     * @param int    $domainId
     *
     * @return Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Abstract
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __construct($domainName, $domainType = null, $domainId = null)
    {
        $this->domainName = idn_to_ascii($domainName);
        $this->domainType = $domainType;
        $this->domainId   = $domainId;

        $this->updateDnsMode =
            0 < pm_Settings::get(Modules_SpamexpertsExtension_Form_Settings::OPTION_AUTO_PROVISION_DNS);
    }

    abstract public function execute();

    /**
     * @param boolean $updateDnsMode
     *
     * @return void
     */
    public function setUpdateDnsMode($updateDnsMode)
    {
        $this->updateDnsMode = $updateDnsMode;
    }

    protected function initPanelDomainInstance()
    {
        return new Modules_SpamexpertsExtension_Plesk_Domain(
            $this->domainName, $this->domainType, $this->domainId
        );
    }

    protected function initSeDomainInstance(Modules_SpamexpertsExtension_Plesk_Domain $panelDomain)
    {
        return new Modules_SpamexpertsExtension_SpamFilter_Domain($panelDomain);
    }

    /**
     * @param Modules_SpamexpertsExtension_Plesk_Domain $pleskDomain
     *
     * @return null|string
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function getContactEmail(Modules_SpamexpertsExtension_Plesk_Domain $pleskDomain)
    {
        $email = null;

        if (pm_Settings::get(Modules_SpamexpertsExtension_Form_Settings::OPTION_AUTO_SET_CONTACT)) {
            $email = $pleskDomain->getContactEmail();
        }

        return $email;
    }

    protected function getAliases()
    {
        return array_column(
            (new Modules_SpamexpertsExtension_Plesk_Domain_Collection)->getAliases(
                [
                    'site-id' => $this->domainId
                ]
            ),
            'name'
        );
    }

    /**
     * @return bool
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function isRemoteDomainsProtectionEnabled()
    {
        return ('0' == pm_Settings::get(
            Modules_SpamexpertsExtension_Form_Settings::OPTION_SKIP_REMOTE_DOMAINS
        ));
    }

    protected function protectAsDomain()
    {
        $pleskDomain = $this->initPanelDomainInstance();
        if (null === $this->domainId) {
            $this->domainId = $pleskDomain->getId();
        }
        if (null === $this->domainType) {
            $this->domainType = $pleskDomain->getType();
        }

        if (! $this->isRemoteDomainsProtectionEnabled() && ! $pleskDomain->isLocal()) {
            throw new RuntimeException(
                sprintf(
                    "Domain '%s' has been skipped as it was detected to be remote and remote domains protection if switched off in the extension configuration",
                    htmlentities($this->domainName, ENT_QUOTES, 'UTF-8')
                )
            );
        }

        $spamfilterDomain = $this->initSeDomainInstance($pleskDomain);

        if ($spamfilterDomain->status()) {
            throw new Modules_SpamexpertsExtension_Exception_IncorrectStatusException(
                sprintf(
                    "Domain '%s' is protected already, skipping it",
                    htmlentities($this->domainName, ENT_QUOTES, 'UTF-8')
                )
            );
        }

        $spamfilterDomain->protect(
            $this->updateDnsMode,
            $this->getAliases(),
            $this->getContactEmail($pleskDomain)
        );
    }

    protected function protectAsAlias()
    {
        $pleskDomain = $this->initPanelDomainInstance();
        if (null === $this->domainId) {
            $this->domainId = $pleskDomain->getId();
        }
        if (null === $this->domainType) {
            $this->domainType = $pleskDomain->getType();
        }

        if (! $this->isRemoteDomainsProtectionEnabled() && ! $pleskDomain->isLocal()) {
            throw new RuntimeException(
                sprintf(
                    "Domain '%s' has been skipped as it was detected to be remote and remote domains protection if switched off in the extension configuration",
                    htmlentities($this->domainName, ENT_QUOTES, 'UTF-8')
                )
            );
        }

        $spamfilterDomain = $this->initSeDomainInstance($pleskDomain);

        if ($spamfilterDomain->statusAlias()) {
            throw new Modules_SpamexpertsExtension_Exception_IncorrectStatusException(
                sprintf(
                    "Domain '%s' is protected already, skipping it",
                    htmlentities($this->domainName, ENT_QUOTES, 'UTF-8')
                )
            );
        }

        $spamfilterDomain->protectAlias(
            $this->updateDnsMode
        );
    }

    protected function unprotectAsDomain()
    {
        $spamfilterDomain = $this->initSeDomainInstance($this->initPanelDomainInstance());

        if (! $spamfilterDomain->status()) {
            throw new Modules_SpamexpertsExtension_Exception_IncorrectStatusException(
                sprintf(
                    "Domain '%s' is not protected, skipping it",
                    htmlentities($this->domainName, ENT_QUOTES, 'UTF-8')
                )
            );
        }

        $spamfilterDomain->unprotect($this->updateDnsMode);
    }

    protected function unprotectAsAlias()
    {
        $spamfilterDomain = $this->initSeDomainInstance($this->initPanelDomainInstance());

        if (! $spamfilterDomain->statusAlias()) {
            throw new Modules_SpamexpertsExtension_Exception_IncorrectStatusException(
                sprintf(
                    "Domain '%s' is not protected, skipping it",
                    htmlentities($this->domainName, ENT_QUOTES, 'UTF-8')
                )
            );
        }

        $spamfilterDomain->unprotectAlias($this->updateDnsMode);
    }

    protected function statusAsDomain()
    {
        return $this->initSeDomainInstance($this->initPanelDomainInstance())->status();
    }

    protected function statusAsAlias()
    {
        return $this->initSeDomainInstance($this->initPanelDomainInstance())->statusAlias();
    }

    /**
     * @return string
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function getSecondaryDomainsAction()
    {
        return pm_Settings::get(
                Modules_SpamexpertsExtension_Form_Settings::OPTION_EXTRA_DOMAINS_HANDLING
            );
    }

}
