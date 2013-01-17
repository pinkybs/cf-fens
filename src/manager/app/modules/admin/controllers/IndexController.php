<?php

/**
 * Admin Index Controller(modules/admin/controllers/Admin_IndexController.php)
 * Admin Index
 *
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create    2009/03/20    zhangxin
 */
class Admin_IndexController extends Zend_Controller_Action
{

    /**
     * page init
     *
     */
    function init()
    {
    }

    /**
     * admin index controller index action
     *
     */
    public function indexAction()
    {
        $this->_forward('login', 'auth');
    }
}