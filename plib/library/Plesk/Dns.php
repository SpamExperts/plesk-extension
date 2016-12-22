<?php

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 */
class Modules_SpamexpertsExtension_Plesk_Dns
{
    use Modules_SpamexpertsExtension_Plesk_ApiClientTrait;
    
    /**
     * MX records getter. It return an array of MX records with record ID as a key
     * and a record hostname as a value
     *
     * @param Modules_SpamexpertsExtension_Plesk_Domain $domain
     * @param bool $forceRaw
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getDomainsMxRecords(Modules_SpamexpertsExtension_Plesk_Domain $domain, $forceRaw = false)
    {
        $domainId = $domain->getId();

        try {
            $filter = $this->buildFilter($domain->getType(), $domainId);
        } catch (InvalidArgumentException $e) {
            return [];
        }

        $request = <<<APICALL
<dns>
 <get_rec>
  <filter>
   $filter
  </filter>
 </get_rec>
</dns>
APICALL;

        $response = $this->xmlapi($request);

        $records = [];
        
        // TODO: Sort records by MX priority

        $useIpAddresses = 0 < pm_Settings::get(
                Modules_SpamexpertsExtension_Form_Settings::OPTION_USE_IP_DESTINATION_ROUTES
            );

        /** @noinspection PhpUndefinedFieldInspection */
        if (!empty($response->dns->get_rec->result)) {
            /** @noinspection PhpUndefinedFieldInspection */
            foreach ($response->dns->get_rec->result as $rec) {
                if ('ok' == $rec->status && 'MX' == $rec->data->type) {
                    $mxHostname = (string) rtrim($rec->data->value, '.');

                    if (! $forceRaw && $useIpAddresses) {
                        pm_Log::debug("Obtaining IP address for '$mxHostname' ... ");

                        $mxIpaddress = gethostbyname($mxHostname);

                        if ($mxIpaddress == $mxHostname) { // IPv4 lookup has failed, let's try IPv6...
                            $ipv6Recs = dns_get_record($mxHostname, DNS_AAAA);
                            if (!empty($ipv6Recs[0]['ipv6'])) {
                                $mxIpaddress = $ipv6Recs[0]['ipv6'];
                            }
                        }

                        pm_Log::debug("'$mxHostname' resolves to '$mxIpaddress'");
                    }

                    $records[(int) $rec->id] = !empty($mxIpaddress) ? $mxIpaddress : $mxHostname;
                }
            }
        }

        return $records;
    }

    /**
     * DNS MX records setter. It replaces existing MX records with the provided ones
     *
     * @param Modules_SpamexpertsExtension_Plesk_Domain $domain
     * @param array $records
     *
     * @return void
     * 
     * @throws RuntimeException
     */
    public function replaceDomainsMxRecords(Modules_SpamexpertsExtension_Plesk_Domain $domain, array $records)
    {
        $domainId = $domain->getId();
        $obsoleteMXRecords = $this->getDomainsMxRecords($domain, true);

        // The remove existing records -> Add new ones does not work
        // because the panel does not allow to delete the last MX record
        // So we remember old records, add new ones and then delete the obsolete ones

        $filter = $this->buildFilter($domain->getType(), $domainId);

        $addRecordRequestTpl = <<<APICALL
<add_rec>
    $filter
    <type>MX</type>
    <host></host>
    <value>%s</value>
    <opt>%d</opt>
</add_rec>
APICALL;
        $bulkRecordsRequest = '';
        $priority = 10;
        foreach ($records as $rec) {
            $existingRecordIndex = array_search($rec, $obsoleteMXRecords);
            if (false === $existingRecordIndex) {
                $bulkRecordsRequest .= sprintf($addRecordRequestTpl, $rec, $priority);
                $priority += 10;

                continue;
            }

            unset($obsoleteMXRecords[$existingRecordIndex]);
        }

        if (!empty($bulkRecordsRequest)) {
            $response = $this->xmlapi("<dns>{$bulkRecordsRequest}</dns>");

            /** @noinspection PhpUndefinedFieldInspection */
            if ('ok' != $response->dns->add_rec->result->status) {
                /** @noinspection PhpUndefinedFieldInspection */
                throw new RuntimeException(
                    "Failed to add DMS MX records - "
                    . $response->dns->add_rec->result->errtext
                );
            }
        }

        if (!empty($obsoleteMXRecords)) {
            $deleteRecRequestTpl = <<<APICALL
<dns>
 <del_rec>
  <filter>
    <id>%s</id>
  </filter>
 </del_rec>
</dns>
APICALL;
            foreach (array_keys($obsoleteMXRecords) as $oldRecordId) {
                $response = $this->xmlapi(sprintf($deleteRecRequestTpl, $oldRecordId));
                /** @noinspection PhpUndefinedFieldInspection */
                if ('ok' != $response->dns->del_rec->result->status) {
                    /** @noinspection PhpUndefinedFieldInspection */
                    throw new RuntimeException(
                        "Failed to delete DMS MX record #{$oldRecordId} - "
                        . $response->dns->del_rec->result->errtext
                    );
                }
            }
        }
    }

    /**
     * Helper method to build filter expressions
     *
     * @param string $type
     * @param string $domainId
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected function buildFilter($type, $domainId)
    {
        switch ($type) {
            case Modules_SpamexpertsExtension_Plesk_Domain::TYPE_SITE:
            case Modules_SpamexpertsExtension_Plesk_Domain::TYPE_WEBSPACE:
            case Modules_SpamexpertsExtension_Plesk_Domain::TYPE_SUBDOMAIN:
                return "<site-id>$domainId</site-id>";

            case Modules_SpamexpertsExtension_Plesk_Domain::TYPE_ALIAS:
                return "<site-alias-id>$domainId</site-alias-id>";

            default:
                throw new InvalidArgumentException(sprintf("Unsupported domain type %s", $type));
        }
    }

}
