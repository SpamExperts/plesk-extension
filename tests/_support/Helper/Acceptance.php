<?php

namespace Helper;

use Page\SpamExpertsEmailSecurityPage;

use Codeception\Util\Locator;
use GuzzleHttp\Client;
use PHPUnit\Framework\Assert;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Acceptance extends BaseHelper
{
    /**
     * Function used to check the success messages
     */
    public function seeSuccessMessage($message)
    {
        /*Wait for success alert to pop up*/
        $this->getWebDriver()->waitForElement(Locator::combine(
            SpamExpertsEmailSecurityPage::SUCCESS_ALERT_XPATH,
            SpamExpertsEmailSecurityPage::SUCCESS_ALERT_CSS));

        /*Check the success message*/
        $this->getWebDriver()->see($message, Locator::combine(
            SpamExpertsEmailSecurityPage::SUCCESS_ALERT_XPATH,
            SpamExpertsEmailSecurityPage::SUCCESS_ALERT_CSS));
    }

    /**
     * Function used to check the error messages
     */
    public function seeErrorMessage($message)
    {
        /*Wait for success alert to pop up*/
        $this->getWebDriver()->waitForElement(Locator::combine(
            SpamExpertsEmailSecurityPage::ERROR_ALERT_XPATH,
            SpamExpertsEmailSecurityPage::ERROR_ALERT_CSS));

        /*Check the success message*/
        $this->getWebDriver()->see($message, Locator::combine(
            SpamExpertsEmailSecurityPage::ERROR_ALERT_XPATH,
            SpamExpertsEmailSecurityPage::ERROR_ALERT_CSS));
    }

    /**
     * Function used to check the warning messages
     */
    public function seeWarningMessage($message)
    {
        /*Wait for success alert to pop up*/
        $this->getWebDriver()->waitForElement(Locator::combine(
            SpamExpertsEmailSecurityPage::WARNING_ALERT_XPATH,
            SpamExpertsEmailSecurityPage::WARNING_ALERT_CSS));

        /*Check the success message*/
        $this->getWebDriver()->see($message, Locator::combine(
            SpamExpertsEmailSecurityPage::WARNING_ALERT_XPATH,
            SpamExpertsEmailSecurityPage::WARNING_ALERT_CSS));
    }

    /**
     * Function used to get the MX records of a domain's Plesk DNS zone
     *
     * @param string $domain
     * @return array - record id => ['value' => host, 'priority' => int]
     */
    public function grabMxRecords($domain)
    {
        $response = $this->callPleskXmlApi(sprintf(
            '<dns><get_rec><filter><site-id>%d</site-id></filter></get_rec></dns>',
            $this->grabSiteId($domain)));

        $records = [];
        foreach ($response->dns->get_rec->result as $result) {
            if ('ok' == (string) $result->status && 'MX' == (string) $result->data->type) {
                $records[(int) $result->id] = [
                    'value' => rtrim((string) $result->data->value, '.'),
                    'priority' => (int) (string) $result->data->opt,
                ];
            }
        }

        return $records;
    }

    /**
     * Function used to replace the MX records of a domain's Plesk DNS zone.
     * New records are added before the old ones are deleted, because Plesk
     * does not allow deleting the last MX record of a zone.
     *
     * @param string $domain
     * @param array $records - list of ['value' => host, 'priority' => int]
     */
    public function haveMxRecords($domain, array $records)
    {
        $siteId = $this->grabSiteId($domain);
        $obsoleteRecords = $this->grabMxRecords($domain);

        foreach ($records as $record) {
            $existingRecordId = array_search($record, $obsoleteRecords);
            if (false !== $existingRecordId) {
                unset($obsoleteRecords[$existingRecordId]);
                continue;
            }

            $this->callPleskXmlApi(sprintf(
                '<dns><add_rec><site-id>%d</site-id><type>MX</type><host></host>'
                . '<value>%s</value><opt>%d</opt></add_rec></dns>',
                $siteId, htmlspecialchars($record['value']), $record['priority']));
        }

        foreach (array_keys($obsoleteRecords) as $recordId) {
            $this->callPleskXmlApi(sprintf(
                '<dns><del_rec><filter><id>%d</id></filter></del_rec></dns>', $recordId));
        }
    }

    /**
     * Function used to remove a domain from the SpamFilter, so that
     * a test starts with the domain not protected
     *
     * @param string $domain
     */
    public function removeDomainFromSpamFilter($domain)
    {
        $client = new Client(['verify' => false, 'http_errors' => false]);
        $response = $client->get(
            'https://' . \ExtensionConfig::getApiHostname() . "/api/domain/remove/domain/{$domain}/",
            ['auth' => [\ExtensionConfig::getApiUsername(), \ExtensionConfig::getApiPassword()]]);

        $this->debug("SpamFilter API domain/remove response: " . $response->getBody());

        /*The legacy API reports bad credentials with HTTP 200, so check the body*/
        if (false !== stripos($response->getBody(), 'API credentials provided are incorrect')) {
            Assert::fail("SpamFilter API rejected API_USERNAME/API_PASSWORD: " . $response->getBody());
        }
    }

    /**
     * Function used to get the Plesk site id of a domain
     *
     * @param string $domain
     * @return int
     */
    private function grabSiteId($domain)
    {
        $response = $this->callPleskXmlApi(sprintf(
            '<site><get><filter><name>%s</name></filter><dataset><gen_info/></dataset></get></site>',
            htmlspecialchars($domain)));

        if ('ok' != (string) $response->site->get->result->status) {
            Assert::fail("Plesk domain {$domain} was not found: " . $response->asXML());
        }

        return (int) $response->site->get->result->id;
    }

    /**
     * Function used to send a request to the Plesk XML API
     *
     * @param string $request - XML API operation, without the packet element
     * @return \SimpleXMLElement
     */
    private function callPleskXmlApi($request)
    {
        $url = parse_url(\ExtensionConfig::getPleskUrl());
        $client = new Client(['verify' => false]);
        $response = $client->post(
            sprintf('https://%s:%d/enterprise/control/agent.php',
                $url['host'], isset($url['port']) ? $url['port'] : 8443),
            [
                'headers' => [
                    'HTTP_AUTH_LOGIN' => \ExtensionConfig::getPleskUsername(),
                    'HTTP_AUTH_PASSWD' => \ExtensionConfig::getPleskPassword(),
                    'Content-Type' => 'text/xml',
                ],
                'body' => '<?xml version="1.0" encoding="UTF-8"?><packet>' . $request . '</packet>',
            ]);

        $xml = simplexml_load_string((string) $response->getBody());
        if (false === $xml || isset($xml->system)) {
            Assert::fail("Plesk XML API request failed: " . $response->getBody());
        }

        return $xml;
    }
}
