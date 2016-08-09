<?php

class Modules_SpamexpertsExtension_Plesk_Domain_Collection
{
    use Modules_SpamexpertsExtension_Plesk_ApiClientTrait;

    /**
     * Webspaces getter
     *
     * @return array
     */
    public function getWebspaces()
    {
        $list = [];

        $request = <<<APICALL
<webspace>
  <get>
    <filter></filter>
    <dataset>
      <gen_info></gen_info>
    </dataset>
  </get>
</webspace>
APICALL;
        $response = $this->xmlapi($request);

        foreach ($response->webspace->get->result as $domainInfo) {
            if ('ok' == $domainInfo->status && !empty($domainInfo->data)) {
                $asciiDomain = (string) $domainInfo->data->gen_info->{"ascii-name"};
                $list[] = [
                    'name' => $asciiDomain,
                    'type' => 'Webspace',
                ];
            }
        }

        return $list;
    }

    /**
     * Sites getter
     *
     * @return array
     */
    public function getSites()
    {
        $list = [];

        $request = <<<APICALL
<site>
  <get>
    <filter></filter>
    <dataset>
      <gen_info></gen_info>
    </dataset>
  </get>
</site>
APICALL;
        $response = $this->xmlapi($request);

        foreach ($response->site->get->result as $domainInfo) {
            if ('ok' == $domainInfo->status && !empty($domainInfo->data)) {
                $asciiDomain = (string) $domainInfo->data->gen_info->{"ascii-name"};
                $list[] = [
                    'name' => $asciiDomain,
                    'type' => 'Site',
                ];
            }
        }

        return $list;
    }


    /**
     * Aliases getter
     *
     * @param array $filter
     * 
     * @return array
     */
    public function getAliases(array $filter = null)
    {
        $list = [];

        $xmlFilter = '';
        if (null !== $filter) {
            foreach ($filter as $fName => $fValue) {
                $xmlFilter .= "<$fName>$fValue</$fName>";;
            }
        }

        $request = <<<APICALL
<site-alias>
  <get>
    <filter>$xmlFilter</filter>
  </get>
</site-alias>
APICALL;
        $response = $this->xmlapi($request);

        foreach ($response->{"site-alias"}->get->result as $domainInfo) {
            if ('ok' == $domainInfo->status && !empty($domainInfo->info)) {
                $asciiDomain = (string) $domainInfo->info->{"ascii-name"};
                $list[] = [
                    'name' => $asciiDomain,
                    'type' => 'Alias',
                ];
            }
        }

        return $list;
    }

}
