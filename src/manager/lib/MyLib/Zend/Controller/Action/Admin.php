<?php

/** @see Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

/**
 * Admin Base Controller
 * admin user must login, identity not empty
 *
 * @package    MyLib_Zend_Controller
 * @subpackage Action
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2009/03/20     zhangxin
 */
class MyLib_Zend_Controller_Action_Admin extends Zend_Controller_Action
{
    /**
     * base url of website
     * @var string
     */
    protected $_baseUrl;

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
        $this->_baseUrl = $this->_request->getBaseUrl();

        require_once 'Admin/Bll/Auth.php';
        $auth = Admin_Bll_Auth::getAuthInstance();
        if (!$auth->hasIdentity()) {
    		$this->_redirect($this->_baseUrl . '/auth/login');
    		return;
        }

        //get user
        $this->_user = Admin_Bll_Auth::getIdentity();
        if (empty($this->_user)) {
        	$this->_forward('notfound', 'Error', 'admin');
        	return;
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
     * initialize view render data
     * @return void
     */
    protected function renderData()
    {
        $this->view->baseUrl = $this->_baseUrl;
        $this->view->staticUrl = Zend_Registry::get('static');
        $this->view->version = Zend_Registry::get('version');
        $this->view->hostUrl = Zend_Registry::get('host');
        $this->view->adminUser = $this->_user;
    }

    /**
     * pre-Render
     * called before parent::render method.
     * it can override
     * @return void
     */
    public function preRender()
    {
    }

    /**
     * Render a view
     * override
     * @see Zend_Controller_Action::render()
     * @param string|null $action Defaults to action registered in request object
     * @param string|null $name Response object named path segment to use; defaults to null
     * @param bool $noController  Defaults to false; i.e. use controller name as subdir in which to search for view script
     * @return void
     */
    public function render($action = null, $name = null, $noController = false)
    {
        $this->renderData();
        $this->preRender();
        parent::render($action, $name, $noController);
    }

    /**
     * proxy for undefined methods
     * override
     * @param string $methodName
     */
    public function __call($methodName, $args)
    {
        return $this->_forward('notfound', 'error', 'admin');
    }
}