<?php

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class Modules_SpamexpertsExtension_Plesk_Domain
{
    use Modules_SpamexpertsExtension_Plesk_ApiClientTrait;

    const TYPE_SITE = 'site';
    const TYPE_WEBSPACE = 'webspace';
    const TYPE_ALIAS = 'site-alias';
    const TYPE_SUBDOMAIN = 'subdomain';

    /**
     * Domain ASCII name
     *
     * @var string
     */
    protected $domain;

    /**
     * Domain type, possible types are:
     * webspace, site, site-alias, subdomain
     * 
     * @var string
     */
    protected $type;

    /**
     * Domain ID
     * 
     * @var int
     */
    protected $id;

    /**
     * Parent domain ID (actual for aliases only)
     *
     * @var int
     */
    protected $parentId;

    /**
     * @var bool
     */
    protected $isLocal = null;

    /**
     * Class constructor
     *
     * @param string $domain
     * @param string $type
     * @param string $id
     *
     * @return Modules_SpamexpertsExtension_Plesk_Domain
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function __construct($domain, $type = null, $id = null)
    {
        $this->domain = idn_to_utf8($domain);
        $this->type = $type;
        $this->id = $id;
    }

    /**
     * Domain ID getter
     *
     * @return int
     */
    public function getId()
    {
        if (null === $this->id) {
            if (null !== $this->type) {
                $this->id = $this->getDomainId($this->domain, $this->type);

                return $this->id;
            }

            foreach ([self::TYPE_SITE, self::TYPE_WEBSPACE, self::TYPE_ALIAS, self::TYPE_SUBDOMAIN] as $type) {
                $this->id = $this->getDomainId($this->domain, $type);
                if (null !== $this->id) {
                    $this->type = $type;

                    break;
                }
            }
        }

        return $this->id;
    }

    /**
     * Domain type getter
     *
     * @return string
     */
    public function getType()
    {
        if (null === $this->id) {

            // If the id field is empty then the type is also empty
            // Execute the id getter to init the domain type
            $this->getId();
        }

        return $this->type;
    }

    /**
     * Domain name getter
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domainName
     * @param string $type
     *
     * @return int
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    protected function getDomainId($domainName, $type)
    {
        $id = null;

        if (!in_array($type, [self::TYPE_SITE, self::TYPE_WEBSPACE, self::TYPE_ALIAS, self::TYPE_SUBDOMAIN])) {
            return $id;
        }

        switch ($type) {
            case self::TYPE_SITE:
            case self::TYPE_WEBSPACE:
                $request = <<<APICALL
<$type>
  <get>
    <filter>
      <name>{$domainName}</name>
    </filter>
    <dataset>
      <gen_info></gen_info>
    </dataset>
  </get>
</$type>
APICALL;
                break;

            case self::TYPE_ALIAS:
            case self::TYPE_SUBDOMAIN:
                $request = <<<APICALL
<$type>
  <get>
    <filter>
      <name>{$domainName}</name>
    </filter>
  </get>
</$type>
APICALL;
                break;

            default:
                return $id;
        }


        $response = $this->xmlapi($request);

        if ('ok' == $response->$type->get->result->status) {
            $id = (int) $response->$type->get->result->id;
        }

        return $id;
    }

    /**
     * Contact email getter
     *
     * @return string
     */
    public function getContactEmail()
    {
        $pmDomain = new pm_Domain($this->getId());
        $email = $pmDomain->getClient()->getProperty('email');

        return !empty($email) ? $email : null;
    }

    /**
     * Determines if a domain is "local" or "remote"
     * (i.e. it's DNS zone is hosted on the same server with the panel or
     * on an external server)
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function isLocal()
    {
        if (null === $this->isLocal) {
            $id = $this->getId();

            switch ($this->getType()) {
                case self::TYPE_WEBSPACE:
                    $request = <<<APICALL
<dns>
 <get>
  <filter>
   <site-id>$id</site-id>
  </filter>
 </get>
</dns>
APICALL;
                    $response = $this->xmlapi($request);
                    if ('ok' == $response->dns->get->result->status) {
                        $this->isLocal = ('enabled' == strtolower($response->dns->get->result->zone_status));
                    }

                    break;

                case self::TYPE_SITE:
                case self::TYPE_SUBDOMAIN:
                    $request = <<<APICALL
<dns>
 <get>
  <filter>
   <site-id>$id</site-id>
  </filter>
 </get>
</dns>
APICALL;
                    $response = $this->xmlapi($request);
                    if ('ok' == $response->dns->get->result->status) {
                        $zoneEnabled = 'enabled' == strtolower($response->dns->get->result->zone_status);
                        if (! $zoneEnabled && $parentDomain = $this->getParent()) {
                            $zoneEnabled = $parentDomain->isLocal();
                        }
                        $this->isLocal = $zoneEnabled;
                    }

                    break;

                case self::TYPE_ALIAS:
                    $request = <<<APICALL
<site-alias>
 <get>
  <filter>
   <name>{$this->getDomain()}</name>
  </filter>
 </get>
</site-alias>
APICALL;
                    $response = $this->xmlapi($request);
                    if ('ok' == $response->{'site-alias'}->get->result->status) {
                        if ('false' == ((string) $response->{'site-alias'}->get->result->info->{'manage-dns'})) {
                            $this->isLocal = $this->getParent()->isLocal();
                        } else {
                            $request = <<<APICALL
<dns>
 <get>
  <filter>
   <site-alias-id>$id</site-alias-id>
  </filter>
 </get>
</dns>
APICALL;
                            $response = $this->xmlapi($request);
                            if ('ok' == $response->dns->get->result->status) {
                                $this->isLocal = ('enabled' == strtolower($response->dns->get->result->zone_status));
                            }
                        }
                    }

                    break;
            }
        }

        return $this->isLocal;
    }

    /**
     * Returns parent domain descriptor for secondary domains
     *
     * @return Modules_SpamexpertsExtension_Plesk_Domain
     */
    public function getParent()
    {
        if (null === $this->parentId) {
            $aliasId = $this->getId();

            if (self::TYPE_ALIAS !== $this->type) {

                // Check if a given domain is a subddomain
                $request = <<<APICALL
<subdomain>
    <get>
       <filter>
            <name>{$this->getDomain()}</name>
       </filter>
    </get>
</subdomain>
APICALL;
                $response = $this->xmlapi($request);

                return (('ok' == $response->subdomain->get->result->status
                    && !empty($response->subdomain->get->result->data->parent))
                    ? new self($response->subdomain->get->result->data->parent)
                    : null);
            }

            $request = <<<APICALL
<site-alias>
  <get>
   <filter>
      <id>$aliasId</id>
   </filter>
  </get>
</site-alias>
APICALL;

            $response = $this->xmlapi($request);

            if ('ok' == $response->{"site-alias"}->get->result->status) {
                $this->parentId = (int) $response->{"site-alias"}->get->result->info->{"site-id"};
            }
        }

        $parentDomain = new pm_Domain($this->parentId);

        return new self($parentDomain->getName(), null, $this->parentId);
    }

    /**
     * Return the name of the class to execute a domain status check
     *
     * @return string
     */
    public function getCheckerClassname()
    {
        return Modules_SpamexpertsExtension_Plesk_Domain::TYPE_ALIAS == $this->getType()
            ? Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Status_Secondary::class
            : Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Status_Primary::class;
    }

    /**
     * Return the name of the class to execute a domain protection
     *
     * @return string
     */
    public function getProtectorClassname()
    {
        return Modules_SpamexpertsExtension_Plesk_Domain::TYPE_ALIAS == $this->getType()
            ? Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Protection_Secondary::class
            : Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Protection_Primary::class;
    }

    /**
     * Return the name of the class to execute a domain unprotection
     *
     * @return string
     */
    public function getUnprotectorClassname()
    {
        return Modules_SpamexpertsExtension_Plesk_Domain::TYPE_ALIAS == $this->getType()
            ? Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Unprotection_Secondary::class
            : Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Unprotection_Primary::class;
    }

}
