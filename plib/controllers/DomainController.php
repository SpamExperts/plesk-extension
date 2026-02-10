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
                        sprintf(
                            'Access denied to the domain %s',
                            htmlentities($pleskDomain->getDomain(), ENT_QUOTES, 'UTF-8')
                        )
                    );
                    $this->_forward('index', 'index');

                    return;
                }

                $checkerClass = $pleskDomain->getCheckerClassname();

                /** @var Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Abstract $checker */
                $checker = new $checkerClass(
                    $pleskDomain->getDomain(),
                    $pleskDomain->getType(),
                    $pleskDomain->getId()
                );

                $messages[] = $checker->execute()
                    ? [
                        'status' => 'info',
                        'content' => sprintf(
                            "Domain '%s' is protected",
                            htmlentities($pleskDomain->getDomain(), ENT_QUOTES, 'UTF-8')
                        ),
                    ]
                    : [
                        'status' => 'error',
                        'content' => sprintf(
                            "Domain '%s' is NOT protected",
                            htmlentities($pleskDomain->getDomain(), ENT_QUOTES, 'UTF-8')
                        ),
                    ];
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
                            sprintf(
                                'Access denied to the domain %s',
                                htmlentities($pleskDomain->getDomain(), ENT_QUOTES, 'UTF-8')
                            )
                        );
                        $this->_forward('index', 'index');

                        return;
                    }

                    $protectorClass = $pleskDomain->getProtectorClassname();

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
                            sprintf(
                                'Access denied to the domain %s',
                                htmlentities($pleskDomain->getDomain(), ENT_QUOTES, 'UTF-8')
                            )
                        );
                        $this->_forward('index', 'index');

                        return;
                    }

                    $unprotectorClass = $pleskDomain->getUnprotectorClassname();

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

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function loginAction()
    {
        $pageURL = 'https://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getRequestUri();

        $domain = $this->_getParam('domain');
        if (! empty($domain)) {
            $pleskDomain = new Modules_SpamexpertsExtension_Plesk_Domain($domain);

            if (! $this->hasAccessToDomain($pleskDomain)) {
                $this->_status->addMessage('error', 'Access denied.');
                $this->_forward('index', 'index');

                return;
            }

            $seDomain = new Modules_SpamexpertsExtension_SpamFilter_Domain($pleskDomain);
            if (! $seDomain->status()) {
                if (! pm_Settings::get(Modules_SpamexpertsExtension_Form_Settings::OPTION_AUTO_ADD_DOMAIN_ON_LOGIN)) {
                    $protectorClass = $pleskDomain->getProtectorClassname();

                    /** @var Modules_SpamexpertsExtension_Plesk_Domain_Strategy_Abstract $protector */
                    $protector = new $protectorClass(
                        $pleskDomain->getDomain(),
                        $pleskDomain->getType(),
                        $pleskDomain->getId()
                    );

                    try {
                        $protector->execute();
                    } catch (Exception $e) {
                        $this->_status->addMessage('error', $e->getMessage());
                        $this->_forward('index', 'index');

                        return;
                    }
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

            $api = new Modules_SpamexpertsExtension_SpamFilter_Api();
            if (! $api->checkDomainUser($domain)) {
                $api->addDomainUser($domain);
            }

            $authToken = $api->getAuthTicket(
                $domain,
                0 < pm_Settings::get(
                    Modules_SpamexpertsExtension_Form_Settings::OPTION_LOGOUT_REDIRECT
                ) ? preg_replace('~/index.php.*$~', '/index.php', $pageURL) : null
            );

            if (!empty($authToken)) {
                $url = rtrim(
                    \Modules_SpamexpertsExtension_Form_Settings::getRuntimeConfigOption(
                        \Modules_SpamexpertsExtension_Form_Settings::OPTION_SPAMPANEL_URL
                    ),
                    '/'
                )
                . "/?authticket=$authToken";

                header("Location: $url");

                exit(0);
            }

            $this->_status->addMessage('error', 'Unable to obtain authentication token.');
            $this->_forward('index', 'index');
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
