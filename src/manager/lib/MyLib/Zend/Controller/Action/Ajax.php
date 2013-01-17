<?php

/** @see Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

/**
 * Admin Ajax Base Controller
 * admin user must login, identity not empty
 *
 * @package    MyLib_Zend_Controller
 * @subpackage Action
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2009/03/25     zhangxin
 */
class MyLib_Zend_Controller_Action_Ajax extends Zend_Controller_Action
{

    /**
     * current user
     * contain uid,uuid,name,email
     *
     * @var array
     */
    protected $_user;

    /**
     * initialize basic data
     * @return void
     */
    public function initData()
    {
        $controller = $this->getFrontController();
        $controller->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        $controller->setParam('noViewRenderer', true);

        require_once 'Admin/Bll/Auth.php';
        $auth = Admin_Bll_Auth::getAuthInstance();
        if (!$auth->hasIdentity()) {
    		$this->_request->setDispatched(true);
            echo 'Authority Denied! Please Login First!';
            exit();
        }

        //get user info
        $this->_user = Admin_Bll_Auth::getIdentity();

        if (empty($this->_user)) {
            $this->_request->setDispatched(true);
            echo 'Something Error Happened, not find such user!!';
            exit();
        }

    }

    /**
     * post-Initialize
     * called after parent::init method execution.
     * it can override
     * @return void
     */
    public function postInit()
    {
    }

    /**
     * initialize object
     * override
     * @return void
     */
    final function init()
    {
        $this->initData();
        parent::init();
        $this->postInit();
    }

    /**
     * proxy for undefined methods
     * override
     * @param string $methodName
     * @param array $args
     */
    public function __call($methodName, $args)
    {
        echo 'No This Method';
    }
}