<?php

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 */
class Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Status_Primary
    extends Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Abstract
{
    /**
     * Unprotection procedure executor
     *
     * @return bool
     */
    public function execute()
    {
        return $this->statusAsDomain();
    }

}
