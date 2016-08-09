<?php

class Modules_SpamexpertsExtension_EventListener implements EventListener
{
    public function handleEvent($objectType, $objectId, $action, $oldValues, $newValues)
    {
        pm_Log::debug(__METHOD__ . ' call with the following arguments:');
        pm_Log::vardump($objectType, '$objectType = ');
        pm_Log::vardump($objectId, '$objectId = ');
        pm_Log::vardump($action, '$action = ');
        pm_Log::vardump($oldValues, '$oldValues = ');
        pm_Log::vardump($newValues, '$newValues = ');

        switch ($objectType) {
            case 'domain':
                switch ($action) {
                    case 'domain_create':
                        if (pm_Settings::get(Modules_SpamexpertsExtension_Form_Settings::OPTION_AUTO_ADD_DOMAINS)) {
                            pm_Log::debug("Starting '{$newValues['Domain Name']}' protection in the {$objectType}/{$action} hook");

                            try {
                                $pleskDomain = new Modules_SpamexpertsExtension_Plesk_Domain(
                                    $newValues['Domain Name'],
                                    Modules_SpamexpertsExtension_Plesk_Domain::TYPE_WEBSPACE,
                                    $objectId
                                );

                                $domainContactEmail = null;
                                if (0 < pm_Settings::get(
                                        Modules_SpamexpertsExtension_Form_Settings::OPTION_AUTO_SET_CONTACT
                                    )) {
                                    $domainContactEmail = $pleskDomain->getContactEmail();
                                }

                                $spamfilterDomain = new Modules_SpamexpertsExtension_SpamFilter_Domain($pleskDomain);
                                $spamfilterDomain->protect(
                                    0 < pm_Settings::get(
                                        Modules_SpamexpertsExtension_Form_Settings::OPTION_AUTO_PROVISION_DNS
                                    ),
                                    array_column(
                                        (new Modules_SpamexpertsExtension_Plesk_Domain_Collection)->getAliases(
                                            [
                                                'site-id' => $pleskDomain->getId()
                                            ]
                                        ),
                                        'name'
                                    ),
                                    $domainContactEmail
                                );
                            } catch (Exception $e) {
                                pm_Log::err("Failed to protect '{$newValues['Domain Name']}' - " . $e->getMessage());
                            }
                        } else {
                            pm_Log::debug("Skipping '{$newValues['Domain Name']}' protection in the {$objectType}/{$action} hook");
                        }
                        
                        break;

                    case 'domain_delete':
                        if (pm_Settings::get(Modules_SpamexpertsExtension_Form_Settings::OPTION_AUTO_DEL_DOMAINS)) {
                            pm_Log::debug("Starting '{$oldValues['Domain Name']}' unprotection in the {$objectType}/{$action} hook");

                            try {
                                $pleskDomain = new Modules_SpamexpertsExtension_Plesk_Domain(
                                    $oldValues['Domain Name']
                                );
                                $spamfilterDomain = new Modules_SpamexpertsExtension_SpamFilter_Domain($pleskDomain);
                                $spamfilterDomain->unprotect(false); // It's senseless to update DNS of deleted entity
                            } catch (Exception $e) {
                                pm_Log::err("Failed to unprotect '{$oldValues['Domain Name']}' - " . $e->getMessage());
                            }
                        } else {
                            pm_Log::debug("Skipping '{$oldValues['Domain Name']}' unprotection in the {$objectType}/{$action} hook");
                        }

                        break;
                }
                
                break;

            case 'site':
                switch ($action) {
                    case 'site_create':
                        if (pm_Settings::get(Modules_SpamexpertsExtension_Form_Settings::OPTION_AUTO_ADD_DOMAINS)) {
                            pm_Log::debug("Starting '{$newValues['Domain Name']}' protection in the {$objectType}/{$action} hook");

                            try {
                                $pleskDomain = new Modules_SpamexpertsExtension_Plesk_Domain(
                                    $newValues['Domain Name'],
                                    Modules_SpamexpertsExtension_Plesk_Domain::TYPE_SITE,
                                    $objectId
                                );

                                $domainContactEmail = null;
                                if (0 < pm_Settings::get(
                                        Modules_SpamexpertsExtension_Form_Settings::OPTION_AUTO_SET_CONTACT
                                    )) {
                                    $domainContactEmail = $pleskDomain->getContactEmail();
                                }

                                $spamfilterDomain = new Modules_SpamexpertsExtension_SpamFilter_Domain($pleskDomain);
                                $spamfilterDomain->protect(
                                    0 < pm_Settings::get(
                                        Modules_SpamexpertsExtension_Form_Settings::OPTION_AUTO_PROVISION_DNS
                                    ),
                                    array_column(
                                        (new Modules_SpamexpertsExtension_Plesk_Domain_Collection)->getAliases(
                                            [
                                                'site-id' => $pleskDomain->getId()
                                            ]
                                        ),
                                        'name'
                                    ),
                                    $domainContactEmail
                                );
                            } catch (Exception $e) {
                                pm_Log::err("Failed to protect '{$newValues['Domain Name']}' - " . $e->getMessage());
                            }
                        } else {
                            pm_Log::debug("Skipping '{$newValues['Domain Name']}' protection in the {$objectType}/{$action} hook");
                        }

                        break;

                    case 'site_delete':
                        if (pm_Settings::get(Modules_SpamexpertsExtension_Form_Settings::OPTION_AUTO_DEL_DOMAINS)) {
                            pm_Log::debug("Starting '{$oldValues['Domain Name']}' unprotection in the {$objectType}/{$action} hook");

                            try {
                                $pleskDomain = new Modules_SpamexpertsExtension_Plesk_Domain(
                                    $oldValues['Domain Name']
                                );
                                $spamfilterDomain = new Modules_SpamexpertsExtension_SpamFilter_Domain($pleskDomain);
                                $spamfilterDomain->unprotect(false); // It's senseless to update DNS of deleted entity
                            } catch (Exception $e) {
                                pm_Log::err("Failed to unprotect '{$oldValues['Domain Name']}' - " . $e->getMessage());
                            }
                        } else {
                            pm_Log::debug("Skipping '{$oldValues['Domain Name']}' unprotection in the {$objectType}/{$action} hook");
                        }

                        break;
                }

                break;

            case 'subdomain':
            case 'site_subdomain':
                switch ($action) {
                    case 'subdomain_create':
                    case 'site_subdomain_create':
                        if (pm_Settings::get(Modules_SpamexpertsExtension_Form_Settings::OPTION_AUTO_ADD_DOMAINS)) {
                            pm_Log::debug("Starting '{$newValues['Subdomain Name']}.{$newValues['Domain Name']}' protection in the {$objectType}/{$action} hook");

                            try {
                                $pleskDomain = new Modules_SpamexpertsExtension_Plesk_Domain(
                                    "{$newValues['Subdomain Name']}.{$newValues['Domain Name']}",
                                    Modules_SpamexpertsExtension_Plesk_Domain::TYPE_SUBDOMAIN,
                                    $objectId
                                );

                                $domainContactEmail = null;
                                if (0 < pm_Settings::get(
                                        Modules_SpamexpertsExtension_Form_Settings::OPTION_AUTO_SET_CONTACT
                                    )) {
                                    $domainContactEmail = $pleskDomain->getContactEmail();
                                }

                                $spamfilterDomain = new Modules_SpamexpertsExtension_SpamFilter_Domain($pleskDomain);
                                $spamfilterDomain->protect(
                                    0 < pm_Settings::get(
                                        Modules_SpamexpertsExtension_Form_Settings::OPTION_AUTO_PROVISION_DNS
                                    ),
                                    array_column(
                                        (new Modules_SpamexpertsExtension_Plesk_Domain_Collection)->getAliases(
                                            [
                                                'site-id' => $pleskDomain->getId()
                                            ]
                                        ),
                                        'name'
                                    ),
                                    $domainContactEmail
                                );
                            } catch (Exception $e) {
                                pm_Log::err("Failed to protect '{$newValues['Subdomain Name']}.{$newValues['Domain Name']}' - " . $e->getMessage());
                            }
                        } else {
                            pm_Log::debug("Skipping '{$newValues['Subdomain Name']}.{$newValues['Domain Name']}' protection in the {$objectType}/{$action} hook");
                        }

                        break;

                    case 'subdomain_delete':
                    case 'site_subdomain_delete':
                        if (pm_Settings::get(Modules_SpamexpertsExtension_Form_Settings::OPTION_AUTO_DEL_DOMAINS)) {
                            pm_Log::debug("Starting '{$oldValues['Subdomain Name']}.{$oldValues['Domain Name']}' unprotection in the {$objectType}/{$action} hook");

                            try {
                                $pleskDomain = new Modules_SpamexpertsExtension_Plesk_Domain(
                                    "{$oldValues['Subdomain Name']}.{$oldValues['Domain Name']}"
                                );
                                $spamfilterDomain = new Modules_SpamexpertsExtension_SpamFilter_Domain($pleskDomain);
                                $spamfilterDomain->unprotect(false); // It's senseless to update DNS of deleted entity
                            } catch (Exception $e) {
                                pm_Log::err("Failed to unprotect '{$oldValues['Subdomain Name']}.{$oldValues['Domain Name']}' - " . $e->getMessage());
                            }
                        } else {
                            pm_Log::debug("Skipping '{$oldValues['Subdomain Name']}.{$oldValues['Domain Name']}' unprotection in the {$objectType}/{$action} hook");
                        }

                        break;
                }

                break;

            case 'domain_alias':
            case 'site_alias':
                switch ($action) {
                    case 'domain_alias_create':
                    case 'site_alias_create':
                        if (pm_Settings::get(Modules_SpamexpertsExtension_Form_Settings::OPTION_AUTO_ADD_DOMAINS)) {
                            pm_Log::debug("Starting '{$newValues['Domain Alias Name']}' protection in the {$objectType}/{$action} hook");

                            try {
                                $pleskDomain = new Modules_SpamexpertsExtension_Plesk_Domain(
                                    $newValues['Domain Alias Name'],
                                    Modules_SpamexpertsExtension_Plesk_Domain::TYPE_ALIAS,
                                    $objectId
                                );

                                $domainContactEmail = null;
                                if (0 < pm_Settings::get(
                                        Modules_SpamexpertsExtension_Form_Settings::OPTION_AUTO_SET_CONTACT
                                    )) {
                                    $domainContactEmail = $pleskDomain->getContactEmail();
                                }

                                $spamfilterDomain = new Modules_SpamexpertsExtension_SpamFilter_Domain($pleskDomain);
                                $spamfilterDomain->protect(
                                    0 < pm_Settings::get(
                                        Modules_SpamexpertsExtension_Form_Settings::OPTION_AUTO_PROVISION_DNS
                                    ),
                                    array_column(
                                        (new Modules_SpamexpertsExtension_Plesk_Domain_Collection)->getAliases(
                                            [
                                                'site-id' => $pleskDomain->getId()
                                            ]
                                        ),
                                        'name'
                                    ),
                                    $domainContactEmail
                                );
                            } catch (Exception $e) {
                                pm_Log::err("Failed to protect '{$newValues['Domain Alias Name']}' - " . $e->getMessage());
                            }
                        } else {
                            pm_Log::debug("Skipping '{$newValues['Domain Alias Name']}' protection in the {$objectType}/{$action} hook");
                        }

                        break;

                    case 'domain_alias_delete':
                    case 'site_alias_delete':
                        if (pm_Settings::get(Modules_SpamexpertsExtension_Form_Settings::OPTION_AUTO_DEL_DOMAINS)) {
                            pm_Log::debug("Starting '{$oldValues['Domain Alias Name']}' unprotection in the {$objectType}/{$action} hook");

                            try {
                                $pleskDomain = new Modules_SpamexpertsExtension_Plesk_Domain(
                                    $oldValues['Domain Alias Name']
                                );
                                $spamfilterDomain = new Modules_SpamexpertsExtension_SpamFilter_Domain($pleskDomain);
                                $spamfilterDomain->unprotect(false); // It's senseless to update DNS of deleted entity
                            } catch (Exception $e) {
                                pm_Log::err("Failed to protect '{$oldValues['Domain Alias Name']}' - " . $e->getMessage());
                            }
                        } else {
                            pm_Log::debug("Skipping '{$oldValues['Domain Alias Name']}' unprotection in the {$objectType}/{$action} hook");
                        }

                        break;
                }

                break;
        }
    }
}

return new Modules_SpamexpertsExtension_EventListener;
