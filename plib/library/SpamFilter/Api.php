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
                'timeout'  => 30,
                'allow_redirects' => false,
                'verify'          => false,
                'headers'         => [
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
            $response = (string) $this->get(
                "/api/domain/add/domain/$domain"  .
                (is_array($destinations) ? "/destinations/" . json_encode($destinations) : "") .
                (is_array($aliases)      ? "/aliases/"      . json_encode($aliases)      : "")
            )->getBody();
            $result = stripos($response, 'added') !== false || stripos($response, 'already') !== false;
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
//            $this->report->add($response, Report::ERROR);
            $result = false;
        }

        pm_Log::debug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    public function removeDomain($domain)
    {
        pm_Log::debug(__METHOD__ . ": " . "Domain removal request");

        try {
            $response = (string) $this->get('/api/domain/remove/domain/' . $domain)->getBody();
            $result = stripos($response, 'removed') !== false;
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
//            $this->report->add($response, Report::ERROR);
            $result = false;
        }

        pm_Log::debug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    public function checkDomain($domain)
    {
        pm_Log::debug(__METHOD__ . ": " . "Domain protection check request");

        try {
            $response = (string) $this->get("/api/domain/exists/domain/$domain")->getBody();
            $result = (1 == json_decode($response, true)['present']);
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
//            $this->report->add($response, Report::ERROR);
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
            $response = (string) $this->get("/api/domainuser/add/domain/$domain/password/$password/email/contact@$domain")->getBody();
            $result = stripos($response, 'saved') !== false || stripos($response, 'already') !== false;
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
//            $this->report->add($response, Report::ERROR);
            $result = false;
        }

        pm_Log::debug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    public function removeDomainUser($domain)
    {
        pm_Log::debug(__METHOD__ . ": " . "Domain user removal request");

        try {
            $response = (string) $this->get("/api/domainuser/remove/username/$domain")->getBody();
            $result = stripos($response, 'deleted') !== false || stripos($response, 'unable') !== false;
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
//            $this->report->add($response, Report::ERROR);
            $result = false;
        }

        pm_Log::debug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    public function checkDomainUser($domain)
    {
        pm_Log::debug(__METHOD__ . ": " . "Domain user protection check request");

        try {
            $response = (string) $this->get("/api/user/get/username/" . $domain)->getBody();
            if (!empty($response)) {
                $userData = json_decode($response, true);
                $result = !empty($userData['username'])
                    && strtolower($userData['username']) == strtolower($domain);
            } else {
                $result = false;
            }
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
//            $this->report->add($response, Report::ERROR);
            $result = false;
        }

        pm_Log::debug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }

    public function getAuthTicket($username)
    {
        pm_Log::debug(__METHOD__ . ": " . "Authentication ticket request");

        try {
            $response = (string) $this->get("/api/authticket/create/username/$username")->getBody();
            $result = $response;
        } catch (Exception $e) {
            $response = "Error: " . $e->getMessage() . " | Code: " . $e->getCode();
//            $this->report->add($response, Report::ERROR);
            $result = null;
        }

        pm_Log::debug(__METHOD__ . ": Result: " . var_export($result, true) . " Response: " . var_export($response, true));

        return $result;
    }
    
}
