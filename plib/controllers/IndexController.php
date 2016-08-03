<?php

class IndexController extends pm_Controller_Action
{
    public function init()
    {
        parent::init();

        // Init title for all actions
        $this->view->pageTitle = htmlentities(
            pm_Settings::get(Modules_SpamexpertsExtension_Form_Brand::OPTION_BRAND_NAME) ?: "Professional SpamFilter",
            ENT_QUOTES,
            'UTF-8'
        );

        // Init common tabs
        $tabs = [
            [
                'title' => "Domains",
                'action' => 'domains',
            ]
        ];

        if (pm_Session::getClient()->isAdmin()) {
            $tabs[] = [
                'title' => "Settings",
                'action' => 'settings',
            ];
            $tabs[] = [
                'title' => "Branding",
                'action' => 'branding',
            ];
            $tabs[] = [
                'title' => "Support",
                'action' => 'support',
            ];
        }
        
        // Init tabs for all actions
        $this->view->tabs = $tabs;
    }

    public function indexAction()
    {
        $this->_forward('domains');
    }

    public function settingsAction()
    {
        if (!pm_Session::getClient()->isAdmin()) {
            $this->accessDenied();
        }

        // Init form here
        $form = new Modules_SpamexpertsExtension_Form_Settings([]);
        if ($this->getRequest()->isPost()
            && $form->isValid($this->getRequest()->getPost())) {

            foreach ([
                $form::OPTION_SPAMPANEL_URL,
                $form::OPTION_SPAMPANEL_API_HOST,
                $form::OPTION_SPAMFILTER_MX1,
                $form::OPTION_SPAMFILTER_MX2,
                $form::OPTION_SPAMFILTER_MX3,
                $form::OPTION_SPAMFILTER_MX4,
                $form::OPTION_AUTO_ADD_DOMAINS,
                $form::OPTION_AUTO_DEL_DOMAINS,
                $form::OPTION_AUTO_PROVISION_DNS,
                $form::OPTION_AUTO_SET_CONTACT,
                $form::OPTION_EXTRA_DOMAINS_HANDLING,
                $form::OPTION_SKIP_REMOTE_DOMAINS,
                $form::OPTION_LOGOUT_REDIRECT,
                $form::OPTION_AUTO_ADD_DOMAIN_ON_LOGIN,
                $form::OPTION_USE_IP_DESTINATION_ROUTES,
            ] as $optionName) {
                pm_Settings::set($optionName, $form->getValue($optionName));
            }

            // API access details need special processing to avoid changing
            // without running the migration procedure
            foreach ([
                $form::OPTION_SPAMPANEL_API_USER,
                $form::OPTION_SPAMPANEL_API_PASS,
            ] as $protectedOptionName) {
                if (empty(pm_Settings::get($protectedOptionName))) {
                    pm_Settings::set($protectedOptionName, $form->getValue($protectedOptionName));
                }
            }

            $this->_status->addMessage('info', 'Configuration options were successfully saved.');
            $this->_helper->json(['redirect' => $this->_helper->url('settings')]);
        }

        $this->view->form = $form;
    }


    public function brandingAction()
    {
        if (!pm_Session::getClient()->isAdmin()) {
            $this->accessDenied();
        }

        $this->checkExtensionConfiguration();

        // Init form here
        $form = new Modules_SpamexpertsExtension_Form_Brand([]);

        if ($this->getRequest()->isPost()
            && $form->isValid($this->getRequest()->getPost())) {

            foreach ([
                $form::OPTION_BRAND_NAME,
                $form::OPTION_LOGO_URL,
            ] as $optionName) {
                $value = $form->getValue($optionName);
                if (null !== $value) {
                    pm_Settings::set($optionName, $value);
                }
            }

            $this->_status->addMessage('info', 'Configuration options were successfully saved.');
            $this->_helper->json(['redirect' => $this->_helper->url('branding')]);
        }

        $this->view->form = $form;
    }

    public function domainsAction()
    {
        $this->checkExtensionConfiguration();

        // List object for pm_View_Helper_RenderList
        $this->view->list = $this->_getDomainsList();
    }

    private function _getDomainsList()
    {
        $data = [];

        foreach (array_merge(
                     Modules_SpamexpertsExtension_Plesk_Domain::getWebspaces(),
                     Modules_SpamexpertsExtension_Plesk_Domain::getSites(),
                     Modules_SpamexpertsExtension_Plesk_Domain::getAliases()
                 ) as $info) {
            $data[$info['name']] = [
                'domain'     => $info['name'],
                'type'       => $info['type'],
                'login-link' => (('Alias' != $info['type'])
                    ? '<a target="_blank" href="' . $this->_helper->url('login', 'index', null, ['domain' => $info['name']])
                        . '" class="s-btn sb-login"><span>Manage in SpamFilter Panel</span></a>'
                    : ''),
            ];
        }

        $options = [
            'defaultSortField' => 'domain',
            'defaultSortDirection' => pm_View_List_Simple::SORT_DIR_UP,
        ];
        $list = new pm_View_List_Simple($this->view, $this->_request, $options);
        $list->setData($data);
        $list->setColumns([
            pm_View_List_Simple::COLUMN_SELECTION,
            'domain' => [
                'title' => 'Link',
                'noEscape' => true,
                'searchable' => true,
            ],
            'type' => [
                'title' => 'Type',
                'searchable' => false,
            ],
            'login-link' => [
                'title' => '',
                'noEscape' => true,
                'sortable' => false,
            ],
        ]);

        $listTools = [
            [
                'title' => 'Check Status',
                'description' => 'Check protection status of selected domains.',
                'class' => 'sb-status-selected',
                'execGroupOperation' => [
                    "url" => $this->_helper->url('status'),
                ],
            ],
        ];
        if (pm_Session::getClient()->isAdmin()
            || pm_Session::getClient()->isReseller()) {
            $listTools[] = [
                'title' => 'Protect',
                'description' => 'Add the selected domains to the SpamFilter and enable email filtering.',
                'class' => 'sb-protect-selected',
                'execGroupOperation' => [
                    "url" => $this->_helper->url('protect'),
                    "skipConfirmation" => false,
                    "locale" => [
                        "confirmOnGroupOperation" => "You are about to protect the selected domains. Continue?",
                    ],
                    "subtype" => "confirm",
                ],
            ];
            $listTools[] = [
                'title' => 'Unprotect',
                'description' => 'Remove the selected domains from the SpamFilter and disable email filtering.',
                'class' => 'sb-unprotect-selected',
                'execGroupOperation' => [
                    "url" => $this->_helper->url('unprotect'),
                    "skipConfirmation" => false,
                    "locale" => [
                        "confirmOnGroupOperation" => "You are about to unprotect the selected domains. Continue?",
                    ],
                    "subtype" => "delete",
                ],
            ];
        }
        $list->setTools($listTools);

        // Take into account listDataAction corresponds to the URL /list-data/
        $list->setDataUrl(['action' => 'list-data']);

        return $list;
    }

    public function listDataAction()
    {
        $list = $this->_getDomainsList();

        // Json data from pm_View_List_Simple
        $this->_helper->json($list->fetchData());
    }

    public function statusAction()
    {
        $messages = [];

        foreach ((array) $this->_getParam('ids') as $domain) {
            $pleskDomain = new Modules_SpamexpertsExtension_Plesk_Domain($domain);

            if (!pm_Session::getClient()->hasAccessToDomain($pleskDomain->getId())) {
                $this->_status->addMessage(
                    'error',
                    sprintf('Access denied to the domain %s.', htmlentities($domain, ENT_QUOTES, 'UTF-8'))
                );
                $this->_forward('domains');
            }

            $spamfilterDomain = new Modules_SpamexpertsExtension_SpamFilter_Domain($pleskDomain);

            $messages[] = [
                'status' => 'info',
                'content' => "Domain '{$domain}' is " . ($spamfilterDomain->status() ? '' : ' NOT ') . "protected",
            ];
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

                    if (!pm_Session::getClient()->hasAccessToDomain($pleskDomain->getId())) {
                        $this->_status->addMessage(
                            'error',
                            sprintf('Access denied to the domain %s.', htmlentities($domain, ENT_QUOTES, 'UTF-8'))
                        );
                        $this->_forward('domains');
                    }

                    $spamfilterDomain = new Modules_SpamexpertsExtension_SpamFilter_Domain($pleskDomain);
                    $spamfilterDomain->protect(
                        0 < pm_Settings::get(Modules_SpamexpertsExtension_Form_Settings::OPTION_AUTO_PROVISION_DNS)
                    );
                    $messages[] = [
                        'status' => 'info',
                        'content' => "Domain '{$domain}' has been successfully protected",
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

                    if (!pm_Session::getClient()->hasAccessToDomain($pleskDomain->getId())) {
                        $this->_status->addMessage(
                            'error',
                            sprintf('Access denied to the domain %s.', htmlentities($domain, ENT_QUOTES, 'UTF-8'))
                        );
                        $this->_forward('domains');
                    }

                    $spamfilterDomain = new Modules_SpamexpertsExtension_SpamFilter_Domain($pleskDomain);
                    $spamfilterDomain->unprotect(
                        0 < pm_Settings::get(Modules_SpamexpertsExtension_Form_Settings::OPTION_AUTO_PROVISION_DNS)
                    );
                    $messages[] = [
                        'status' => 'info',
                        'content' => "Domain '{$domain}' has been successfully unprotected",
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
    
    public function supportAction()
    {
        if (!pm_Session::getClient()->isAdmin()) {
            $this->accessDenied();
        }

        $this->checkExtensionConfiguration();
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
            if (!pm_Session::getClient()->hasAccessToDomain($pleskDomain->getId())) {
                $this->_status->addMessage('error', 'Access denied.');
                $this->_forward('domains');
            } else {
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
                    $this->_forward('domains');
                }
            }
        }
    }

    protected function accessDenied()
    {
        throw new pm_Exception('Access denied');
    }

    protected function checkExtensionConfiguration()
    {
        if (Modules_SpamexpertsExtension_Form_Settings::areEmpty()) {
            if (pm_Session::getClient()->isAdmin()) {
                $this->_status->addMessage(
                    'error',
                    'Extension is not configured yet. Please set up configuration options.'
                );
                $this->_forward('settings');
            } else {
                throw new pm_Exception(
                    'Extension is not configured yet. Please ask your system administrator to fix that.'
                );
            }
        }
    }

}
