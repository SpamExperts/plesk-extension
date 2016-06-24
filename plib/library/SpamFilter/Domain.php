<?php

class Modules_SpamexpertsExtension_SpamFilter_Domain
{
    /**
     * Plesk domain instance container
     *
     * @var Modules_SpamexpertsExtension_Plesk_Domain
     */
    protected $pleskDomain;

    /**
     * SpamFilter API client instance container
     * 
     * @var Modules_SpamexpertsExtension_SpamFilter_Api
     */
    protected $api;

    /**
     * Plesk DNS manager instance container
     *
     * @var Modules_SpamexpertsExtension_Plesk_Dns
     */
    protected $dns;

    /**
     * Class constructor
     *
     * @param Modules_SpamexpertsExtension_Plesk_Domain $domain
     * @param Modules_SpamexpertsExtension_SpamFilter_Api $api
     * @param Modules_SpamexpertsExtension_Plesk_Dns $dns
     *
     * @return Modules_SpamexpertsExtension_SpamFilter_Domain
     */
    public function __construct(Modules_SpamexpertsExtension_Plesk_Domain $domain,
                                Modules_SpamexpertsExtension_SpamFilter_Api $api = null,
                                Modules_SpamexpertsExtension_Plesk_Dns $dns = null)
    {
        $this->pleskDomain = $domain;

        if (null === $api) {
            $api = new Modules_SpamexpertsExtension_SpamFilter_Api;
        }
        $this->api = $api;

        if (null === $dns) {
            $dns = new Modules_SpamexpertsExtension_Plesk_Dns;
        }
        $this->dns = $dns;
    }

    /**
     * Domain status checker
     *
     * @return bool
     */
    public function status()
    {
        return $this->api->checkDomain($this->pleskDomain->getDomain());
    }

    /**
     * Implements a domain protection
     *
     * @param bool $updateDns
     *
     * @return void
     *
     * @throws RuntimeException
     */
    public function protect($updateDns = true)
    {
        if ($updateDns) {
            $spamfilterMx = $this->getSpamfilterMxs();
            if (empty($spamfilterMx)) {
                throw new RuntimeException("SpamFilter MX hostnames are not set");
            }
        }

        $domainAddOk = $this->api->addDomain(
            $this->pleskDomain->getDomain(),
            array_values($this->dns->getDomainsMxRecords($this->pleskDomain))
        );

        if ($domainAddOk && !empty($spamfilterMx)) {
            $this->dns->replaceDomainsMxRecords($this->pleskDomain, $spamfilterMx);
        }
    }

    /**
     * Implements a domain unprotection
     *
     * @param bool $updateDns
     *
     * @return void
     *
     * @throws RuntimeException
     */
    public function unprotect($updateDns = true)
    {
//        if ($updateDns) {
//            $spamfilterMx = $this->getSpamfilterMxs();
//            if (empty($spamfilterMx)) {
//                throw new RuntimeException("SpamFilter MX hostnames are not set");
//            }
//        }

        $domainRemoveOk = $this->api->removeDomain($this->pleskDomain->getDomain());

        if ($domainRemoveOk && $updateDns) {
            $this->dns->replaceDomainsMxRecords($this->pleskDomain, 
                ["mainmx.{$this->pleskDomain->getDomain()}", "fallbackmx.{$this->pleskDomain->getDomain()}"]);
        }
    }

    /**
     * Helper method to fetch SpamFilter MX hostnames which should be
     * set up in tyhe extension configuration
     *
     * @return array
     */
    protected function getSpamfilterMxs()
    {
        $mxs = [];

        for ($mx = 1; $mx <= 4; $mx++) {
            $record = pm_Settings::get(
                constant("Modules_SpamexpertsExtension_Form_Settings::OPTION_SPAMFILTER_MX{$mx}")
            );
            if (!empty($record)) {
                $mxs[] = $record;
            }
        }

        return $mxs;
    }

}
