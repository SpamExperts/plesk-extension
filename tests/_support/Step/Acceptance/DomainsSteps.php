<?php

namespace Step\Acceptance;

use Page\DomainsPage;

class DomainsSteps extends CommonSteps
{
    /**
     * Function used to protect a domain from the extension domains list
     *
     * @param string $domain
     */
    public function protectDomain($domain)
    {
        $this->amGoingTo("\n\n --- Protect domain {$domain} --- \n");

        $this->selectDomain($domain);
        $this->click(DomainsPage::PROTECT_BUTTON);
        $this->confirmGroupOperation();
        $this->waitForText(sprintf(DomainsPage::PROTECTED_MESSAGE, $domain), 120);
    }

    /**
     * Function used to unprotect a domain from the extension domains list
     *
     * @param string $domain
     */
    public function unprotectDomain($domain)
    {
        $this->amGoingTo("\n\n --- Unprotect domain {$domain} --- \n");

        $this->selectDomain($domain);
        $this->click(DomainsPage::UNPROTECT_BUTTON);
        $this->confirmGroupOperation();
        $this->waitForText(sprintf(DomainsPage::UNPROTECTED_MESSAGE, $domain), 120);
    }

    /**
     * Function used to select a domain in the extension domains list
     *
     * @param string $domain
     */
    public function selectDomain($domain)
    {
        $checkbox = sprintf(DomainsPage::DOMAIN_ROW_CHECKBOX, $domain);

        /*The list re-renders after a group operation, so start from a freshly loaded one*/
        $this->reloadPage();
        $this->waitForElement($checkbox, 30);
        $this->checkOption($checkbox);
    }

    /**
     * Function used to confirm the popup shown before a group operation
     */
    private function confirmGroupOperation()
    {
        $this->waitForElementVisible(DomainsPage::CONFIRM_BUTTON, 10);
        $this->click(DomainsPage::CONFIRM_BUTTON);
    }
}
