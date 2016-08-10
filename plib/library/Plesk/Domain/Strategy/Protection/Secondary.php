<?php

class Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Protection_Secondary
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

        if (! $this->isRemoteDomainsProtectionEnabled() && ! $pleskDomain->isLocal()) {
            throw new RuntimeException(
                sprintf(
                    "Domain '%s' has been skipped as it was detected to be remote and remote domains protection if switched off in the extension configuration",
                    htmlentities($this->domainName, ENT_QUOTES, 'UTF-8')
                )
            );
        }

        $spamfilterDomain = $this->initSeDomainInstance($pleskDomain);
        $spamfilterDomain->protect(
            $this->updateDnsMode,
            [],
            $this->getContactEmail($pleskDomain)
        );
    }

}
