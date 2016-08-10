<?php

class Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Unprotection_Secondary
    extends Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Abstract
{
    /**
     * Unprotection procedure executor
     *
     * @return void
     */
    public function execute()
    {
        $this->initSeDomainInstance($this->initPanelDomainInstance())->unprotect($this->updateDnsMode);
    }

}
