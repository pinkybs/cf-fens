<?php

/**
 * Admin Manage Controller(modules/admin/controllers/Admin_ManageController.php)
 * Linno Admin Manage Controller
 *
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create    2009/03/20    zhangxin
 */
class Admin_ManageController extends MyLib_Zend_Controller_Action_Admin
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
     * manage controller index action
     *
     */
    public function indexAction()
    {
        $this->_forward('topmenu', 'manage', 'admin');
        return;
    }

    /**
     * manager controller top action
     *
     */
    public function topmenuAction()
    {
        $this->view->title = 'FENS様検索エンジン管理システム｜FENS';
        $this->render();
    }

    /**
     * manager controller list category action
     *
     */
    public function listcategoryAction()
    {
        $pageIndex = (int)$this->_request->getParam('pageIndex');
        $this->view->pageIndex = empty($pageIndex) ? 1 : $pageIndex;
        $this->view->title = 'FENS様検索エンジン管理システム｜FENS';
        $this->render();
    }

    /**
     * manager controller add category action
     *
     */
    public function addcategoryAction()
    {
        $pageIndex = (int)$this->_request->getParam('pageIndex');
        $this->view->pageIndex = $pageIndex;
        $this->view->title = 'FENS様検索エンジン管理システム｜FENS';
        $this->render();
    }

    /**
     * manager controller edit category action
     *
     */
    public function editcategoryAction()
    {
        $id = (int)$this->_request->getParam('id');
        $pageIndex = (int)$this->_request->getParam('pageIndex');

        require_once 'Admin/Dal/SiteCategory.php';
        $dalCate = Admin_Dal_SiteCategory::getDefaultInstance();
        $rowCate = $dalCate->getCategoryById($id);
        if (empty($rowCate)) {
            $this->_redirect($this->_baseUrl . '/manage/addcategory');
            return;
        }

        $this->view->cateInfo = $rowCate;
        $this->view->pageIndex = $pageIndex;
        $this->view->title = 'FENS様検索エンジン管理システム｜FENS';
        $this->render();
    }


    /**
     * manager controller list site action
     *
     */
    public function listsiteAction()
    {
        $pageIndex = (int)$this->_request->getParam('pageIndex');
        $hidSrhName = $this->_request->getParam('hidSrhName');
        $hidSrhUrl = $this->_request->getParam('hidSrhUrl');
        $hidSrhArea = (int)$this->_request->getParam('hidSrhArea');
        $hidSrhCate = (int)$this->_request->getParam('hidSrhCate');

        require_once 'Admin/Dal/SiteArea.php';
        require_once 'Admin/Dal/SiteCategory.php';
        $dalArea = Admin_Dal_SiteArea::getDefaultInstance();
        $dalCate = Admin_Dal_SiteCategory::getDefaultInstance();
        $this->view->lstArea = $dalArea->getAreaList(1, 1000);
        $this->view->lstCate = $dalCate->getCategoryList(1, 1000);

        $this->view->pageIndex = empty($pageIndex) ? 1 : $pageIndex;
        $this->view->hidSrhName = empty($hidSrhName) ? '' : $hidSrhName;
        $this->view->hidSrhUrl = empty($hidSrhUrl) ? '' : $hidSrhUrl;
        $this->view->hidSrhArea = empty($hidSrhArea) ? '' : $hidSrhArea;
        $this->view->hidSrhCate = empty($hidSrhCate) ? '' : $hidSrhCate;
        $this->view->title = 'FENS様検索エンジン管理システム｜FENS';
        $this->render();
    }

	/**
     * manager controller add site action
     *
     */
    public function addsiteAction()
    {
        $pageIndex = (int)$this->_request->getParam('pageIndex');
        $hidSrhName = $this->_request->getParam('hidSrhName');
        $hidSrhUrl = $this->_request->getParam('hidSrhUrl');
        $hidSrhArea = (int)$this->_request->getParam('hidSrhArea');
        $hidSrhCate = (int)$this->_request->getParam('hidSrhCate');

        require_once 'Admin/Dal/SiteArea.php';
        require_once 'Admin/Dal/SiteCategory.php';
        $dalArea = Admin_Dal_SiteArea::getDefaultInstance();
        $dalCate = Admin_Dal_SiteCategory::getDefaultInstance();
        $this->view->lstArea = $dalArea->getAreaList(1, 1000);
        $this->view->lstCate = $dalCate->getCategoryList(1, 1000);

        $this->view->pageIndex = $pageIndex;
        $this->view->hidSrhName = $hidSrhName;
        $this->view->hidSrhUrl = $hidSrhUrl;
        $this->view->hidSrhArea = $hidSrhArea;
        $this->view->hidSrhCate = $hidSrhCate;
        $this->view->title = 'FENS様検索エンジン管理システム｜FENS';
        $this->render();
    }

    /**
     * manager controller edit site action
     *
     */
    public function editsiteAction()
    {
        $id = (int)$this->_request->getParam('id');
        $pageIndex = (int)$this->_request->getParam('pageIndex');
        $hidSrhName = $this->_request->getParam('hidSrhName');
        $hidSrhUrl = $this->_request->getParam('hidSrhUrl');
        $hidSrhArea = (int)$this->_request->getParam('hidSrhArea');
        $hidSrhCate = (int)$this->_request->getParam('hidSrhCate');

        require_once 'Admin/Dal/Site.php';
        $dalSite = Admin_Dal_Site::getDefaultInstance();
        $rowSite = $dalSite->getSiteById($id);
        if (empty($rowSite)) {
            $this->_redirect($this->_baseUrl . '/manage/addsite');
            return;
        }

        require_once 'Admin/Dal/SiteArea.php';
        require_once 'Admin/Dal/SiteCategory.php';
        $dalArea = Admin_Dal_SiteArea::getDefaultInstance();
        $dalCate = Admin_Dal_SiteCategory::getDefaultInstance();
        $this->view->lstArea = $dalArea->getAreaList(1, 1000);
        $this->view->lstCate = $dalCate->getCategoryList(1, 1000);

        $this->view->siteInfo = $rowSite;
        $this->view->pageIndex = $pageIndex;
        $this->view->hidSrhName = $hidSrhName;
        $this->view->hidSrhUrl = $hidSrhUrl;
        $this->view->hidSrhArea = $hidSrhArea;
        $this->view->hidSrhCate = $hidSrhCate;
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