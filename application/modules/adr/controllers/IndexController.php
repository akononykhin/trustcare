<?php

class Adr_IndexController extends ZendX_Controller_Action
{

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $this->getRedirector()->gotoSimpleAndExit("index", "reports");
    }

}

