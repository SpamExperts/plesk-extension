<?php

class EulaController extends pm_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->view->pageTitle = htmlentities(
            pm_Settings::get(Modules_SpamexpertsExtension_Form_Brand::OPTION_BRAND_NAME) ?: "SpamExperts Email Security",
            ENT_QUOTES,
            'UTF-8'
        );
    }

    public function indexAction()
    {
        if (!pm_Session::getClient()->isAdmin()) {
            throw new pm_Exception('Access denied');
        }

        $this->view->eulaUrl = 'https://www.n-able.com/legal/end-user-license-agreement';
        $this->view->privacyUrl = 'https://www.n-able.com/legal/privacy';

        $eulaAccepted = pm_Settings::get('eulaAccepted');

        $form = new pm_Form_Simple();
        if ((int)$eulaAccepted !== 1) {
            $form->addElement('description', 'msg', [
              'description' => 'You must accept the terms to use this extension. Please review them and click "Accept terms" to proceed.',
            ]);
            $form->addElement('submit', 'accept', [
              'label' => 'Accept terms',
              'attribs' => [ 'class' => 'btn action',  ]
            ]);
        } else {
            $form->addElement('description', 'msg', [
              'description' => 'You can revoke your acceptance of the terms at any time. If you revoke acceptance, you will no longer be able to use this extension until you accept the terms again.',
            ]);
            $form->addElement('submit', 'revoke', [
              'label' => 'Revoke Acceptance',
              'attribs' => [ 'class' => 'btn btn-danger', ],
            ]);
        }
        $form->addDisplayGroup(['accept', 'revoke'], 'buttons', [
          'decorators' => [
            'FormElements',
            ['HtmlTag', ['tag' => 'div', 'class' => 'btns-box']],
          ],
        ]);
        $form->setAction(pm_Context::getActionUrl('eula', 'update'));
        $this->view->form = $form;
    }

    public function updateAction()
    {
        $req = $this->getRequest();
        if ($req->isPost()) {
            if ($req->getPost('revoke')) {
                pm_Settings::set('eulaAccepted', 0);
                $this->_status->addWarning('Revoked acceptance');
                $this->_redirect('eula', [ 'exit' => true ]);
            } if ($req->getPost('accept')) {
                pm_Settings::set('eulaAccepted', 1);
                $this->_status->addInfo('Accepted terms');
                $this->_redirect('index', [ 'exit' => true ]);
            }
        }
        $this->_redirect('eula', [ 'exit' => true ]);
    }
}
