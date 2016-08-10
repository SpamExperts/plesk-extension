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
        $this->protectAsDomain();
    }

}
