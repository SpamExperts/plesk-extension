<?php

class DomainController extends pm_Controller_Action
{
    public function statusAction()
    {
        $messages = [];

        foreach ((array) $this->_getParam('ids') as $domain) {
            try {
                $pleskDomain = new Modules_SpamexpertsExtension_Plesk_Domain($domain);

                if (!$this->hasAccessToDomain($pleskDomain)) {
                    $this->_status->addMessage(
                        'error',
                        sprintf('Access denied to the domain %s',
                            htmlentities($pleskDomain->getDomain(), ENT_QUOTES, 'UTF-8'))
                    );
                    $this->_forward('index', 'index');

                    return;
                }

                $checkerClass =
                    Modules_SpamexpertsExtension_Plesk_Domain::TYPE_ALIAS == $pleskDomain->getType()
                        ? Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Status_Secondary::class
                        : Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Status_Primary::class;

                /** @var Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Abstract $checker */
                $checker = new $checkerClass(
                    $pleskDomain->getDomain(),
                    $pleskDomain->getType(),
                    $pleskDomain->getId()
                );

                if ($checker->execute()) {
                    $messages[] = [
                        'status' => 'info',
                        'content' => sprintf("Domain '%s' is protected",
                            htmlentities($pleskDomain->getDomain(), ENT_QUOTES, 'UTF-8')),
                    ];
                } else {
                    $messages[] = [
                        'status' => 'error',
                        'content' => sprintf("Domain '%s' is NOT protected",
                            htmlentities($pleskDomain->getDomain(), ENT_QUOTES, 'UTF-8')),
                    ];
                }
            } catch (Exception $e) {
                $messages[] = [
                    'status' => 'error',
                    'content' => $e->getMessage(),
                ];
            }
        }

        $this->_helper->json(['status' => 'success', 'statusMessages' => $messages]);
    }

    public function protectAction()
    {
        $messages = [];

        if ($this->_request->isPost()) {
            foreach ((array)$this->_getParam('ids') as $domain) {
                try {
                    $pleskDomain = new Modules_SpamexpertsExtension_Plesk_Domain($domain);

                    if (!$this->hasAccessToDomain($pleskDomain)) {
                        $this->_status->addMessage(
                            'error',
                            sprintf('Access denied to the domain %s',
                                htmlentities($pleskDomain->getDomain(), ENT_QUOTES, 'UTF-8'))
                        );
                        $this->_forward('index', 'index');

                        return;
                    }

                    $protectorClass =
                        Modules_SpamexpertsExtension_Plesk_Domain::TYPE_ALIAS == $pleskDomain->getType()
                            ? Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Protection_Secondary::class
                            : Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Protection_Primary::class;

                    /** @var Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Abstract $protector */
                    $protector = new $protectorClass(
                        $pleskDomain->getDomain(),
                        $pleskDomain->getType(),
                        $pleskDomain->getId()
                    );
                    $protector->execute();

                    $messages[] = [
                        'status' => 'info',
                        'content' => sprintf(
                            "Domain '%s' has been successfully protected",
                            htmlentities($pleskDomain->getDomain(), ENT_QUOTES, 'UTF-8')
                        ),
                    ];
                } catch (Modules_SpamexpertsExtension_Exception_IncorrectStatusException $e) {
                    $messages[] = [
                        'status' => 'warning',
                        'content' => $e->getMessage(),
                    ];
                } catch (Exception $e) {
                    $messages[] = [
                        'status' => 'error',
                        'content' => $e->getMessage(),
                    ];
                }
            }
        }

        $this->_helper->json(['status' => 'success', 'statusMessages' => $messages]);
    }

    public function unprotectAction()
    {
        $messages = [];

        if ($this->_request->isPost()) {
            foreach ((array)$this->_getParam('ids') as $domain) {
                try {
                    $pleskDomain = new Modules_SpamexpertsExtension_Plesk_Domain($domain);

                    if (!$this->hasAccessToDomain($pleskDomain)) {
                        $this->_status->addMessage(
                            'error',
                            sprintf('Access denied to the domain %s',
                                htmlentities($pleskDomain->getDomain(), ENT_QUOTES, 'UTF-8'))
                        );
                        $this->_forward('index', 'index');

                        return;
                    }

                    $unprotectorClass =
                        Modules_SpamexpertsExtension_Plesk_Domain::TYPE_ALIAS == $pleskDomain->getType()
                            ? Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Unprotection_Secondary::class
                            : Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Unprotection_Primary::class;

                    /** @var Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Abstract $unprotector */
                    $unprotector = new $unprotectorClass(
                            $pleskDomain->getDomain(),
                            $pleskDomain->getType(),
                            $pleskDomain->getId()
                        );
                    $unprotector->execute();

                    $messages[] = [
                        'status' => 'info',
                        'content' => sprintf(
                            "Domain '%s' has been successfully unprotected",
                            htmlentities($pleskDomain->getDomain(), ENT_QUOTES, 'UTF-8')
                        ),
                    ];
                } catch (Modules_SpamexpertsExtension_Exception_IncorrectStatusException $e) {
                    $messages[] = [
                        'status' => 'warning',
                        'content' => $e->getMessage(),
                    ];
                }  catch (Exception $e) {
                    $messages[] = [
                        'status' => 'error',
                        'content' => $e->getMessage(),
                    ];
                }
            }
        }

        $this->_helper->json(['status' => 'success', 'statusMessages' => $messages]);
    }

    public function loginAction()
    {
        $pageURL = 'http';
        if ('on' == $_SERVER["HTTPS"]) {
            $pageURL .= 's';
        }
        $pageURL .= '://' . $this->getRequest()->getHttpHost() . $_SERVER["REQUEST_URI"];

        $domain = $this->_getParam('domain');
        if (!empty($domain)) {
            $pleskDomain = new Modules_SpamexpertsExtension_Plesk_Domain($domain);

            if (!$this->hasAccessToDomain($pleskDomain)) {
                $this->_status->addMessage('error', 'Access denied.');
                $this->_forward('index', 'index');

                return;
            } else {
                $seDomain = new Modules_SpamexpertsExtension_SpamFilter_Domain($pleskDomain);
                if (! $seDomain->status()) {
                    if (! pm_Settings::get(Modules_SpamexpertsExtension_Form_Settings::OPTION_AUTO_ADD_DOMAIN_ON_LOGIN)) {
                        $protectorClass =
                            Modules_SpamexpertsExtension_Plesk_Domain::TYPE_ALIAS == $pleskDomain->getType()
                                ? Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Protection_Secondary::class
                                : Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Protection_Primary::class;

                        /** @var Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Abstract $protector */
                        $protector = new $protectorClass(
                            $pleskDomain->getDomain(),
                            $pleskDomain->getType(),
                            $pleskDomain->getId()
                        );
                        $protector->execute();
                    } else {
                        $this->_status->addMessage(
                            'error',
                            sprintf(
                                "Domain '%s' is not protected",
                                htmlentities($pleskDomain->getDomain(), ENT_QUOTES, 'UTF-8')
                            )
                        );
                        $this->_forward('index', 'index');

                        return;
                    }
                }

                $api = new Modules_SpamexpertsExtension_SpamFilter_Api;
                if (!$api->checkDomainUser($domain)) {
                    $api->addDomainUser($domain);
                }

                $authToken = $api->getAuthTicket(
                    $domain,
                    0 < pm_Settings::get(
                        Modules_SpamexpertsExtension_Form_Settings::OPTION_LOGOUT_REDIRECT
                    ) ? preg_replace('~/index.php.*$~', '/index.php', $pageURL) : null
                );

                if (!empty($authToken)) {
                    $url = rtrim(pm_Settings::get(Modules_SpamexpertsExtension_Form_Settings::OPTION_SPAMPANEL_URL), '/')
                        . "/?authticket=$authToken";

                    header("Location: $url");

                    exit(0);
                } else {
                    $this->_status->addMessage('error', 'Unable to obtain authentication token.');
                    $this->_forward('index', 'index');

                    return;
                }
            }
        }
    }

    private function hasAccessToDomain(Modules_SpamexpertsExtension_Plesk_Domain $domain)
    {
        $result = pm_Session::getClient()->hasAccessToDomain($domain->getId());

        if (!$result && $parentDomain = $domain->getParent()) {
            $result = pm_Session::getClient()->hasAccessToDomain($parentDomain->getId());
        }

        return $result;
    }

}
