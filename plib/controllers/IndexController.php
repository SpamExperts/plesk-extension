<?php

class IndexController extends pm_Controller_Action
{
    public function init()
    {
        parent::init();

        // Init title for all actions
        $this->view->pageTitle = "Professional SpamFilter";

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
        // Init form here
        $form = new Modules_SpamexpertsExtension_SettingsForm([]);
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

    public function domainsAction()
    {
        // List object for pm_View_Helper_RenderList
        $this->view->list = $this->_getDomainsList();
    }

    private function _getDomainsList()
    {
        $request = <<<APICALL
<webspace>
  <get>
    <filter></filter>
    <dataset>
      <gen_info></gen_info>
    </dataset>
  </get>
</webspace>
APICALL;
        $response = pm_ApiRpc::getService()->call($request);

        $data = [];
        $index = 1;
        foreach ($response->webspace->get->result as $domainInfo) {
            if ('ok' == $domainInfo->status) {
                $data[$index++] = [
                    'domain'   => (string) $domainInfo->data->gen_info->{"ascii-name"},
                    'login-link' => '<a href="#" class="s-btn sb-login"><span>Manage in SpamFilter Panel</span></a>',
                ];
            }
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
                'execGroupOperation' => $this->_helper->url('status'),
            ],
        ];
        if (pm_Session::getClient()->isAdmin()
            || pm_Session::getClient()->isReseller()) {
            $listTools[] = [
                'title' => 'Protect',
                'description' => 'Add the selected domains to the SpamFilter and enable email filtering.',
                'class' => 'sb-protect-selected',
                'execGroupOperation' => $this->_helper->url('protect'),
            ];
            $listTools[] = [
                'title' => 'Unprotect',
                'description' => 'Remove the selected domains from the SpamFilter and disable email filtering.',
                'class' => 'sb-unprotect-selected',
                'execGroupOperation' => $this->_helper->url('unprotect'),
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
        foreach ((array) $this->_getParam('ids') as $id) {
            $messages[] = ['status' => 'info', 'content' => "Row #$id was successfully removed."];
        }
        $this->_helper->json(['status' => 'success', 'statusMessages' => $messages]);
    }

    public function protectAction()
    {
        $messages = [];
        foreach ((array) $this->_getParam('ids') as $id) {
            $messages[] = ['status' => 'info', 'content' => "Row #$id was successfully removed."];
        }
        $this->_helper->json(['status' => 'success', 'statusMessages' => $messages]);
    }

    public function unprotectAction()
    {
        $messages = [];
        foreach ((array) $this->_getParam('ids') as $id) {
            $messages[] = ['status' => 'info', 'content' => "Row #$id was successfully removed."];
        }
        $this->_helper->json(['status' => 'success', 'statusMessages' => $messages]);
    }

}
