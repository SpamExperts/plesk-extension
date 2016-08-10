<?php

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
     */
    public function __construct($domain, $type = null, $id = null)
    {
        $this->domain = $domain;
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
            } else {
                foreach ([self::TYPE_SITE, self::TYPE_WEBSPACE, self::TYPE_ALIAS, self::TYPE_SUBDOMAIN] as $type) {
                    $this->id = $this->getDomainId($this->domain, $type);
                    if (null !== $this->id) {
                        $this->type = $type;

                        break;
                    }
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
     */
    public function isLocal()
    {
        if (null === $this->isLocal) {
            $id = $this->getId();

            $filterName = 'site-id';
            if (self::TYPE_ALIAS == $this->getType()) {
                $filterName = 'site-alias-id';
            }

            $request = <<<APICALL
<dns>
 <get>
  <filter>
   <$filterName>$id</$filterName>
  </filter>
 </get>
</dns>
APICALL;

            $response = $this->xmlapi($request);

            if ('ok' == $response->dns->get->result->status) {
                $this->isLocal = 'enabled' == strtolower($response->dns->get->result->zone_status);
            }
        }

        return $this->isLocal;
    }

}
