<?php

/**
 * Admin Search Controller(modules/admin/controllers/Admin_SearchController.php)
 * Linno Admin System Search Controller
 *
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create    2009/03/26    zhangxin
 */
class Admin_SearchController extends MyLib_Zend_Controller_Action_Admin
{

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
     * search controller index action
     *
     */
    public function indexAction()
    {
        return $this->_forward('top', 'search', 'admin');
    }

    /**
     * sys Search controller top action
     *
     */
    public function topAction()
    {
        $this->view->title = 'FENS様検索エンジン管理システム｜FENS';
        $this->render();
    }

	/**
     * search controller keyword result action
     *
     */
    public function keywordresultAction()
    {
        $this->view->title = 'FENS様検索エンジン管理システム｜FENS';
        $this->render();
    }

	/**
     * search controller keyword rank action
     *
     */
    public function keywordrankAction()
    {
        //yesterday
        $this->view->yesDate = date('Y-m-d', strtotime((date('Ymd'))) - 86400);
        $this->view->title = 'FENS様検索エンジン管理システム｜FENS';
        $this->render();
    }

    /**
     * preDispatch
     *
     */
    function preDispatch()
    {
    }
}