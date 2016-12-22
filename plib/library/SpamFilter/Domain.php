<?php

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 */
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
     * Domain status checker
     *
     * @return bool
     */
    public function statusAlias()
    {
        return $this->api->aliasExists(
            $this->pleskDomain->getParent()->getDomain(),
            $this->pleskDomain->getDomain()
        );
    }

    /**
     * Implements a domain protection
     *
     * @param bool   $updateDns
     * @param array  $aliases
     * @param string $contactEmail
     *
     * @return void
     *
     * @throws RuntimeException
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function protect($updateDns = true, array $aliases = [], $contactEmail = null)
    {
        if ($updateDns) {
            $spamfilterMx = $this->getSpamfilterMxs();
            if (empty($spamfilterMx)) {
                throw new RuntimeException("SpamFilter MX hostnames are not set");
            }
        }

        /**
         * Current MX records retrieval procedure improvement.
         * In some case the protection can be started before all DNS records are created.
         * To avoid such cases we check if a non-empty records set has been fetched
         * and if the set is empty we repeat the attempt after some delay.
         * The total delay cannot exceed 60 seconds.
         */
        $actualMxRecords = [];
        foreach ([1, 2, 5, 10, 17, 25] as $delay) {
            $actualMxRecords = array_values($this->dns->getDomainsMxRecords($this->pleskDomain));
            if (!empty($actualMxRecords)) {
                break;
            }
            sleep($delay);
        }

        $domainAddOk = $this->api->addDomain(
            $this->pleskDomain->getDomain(),
            $actualMxRecords,
            $aliases
        );

        if (! $domainAddOk) {
            throw new \RuntimeException(
                sprintf(
                    "Failed to add the domain '%s' into the spamfilter",
                    htmlentities($this->pleskDomain->getDomain(), ENT_QUOTES, 'UTF-8')
                )
            );
        }

        if ($domainAddOk && !empty($spamfilterMx)) {
            $this->dns->replaceDomainsMxRecords($this->pleskDomain, $spamfilterMx);
        }

        if ($domainAddOk && !empty($contactEmail)) {
            $this->api->setContact($this->pleskDomain->getDomain(), $contactEmail);
        }
    }

    /**
     * Implements a domain alias protection
     *
     * @param bool   $updateDns
     *
     * @return void
     *
     * @throws RuntimeException
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function protectAlias($updateDns = true)
    {
        if ($updateDns) {
            $spamfilterMx = $this->getSpamfilterMxs();
            if (empty($spamfilterMx)) {
                throw new RuntimeException("SpamFilter MX hostnames are not set");
            }
        }

        $domainAddOk = $this->api->addAlias(
            $this->pleskDomain->getParent()->getDomain(),
            $this->pleskDomain->getDomain()
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
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function unprotect($updateDns = true)
    {
        if ($updateDns) {
            $destinationRoutes = $this->api->getRoutes(
                $this->pleskDomain->getDomain()
            );
        }

        $domainRemoveOk = $this->api->removeDomain(
            $this->pleskDomain->getDomain()
        );

        if ($domainRemoveOk && !empty($destinationRoutes)) {
            $this->dns->replaceDomainsMxRecords(
                $this->pleskDomain,
                $destinationRoutes
            );
        }
    }

    /**
     * Implements a domain alias unprotection
     *
     * @param bool $updateDns
     *
     * @return void
     *
     * @throws RuntimeException
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function unprotectAlias($updateDns = true)
    {
        $parentDomain = $this->pleskDomain->getParent();

        if ($updateDns) {
            $destinationRoutes = $this->api->getRoutes(
                $parentDomain->getDomain()
            );
        }

        $domainRemoveOk = $this->api->removeAlias(
            $parentDomain->getDomain(),
            $this->pleskDomain->getDomain()
        );

        if ($domainRemoveOk && !empty($destinationRoutes)) {
            $this->dns->replaceDomainsMxRecords(
                $this->pleskDomain,
                $destinationRoutes
            );
        }
    }

    /**
     * Helper method to fetch SpamFilter MX hostnames which should be
     * set up in tyhe extension configuration
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function getSpamfilterMxs()
    {
        $mxs = [];

        for ($mx = 1; $mx <= 4; $mx++) {
            $record = \Modules_SpamexpertsExtension_Form_Settings::getRuntimeConfigOption(
                constant("Modules_SpamexpertsExtension_Form_Settings::OPTION_SPAMFILTER_MX{$mx}")
            );
            if (!empty($record)) {
                $mxs[] = $record;
            }
        }

        return $mxs;
    }

}
