<?php

namespace Page;

class DomainsPage
{
    /*Selection checkbox of the list row for a domain*/
    const DOMAIN_ROW_CHECKBOX = "//tr[.//td[contains(., '%s')]]//input[@type='checkbox']";

    /*List tools*/
    const PROTECT_BUTTON = "//*[contains(@class, 'sb-protect-selected')]";
    const UNPROTECT_BUTTON = "//*[contains(@class, 'sb-unprotect-selected')]";

    /*Confirmation popup shown before a group operation*/
    const CONFIRM_BUTTON = "//button[contains(., 'Yes')]";

    /*Status messages returned by the protect/unprotect operations*/
    const PROTECTED_MESSAGE = "Domain '%s' has been successfully protected";
    const UNPROTECTED_MESSAGE = "Domain '%s' has been successfully unprotected";
}
