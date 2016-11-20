<?php
/**
 * 
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 * 
 */

class Portal_SignController extends ZendX_Controller_Action 
{
    /**
     * This controller is for login/logout so it's not necessary to check either user is authenticated or not
     *
     * @return bool
     */
    public function isAuthenticated()
    {
        return true;    
    }
    
    public function indexAction() 
    {
        $form = $this->_getSignForm();
        if($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $remember = $form->getValue("remember");
                if(!empty($remember)) {
                    Zend_Session::rememberMe(60*60*24*120);
                }
                $hasAccess = $this->_hasAccess($form->getValue('username'), $form->getValue('password'));
                if($hasAccess) {
                    $log = new TrustCare_Model_LogAccess(array('author' => Zend_Auth::getInstance()->getIdentity(),
                                       		                   'ip' => $_SERVER['REMOTE_ADDR'],
                                              		           'action' => "Logged in"));
                    $log->save();

                    $this->getHelper("Redirector")->gotoSimpleAndExit("index", "index");
                    return;
                }
                $form->addError(Zend_Registry::get("Zend_Translate")->_("Invalid Credentials"));
            }
        }
        $this->view->form = $form;
        $this->render('form');   
    }
    
    public function logoutAction()
    {
        $log = new TrustCare_Model_LogAccess(array('author' => Zend_Auth::getInstance()->getIdentity(),
		                                           'ip' => $_SERVER['REMOTE_ADDR'],
        		                                   'action' => "Logged out"));
        $log->save();

        Zend_Session::forgetMe();
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect($this->getRequest()->getBaseUrl() . '/' . $this->getRequest()->getModuleName() . '/' . $this->getRequest()->getControllerName() . "/index");
    }
    
    /**
     * @return Zend_Form
     */
    private function _getSignForm()
    {
        $form = new ZendX_Form();
        $form->setAction($this->getRequest()->getBaseUrl() . '/' . $this->getRequest()->getModuleName() . '/' . $this->getRequest()->getControllerName() . "/index");
        $form->setMethod('post');

        $tabIndex = 1;
        $login = $form->createElement('text', 'username', array('label' => Zend_Registry::get("Zend_Translate")->_("Login"),
                                                                   'tabindex' => $tabIndex++));
        $login->setRequired(true);
        
        $password = $form->createElement('password', 'password', array('label' => Zend_Registry::get("Zend_Translate")->_("Password"),
                                                                       'tabindex' => $tabIndex++));

        
        $remember = $form->createElement('checkbox', 'remember', array('label_comment' => Zend_Registry::get("Zend_Translate")->_("Remember me"),
                                                                       'tabindex' => $tabIndex++,
                                                                       'checked' => true));
        
        $signBtn = $form->createElement('submit', 'login', array('label' => Zend_Registry::get("Zend_Translate")->_("Sign In"),
                                                                 'tabindex' => $tabIndex++));
        
        $form->addElement($login);
        $form->addElement($password);
        $form->addElement($remember);
        $form->addElement($signBtn);
        
        return $form;
    }

    
    private function _hasAccess($login, $password)
    {
        $auth = Zend_Auth::getInstance();
        $authAdapter = new Zend_Auth_Adapter_DbTable(
                            Zend_Registry::get("dbAdapter"),
                            'user',
                            'login',
                            'password',
                            'MD5(?) and is_active=1'
                            );
        $authAdapter->setIdentity($login);
        $authAdapter->setCredential($password);

        $result = $auth->authenticate($authAdapter);

        if (!$result->isValid()) {
            return false;
        } else {
            return true;
        }
    }
}

