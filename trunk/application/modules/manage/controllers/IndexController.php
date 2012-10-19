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
        $pharmacyModel = TrustCare_Model_Pharmacy::find($user->getIdPharmacy());

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
                    
                    if(!empty($password)) {
                        $user->setPassword(md5($password));
                    }
                    $user->setFirstName($form->getValue("first_name"));
                    $user->setLastName($form->getValue("last_name"));
                    $user->setIdCountry($form->getValue("id_country"));
                    $user->setIdState($form->getValue("id_state"));
                    $user->setCity($form->getValue("city"));
                    $user->setAddress($form->getValue("address"));
                    $user->setZip($form->getValue("zip"));
                    $user->setPhone($form->getValue("phone"));
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
            $form->getElement("first_name")->setValue($user->first_name);
            $form->getElement("last_name")->setValue($user->last_name);
            $form->getElement("id_country")->setValue($user->id_country);
            $form->getElement("id_state")->setValue($user->id_state);
            $form->getElement("city")->setValue($user->city);
            $form->getElement("address")->setValue($user->address);
            $form->getElement("zip")->setValue($user->zip);
            $form->getElement("phone")->setValue($user->phone);
            
            if(!is_null($pharmacyModel)) {
                $form->getElement("pharmacy_name")->setValue($pharmacyModel->getName());
            }
        }

        $this->view->form = $form;
        $this->render('settings');
    }
    
    
    /**
     * @return Zend_Form
     */
    private function _getSettingsForm()
    {
        $countryList = array();
        $countryList[''] = '';
        $model = new TrustCare_Model_Country();
        foreach ($model->fetchAll() as $obj) {
            $countryList[$obj->getId()] = $obj->getName();
        }
        
        $stateList = array();
        $stateList[''] = '';
        $model = new TrustCare_Model_State();
        foreach ($model->fetchAll() as $obj) {
            $stateList[$obj->getId()] = $obj->getName();
        }
        
        
        $form = new ZendX_Form();
        $form->setMethod('post');

        $tabIndex = 1;
        $form->addElement('htmltext', 'pharmacy_name', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Pharmacy"),
            'description'   => '',
            'value'         => "",
            'tabindex'      => $tabIndex++,
            'required'      => false,
        ));
        $form->addElement('password', 'password', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Password"),
            'description'   => "",
            'tabindex'      => $tabIndex++,
            'required'		=> $isCreate
        ));
        $form->addElement('password', 'confirm_password', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Confirm Password"),
            'description'   => "",
            'tabindex'      => $tabIndex++,
        ));
        $form->addElement('text', 'first_name', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("First Name"),
            'description'   => "",
            'size'          => 32,
            'tabindex'      => $tabIndex++,
        ));
        $form->addElement('text', 'last_name', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Last Name"),
            'description'   => "",
            'size'          => 32,
            'tabindex'      => $tabIndex++,
        ));
        $form->addElement('select', 'id_country', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Country"),
            'tabindex'      => $tabIndex++,
            'required'      => false,
            'multioptions'  => $countryList,
            'description'   => '',
        ));
        $form->addElement('select', 'id_state', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("State"),
            'tabindex'      => $tabIndex++,
            'required'      => false,
            'multioptions'  => $stateList,
            'description'   => '',
        ));
        $form->addElement('text', 'city', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("City"),
            'description'   => "",
            'size'          => 16,
            'tabindex'      => $tabIndex++,
            'required'      => false
        ));
        $form->addElement('text', 'address', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Address"),
            'description'   => "",
            'size'          => 32,
            'tabindex'      => $tabIndex++,
            'required'      => false
        ));
        $form->addElement('text', 'zip', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Zip"),
            'description'   => "",
            'size'          => 16,
            'tabindex'      => $tabIndex++,
            'required'      => false
        ));
        $form->addElement('text', 'phone', array(
            'label'         => Zend_Registry::get("Zend_Translate")->_("Phone"),
            'description'   => "",
            'size'          => 16,
            'tabindex'      => $tabIndex++,
            'required'      => false
        ));
                
        $form->addElement('submit', 'send', array(
            'label'     => Zend_Registry::get("Zend_Translate")->_("Save"),
            'tabindex'  => $tabIndex++,
        ));
                
        return $form;
    }
}

