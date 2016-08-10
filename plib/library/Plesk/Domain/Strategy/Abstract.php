<?php

abstract class Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Abstract
{
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
     */
    public function __construct($domainName, $domainType = null, $domainId = null)
    {
        $this->domainName = $domainName;
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

    protected function isRemoteDomainsProtectionEnabled()
    {
        return ('0' == pm_Settings::get(
            Modules_SpamexpertsExtension_Form_Settings::OPTION_SKIP_REMOTE_DOMAINS
        ));
    }

}
