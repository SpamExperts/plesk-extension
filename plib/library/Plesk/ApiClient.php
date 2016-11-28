<?php

class Modules_SpamexpertsExtension_Plesk_ApiClient
{
    use Modules_SpamexpertsExtension_Plesk_ApiClientTrait;

    static final public function getVersion()
    {
        $api = new self;

        /** @noinspection PhpUndefinedFieldInspection */
        return (string) $api->xmlapi('<server><get><gen_info/></get></server>')['version'];
    }

    public function getDomains($resellerId)
    {
        $domains = [];

        $request = <<<APICALL
<customer>
    <get>
      <filter>
          <owner-id>$resellerId</owner-id>
      </filter>
      <dataset>
        <gen_info>
        </gen_info>
      </dataset>
    </get>
</customer>
APICALL;

        $response = $this->xmlapi($request);

        $filter = [];

        foreach ($response->customer->get->result as $customer) {
            $filter[] = "<get-domain-list><filter><id>".$customer->id->__toString()."</id></filter></get-domain-list>";
        }

        if (!empty($filter)) {
            $filter = implode("", $filter);

            $request = <<<APICALL
<customer>
    $filter
</customer>
APICALL;

            $response = $this->xmlapi($request);

            foreach ($response->customer->children() as $customerDomains) {
                foreach ($customerDomains->result->domains as $domain) {
                    foreach ($domain->children() as $item) {
                        $domains[] = [
                            "id" => $item->id->__toString(),
                            "name" => $item->name->__toString(),
                            "type" => $item->type->__toString()
                        ];
                    }
                }
            }
        }

        return $domains;
    }
}
