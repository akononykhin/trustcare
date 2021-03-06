<?php
/**
 *
 * Alexey Kononykhin
 * alexey.kononykhin@gmail.com
 *
 */
 
 class ZendX_Layout_Controller_Action_Helper_Layout extends Zend_Layout_Controller_Action_Helper_Layout
 {
     public function init()
     {
         parent::init();
         
         $front = $this->getFrontController();
         $moduleName = $front->getRequest()->getModuleName();
         $this->getLayoutInstance()->setLayout($moduleName);
     }
 }
