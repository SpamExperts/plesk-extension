<?php

require_once __DIR__ . '/../vendor/autoload.php';

class Modules_SpamexpertsExtension_SpamFilter_Api extends GuzzleHttp\Client
{
    /**
     * Class contstructor
     *
     * @return Modules_SpamexpertsExtension_SpamFilter_Api
     */
    public function __construct()
    {
        parent::__construct(
            [
                'base_uri' => "https://" . pm_Settings::get(Modules_SpamexpertsExtension_Form_Settings::OPTION_SPAMPANEL_API_HOST),
                'timeout' => 30,
                'allow_redirects' => false,
                'verify' => false,
                'headers' => [
                    'User-Agent' => "Professional SpamFilter Plesk/1.0",
                ],
                'auth' => [
                    pm_Settings::get(Modules_SpamexpertsExtension_Form_Settings::OPTION_SPAMPANEL_API_USER),
                    pm_Settings::get(Modules_SpamexpertsExtension_Form_Settings::OPTION_SPAMPANEL_API_PASS),
                ],
            ]
        );
    }

    public function addDomain($domain, $destinations = null, $aliases = null)
    {
        pm_Log::debug(__METHOD__ . ": " . "Domain addition request");

        try {
            $response = $this->call(
                "/api/domain/add/domain/$domain" .
                (is_array($destinations) ? "/destinations/" . json_encode($destinations) : "") .
                (is_array($aliases) ? "/aliases/" . json_encode($aliases) : "")
            );
            $result = stripos($response, 'added') !== false || stripos($response, 'already') !== false;
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
            $result = false;
        }

        pm_Log::debug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    public function removeDomain($domain)
    {
        pm_Log::debug(__METHOD__ . ": " . "Domain removal request");

        try {
            $response = $this->call('/api/domain/remove/domain/' . $domain);
            $result = stripos($response, 'removed') !== false;
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
            $result = false;
        }

        pm_Log::debug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    public function checkDomain($domain)
    {
        pm_Log::debug(__METHOD__ . ": " . "Domain protection check request");

        try {
            $response = $this->call("/api/domain/exists/domain/$domain");
            $result = (1 == json_decode($response, true)['present']);
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
            $result = false;
        }

        pm_Log::debug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    public function getRoutes($domain)
    {
        pm_Log::debug(__METHOD__ . ": " . "Domain get routes request");

        try {
            $response = $this->call("/api/domain/getroute/domain/$domain");
            $result = json_decode($response, true);
            if (is_array($result)) {
                foreach ($result as &$route) {
                    list($route, ) = explode(':', $route);
                }
            }
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
            $result = false;
        }

        pm_Log::debug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    public function setContact($domain, $email)
    {
        pm_Log::debug(__METHOD__ . ": " . "Domain admin contact set request");

        try {
            $response = $this->call("/api/domaincontact/set/domain/$domain/email/$email/");
            $result = stripos($response, 'changed to') !== false;
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
            $result = false;
        }

        pm_Log::debug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    ## Domain user

    public function addDomainUser($domain)
    {
        pm_Log::debug(__METHOD__ . ": " . "Domain user addition request");

        $password = substr(str_shuffle(md5(microtime())), 0, 10);

        try {
            $response = $this->call("/api/domainuser/add/domain/$domain/password/$password/email/contact@$domain");
            $result = stripos($response, 'saved') !== false || stripos($response, 'already') !== false;
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
            $result = false;
        }

        pm_Log::debug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    public function removeDomainUser($domain)
    {
        pm_Log::debug(__METHOD__ . ": " . "Domain user removal request");

        try {
            $response = $this->call("/api/domainuser/remove/username/$domain");
            $result = stripos($response, 'deleted') !== false || stripos($response, 'unable') !== false;
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
            $result = false;
        }

        pm_Log::debug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    public function checkDomainUser($domain)
    {
        pm_Log::debug(__METHOD__ . ": " . "Domain user protection check request");

        try {
            $response = $this->call("/api/user/get/username/$domain");
            if (!empty($response)) {
                $userData = json_decode($response, true);
                $result = !empty($userData['username'])
                    && strtolower($userData['username']) == strtolower($domain);
            } else {
                $result = false;
            }
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
            $result = false;
        }

        pm_Log::debug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    public function getAuthTicket($username, $logoutUrl = null)
    {
        pm_Log::debug(__METHOD__ . ": " . "Authentication ticket request");

        try {
            $url = "/api/authticket/create/username/$username/format/json/";
            if (!empty($logoutUrl)) {
                $url .= 'logouturl/' . base64_encode($logoutUrl) . '/';
            }
            $result = $response = $this->call($url);
            if ($jsonDecoded = json_decode($result, true)) {
                $result = $jsonDecoded['result'];
            }
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
            $result = null;
        }

        pm_Log::debug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    static final public function getRevision()
    {
        return (new self)->call('/api/version/get');
    }

    # Domain aliases

    public function addAlias($domain, $alias)
    {
        pm_Log::debug(__METHOD__ . ": " . "Domain alias add request");

        try {
            $response = $this->call("/api/domainalias/add/domain/" . idn_to_ascii($domain)
                . "/alias/" . idn_to_ascii($alias) . "/");
            $result = stripos($response, 'has been added') !== false;
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
            $result = false;
        }

        pm_Log::debug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    public function removeAlias($domain, $alias)
    {
        pm_Log::debug(__METHOD__ . ": " . "Domain alias remove request");

        try {
            $response = $this->call("/api/domainalias/remove/domain/" . idn_to_ascii($domain)
                . "/alias/" . idn_to_ascii($alias) . "/");
            $result = stripos($response, 'has been removed') !== false;
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
            $result = false;
        }

        pm_Log::debug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    public function aliasExists($domain, $alias)
    {
        pm_Log::debug(__METHOD__ . ": " . "Domain alias presence check request");

        try {
            $response = $this->call("/api/domainalias/list/domain/" . idn_to_ascii($domain) . "/");
            $allAliases = json_decode($response);
            $result = is_array($allAliases) && in_array($alias, $allAliases);
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
            $result = false;
        }

        pm_Log::debug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    /**
     * Method for sending requests to the SpamFilter API
     *
     * @param $url
     *
     * @return string
     */
    protected function call($url)
    {
        pm_Log::info("Making SpamFilter API request: $url");

        $response = (string) $this->get($url)->getBody();

        pm_Log::info("SpamFilter API response: $response");

        return $response;
    }

}
