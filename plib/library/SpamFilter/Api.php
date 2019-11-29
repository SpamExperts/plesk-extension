<?php

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * @noinspection PhpUndefinedClassInspection
 *
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 */
class Modules_SpamexpertsExtension_SpamFilter_Api extends GuzzleHttp\Client
{
    /**
     * Class contstructor
     *
     * @return Modules_SpamexpertsExtension_SpamFilter_Api
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __construct()
    {
        /** @noinspection PhpUndefinedClassInspection */
        parent::__construct(
            [
                'base_uri' => "https://" . \Modules_SpamexpertsExtension_Form_Settings::getRuntimeConfigOption(
                    \Modules_SpamexpertsExtension_Form_Settings::OPTION_SPAMPANEL_API_HOST
                ),
                'timeout' => 30,
                'allow_redirects' => false,
                'verify' => false,
                'headers' => [
                    'User-Agent' => "SpamExperts Email Security Plesk/1.2",
                ],
                'auth' => [
                    \Modules_SpamexpertsExtension_Form_Settings::getRuntimeConfigOption(
                        \Modules_SpamexpertsExtension_Form_Settings::OPTION_SPAMPANEL_API_USER
                    ),
                    \Modules_SpamexpertsExtension_Form_Settings::getRuntimeConfigOption(
                        \Modules_SpamexpertsExtension_Form_Settings::OPTION_SPAMPANEL_API_PASS
                    ),
                ],
            ]
        );
    }

    public function addDomain($domain, array $destinations = [], array $aliases = [])
    {
        $this->logDebug(__METHOD__ . ": " . "Domain addition request");

        try {
            $domainAddResponseRaw = $this->call(
                "/api/domain/add/domain/$domain/format/json/" .
                (!empty($destinations) ? "destinations/" . json_encode($destinations) . '/' : "") .
                (!empty($aliases) ? "aliases/" . json_encode($aliases) . '/' : "")
            );
            $domainAddResponseData = json_decode($domainAddResponseRaw, true);
            $result = !empty($domainAddResponseData['messages']['success'])
                && in_array(
                    sprintf(
                        "Domain '%s' added",
                        function_exists('idn_to_utf8') ? idn_to_utf8($domain) : $domain
                    ),
                    $domainAddResponseData['messages']['success']
                );
        } catch (Exception $e) {
            $domainAddResponseRaw = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
            $result = false;
        }

        $this->logDebug(__METHOD__ . ": Result: " . var_export($result, true)
            . " Response: " . var_export($domainAddResponseRaw, true));

        return $result;
    }

    public function removeDomain($domain)
    {
        $this->logDebug(__METHOD__ . ": " . "Domain removal request");

        try {
            $response = $this->call("/api/domain/remove/domain/$domain/");
            $result = stripos($response, 'removed') !== false;
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
            $result = false;
        }

        $this->logDebug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    public function checkDomain($domain)
    {
        $this->logDebug(__METHOD__ . ": " . "Domain protection check request");

        try {
            $response = $this->call("/api/domain/exists/domain/$domain");
            $result = (1 == json_decode($response, true)['present']);
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
            $result = false;
        }

        $this->logDebug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    public function getRoutes($domain)
    {
        $this->logDebug(__METHOD__ . ": " . "Domain get routes request");

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

        $this->logDebug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    public function setContact($domain, $email)
    {
        $this->logDebug(__METHOD__ . ": " . "Domain admin contact set request");

        try {
            $response = $this->call("/api/domaincontact/set/domain/$domain/email/$email/");
            $result = stripos($response, 'changed to') !== false;
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
            $result = false;
        }

        $this->logDebug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    ## Domain user

    public function addDomainUser($domain)
    {
        $this->logDebug(__METHOD__ . ": " . "Domain user addition request");

        $password = substr(str_shuffle(md5(microtime())), 0, 10);

        try {
            $response = $this->call("/api/domainuser/add/domain/$domain/password/$password/email/contact@$domain");
            $result = stripos($response, 'saved') !== false || stripos($response, 'already') !== false;
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
            $result = false;
        }

        $this->logDebug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    public function removeDomainUser($domain)
    {
        $this->logDebug(__METHOD__ . ": " . "Domain user removal request");

        try {
            $response = $this->call("/api/domainuser/remove/username/$domain");
            $result = stripos($response, 'deleted') !== false || stripos($response, 'unable') !== false;
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
            $result = false;
        }

        $this->logDebug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    /**
     * @param string $domain
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function checkDomainUser($domain)
    {
        $this->logDebug(__METHOD__ . ": " . "Domain user protection check request");

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

        $this->logDebug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    public function getAuthTicket($username, $logoutUrl = null)
    {
        $this->logDebug(__METHOD__ . ": " . "Authentication ticket request");

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

        $this->logDebug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    static final public function getRevision()
    {
        $response = trim((new self)->call('/api/version/get'));

        return is_numeric($response) ? $response : 'UNKNOWN';
    }

    # Domain aliases

    public function addAlias($domain, $alias)
    {
        $this->logDebug(__METHOD__ . ": " . "Domain alias add request");

        try {
            $response = $this->call("/api/domainalias/add/domain/$domain/alias/$alias/");
            $result = stripos($response, 'has been added') !== false;
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
            $result = false;
        }

        $this->logDebug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    public function removeAlias($domain, $alias)
    {
        $this->logDebug(__METHOD__ . ": " . "Domain alias remove request");

        try {
            $response = $this->call("/api/domainalias/remove/domain/$domain/alias/$alias/");
            $result = stripos($response, 'has been removed') !== false;
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
            $result = false;
        }

        $this->logDebug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    public function aliasExists($domain, $alias)
    {
        $this->logDebug(__METHOD__ . ": " . "Domain alias presence check request");

        try {
            $response = $this->call("/api/domainalias/list/domain/$domain/");
            $allAliases = json_decode($response);
            $result = is_array($allAliases) && in_array($alias, $allAliases);
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
            $result = false;
        }

        $this->logDebug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    /**
     * Method for sending requests to the SpamFilter API
     *
     * @param $url
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function call($url)
    {
        pm_Log::info("Making SpamFilter API request: $url");

        try {
            $response = (string) $this->get($url)->getBody();
        } catch (\Exception $e) {
            pm_Log::err("SpamFilter API request error: " . $e->getMessage());
            $response = "";
        }

        pm_Log::info("SpamFilter API response: $response");

        return $response;
    }


    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function logDebug($msg)
    {
        pm_Log::debug($msg);
    }

}
