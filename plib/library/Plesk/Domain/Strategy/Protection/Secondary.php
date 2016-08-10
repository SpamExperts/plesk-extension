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
        switch ($this->getSecondaryDomainsAction()) {
            case self::SECONDARY_DOMAIN_ACTION_SKIP:
                throw new RuntimeException(
                    sprintf(
                        "Domain '%s' has been skipped according to current configuration",
                        htmlentities($this->domainName, ENT_QUOTES, 'UTF-8')
                    )
                );

            case self::SECONDARY_DOMAIN_ACTION_PROTECT_AS_DOMAIN:
                $this->protectAsDomain();

                break;

            case self::SECONDARY_DOMAIN_ACTION_PROTECT_AS_ALIAS:
                $this->protectAsAlias();

                break;
        }
    }

}
