<?php

class Modules_SpamexpertsExtension_Plesk_Dns
{
    use Modules_SpamexpertsExtension_Plesk_ApiClientTrait;
    
    /**
     * MX records getter. It return an array of MX records with record ID as a key
     * and a record hostname as a value
     *
     * @param Modules_SpamexpertsExtension_Plesk_Domain $domain
     *
     * @return array
     */
    public function getDomainsMxRecords(Modules_SpamexpertsExtension_Plesk_Domain $domain)
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

        /** @noinspection PhpUndefinedFieldInspection */
        if (!empty($response->dns->get_rec->result)) {
            /** @noinspection PhpUndefinedFieldInspection */
            foreach ($response->dns->get_rec->result as $rec) {
                if ('ok' == $rec->status && 'MX' == $rec->data->type) {
                    $records[(int) $rec->id] = (string) $rec->data->value;
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

        $deleteRecordRequestTemplate = <<<APICALL
<dns>
 <del_rec>
  <filter>
    <id>%s</id>
  </filter>
 </del_rec>
</dns>
APICALL;
        foreach ($this->getDomainsMxRecords($domain) as $oldRecordId => $oldRecordValue) {
            $response = $this->xmlapi(sprintf($deleteRecordRequestTemplate, $oldRecordId));
            /** @noinspection PhpUndefinedFieldInspection */
            if ('ok' != $response->dns->del_rec->result->status) {
                /** @noinspection PhpUndefinedFieldInspection */
                throw new RuntimeException(
                    "Failed to delete DMS MX record #{$oldRecordId} - "
                        . $response->dns->del_rec->result->errtext
                );
            }
        }

        $filter = $this->buildFilter($domain->getType(), $domainId);

        $addRecordRequestTemplate = <<<APICALL
<add_rec>
    $filter
    <type>MX</type>
    <host>{$domain->getDomain()}</host>
    <value>%s</value>
    <opt>%d</opt>
</add_rec>
APICALL;
        $bulkRecordsRequest = '';
        $priority = 10;
        foreach ($records as $rec) {
            $bulkRecordsRequest .= sprintf($addRecordRequestTemplate, $rec, $priority);
            $priority += 10;
        }

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
                return "<site-id>$domainId</site-id>";

            case Modules_SpamexpertsExtension_Plesk_Domain::TYPE_ALIAS:
                return "<site-alias-id>$domainId</site-alias-id>";

            default:
                throw new InvalidArgumentException(sprintf("Unsupported domain type %s", $type));
        }
    }

}
