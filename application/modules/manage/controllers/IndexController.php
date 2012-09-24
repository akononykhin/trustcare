<?php

class IndexController extends ZendX_Controller_Action
{

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
    }


    
    public function settingsAction()
    {
        $form = $this->_getSettingsForm();

        $user = Zend_Registry::get("TrustCare_Registry_User")->getUser();

        if($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $errorMsg = Zend_Registry::get("Zend_Translate")->_("Internal Error");
                try {
                    $password = $form->getValue('password');
                    $c_password = $form->getValue('confirm_password');
                    if(!empty($password) && $password != $c_password) {
                        $errorMsg = Zend_Registry::get("Zend_Translate")->_("Passwords not match");
                        throw new Exception("");
                    }
                    
                    $user->setFullName($form->getValue("full_name"));
                    if(!empty($password)) {
                        $user->setPassword(md5($password));
                    }
                    $user->save();

                    $form->getElement("password")->setValue('');
                    $form->getElement("confirm_password")->setValue('');
                    $form->addError(Zend_Registry::get("Zend_Translate")->_("Saved"));
                }
                catch(Exception $ex) {
                    $form->addError($errorMsg);
                    $message = $ex->getMessage();
                    if(!empty($message)) {
                        $this->getLogger()->error($message);
                    }
                }
            }
        }
        else {
            $form->getElement("full_name")->setValue($user->getFullName());
        }

        $this->view->form = $form;
        $this->render('settings');
    }
    
    
    /**
     * @return Zend_Form
     */
    private function _getSettingsForm()
    {
        $form = new ZendX_Form();
        $form->setAction($this->getRequest()->getBaseUrl() . '/' . $this->getRequest()->getControllerName() . "/settings");
        $form->setMethod('post');

        $tabIndex = 1;
        $form->addElement('password', 'password', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Password"),
            'description'   => "",
            'tabindex'      => $tabIndex++,
        ));
        
        $form->addElement('password', 'confirm_password', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Confirm Password"),
            'description'   => "",
            'tabindex'      => $tabIndex++,
        ));
        
        $form->addElement('text', 'full_name', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Full Name"),
            'description'   => "",
            'tabindex'      => $tabIndex++,
        ));
        
        $form->addElement('submit', 'send', array(
            'label'     => Zend_Registry::get("Zend_Translate")->_("Save"),
            'tabindex'  => $tabIndex++,
        ));
                
        return $form;
    }
}

