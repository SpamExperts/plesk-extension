<?php

class Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Protection_Primary
    extends Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Abstract
{
    /**
     * Unprotection procedure executor
     *
     * @return void
     */
    public function execute()
    {
        $pleskDomain = $this->initPanelDomainInstance();
        if (null === $this->domainId) {
            $this->domainId = $pleskDomain->getId();
        }
        if (null === $this->domainType) {
            $this->domainType = $pleskDomain->getType();
        }

        $spamfilterDomain = $this->initSeDomainInstance($pleskDomain);
        $spamfilterDomain->protect(
            0 < pm_Settings::get(Modules_SpamexpertsExtension_Form_Settings::OPTION_AUTO_PROVISION_DNS),
            $this->getAliases(),
            $this->getContactEmail($pleskDomain)
        );
    }

}
