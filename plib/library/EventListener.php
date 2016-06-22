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
                        } else {
                            pm_Log::debug("Skipping '{$newValues['Domain Name']}' protection in the {$objectType}/{$action} hook");
                        }
                        
//                        $newValues =
//                        array (
//                            'Domain Name' => 'event-processing.test',
//                            'IP Address' => '138.201.53.58',
//                            'IPv6 Address' => '',
//                            'Domain GUID' => 'bf8372b1-58c8-453a-a50a-86ac5f6f3eb0',
//                            'Client GUID' => '773dfeca-9eef-468c-9684-549dc0ef88f6',
//                        )
                        
                        break;

                    case 'domain_delete':
                        if (pm_Settings::get(Modules_SpamexpertsExtension_Form_Settings::OPTION_AUTO_DEL_DOMAINS)) {
                            pm_Log::debug("Starting '{$oldValues['Domain Name']}' unprotection in the {$objectType}/{$action} hook");
                        } else {
                            pm_Log::debug("Skipping '{$oldValues['Domain Name']}' unprotection in the {$objectType}/{$action} hook");
                        }
                        
//                        $oldValues =
//                        array (
//                            'Domain Name' => 'event-processing.test',
//                            'Domain GUID' => 'bf8372b1-58c8-453a-a50a-86ac5f6f3eb0',
//                            'Login Name' => 'admin',
//                            'Client GUID' => '773dfeca-9eef-468c-9684-549dc0ef88f6',
//                        )

                        break;
                }
                
                break;
        }
    }
}

return new Modules_SpamexpertsExtension_EventListener;
