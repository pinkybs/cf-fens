<?php

/** @see Zend_Json */
require_once 'Zend/Json.php';

/**
 * Admin Manage Ajax Controller
 * Manage ajax operation
 *
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create    2009/03/25    zhangxin
 */
class Ajax_ManageController extends MyLib_Zend_Controller_Action_Ajax
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
     * site category list view
     *
     */
    public function listcategoryAction()
    {
        $pageIndex = (int)$this->_request->getPost('pageIndex', 1);
        $pageSize = (int)$this->_request->getPost('pageSize', 10);

        require_once 'Admin/Dal/Site.php';
        $dalSite = Admin_Dal_Site::getDefaultInstance();
        require_once 'Admin/Dal/SiteCategory.php';
        $dalCate = Admin_Dal_SiteCategory::getDefaultInstance();
        $result = $dalCate->getCategoryList($pageIndex, $pageSize);
        $count = (int)$dalCate->getCategoryListCount();
        foreach ($result as $key=>$itemCate) {
            //get category's site count
            $result[$key]['site_count'] = (int)$dalSite->getSiteCountByCategoryId($itemCate['id']);
        }

        $response = array('info' => $result, 'count' => $count);
        $response = Zend_Json::encode($response);
        echo $response;
    }

	/**
     * change category sort
     *
     */
    public function sortcategoryAction()
    {
        if ($this->_request->isPost()) {
            $upOrDown = (int)$this->_request->getPost('op');
            $id = (int)$this->_request->getPost('id');

            if (empty($upOrDown) || empty($id)) {
                echo 'false';
                return;
            }

            require_once 'Admin/Bll/SiteCategory.php';
            $bllCate = new Admin_Bll_SiteCategory();
            $result = false;
            //up sort
            if (1 == $upOrDown) {
                $result = $bllCate->upSortCategory($id);
            }
            //down sort
            else if (2 == $upOrDown) {
                $result = $bllCate->downSortCategory($id);
            }

            echo $result ? 'true' : 'false';
        }
    }

	/**
     * delete category
     *
     */
    public function delcategoryAction()
    {
        if ($this->_request->isPost()) {
            $id = (int)$this->_request->getPost('id');

            if (empty($id)) {
                echo 'false';
                return;
            }

            require_once 'Admin/Bll/SiteCategory.php';
            $bllCate = new Admin_Bll_SiteCategory();
            $result = $bllCate->delCategory($id);

            echo $result ? 'true' : 'false';
        }
    }

	/**
     * add category
     *
     */
    public function addcategoryAction()
    {
        if ($this->_request->isPost()) {
            $txtName = $this->_request->getPost('txtName');

            if (empty($txtName) || mb_strlen($txtName, 'UTF-8') > 50) {
                echo 'false';
                return;
            }

            require_once 'Admin/Bll/SiteCategory.php';
            $bllCate = new Admin_Bll_SiteCategory();
            $result = $bllCate->addCategory(array('name' => $txtName));

            echo $result ? 'true' : 'false';
        }
    }

	/**
     * edit category
     *
     */
    public function editcategoryAction()
    {
        if ($this->_request->isPost()) {
            $txtName = $this->_request->getPost('txtName');
            $id = $this->_request->getPost('id');

            if (empty($id) || empty($txtName) || mb_strlen($txtName, 'UTF-8') > 50) {
                echo 'false';
                return;
            }

            require_once 'Admin/Bll/SiteCategory.php';
            $bllCate = new Admin_Bll_SiteCategory();
            $result = $bllCate->editCategory(array('name' => $txtName), $id);

            echo $result ? 'true' : 'false';
        }
    }

	/**
     * site list view
     *
     */
    public function listsiteAction()
    {
        $pageIndex = (int)$this->_request->getPost('pageIndex', 1);
        $pageSize = (int)$this->_request->getPost('pageSize', 10);
        $srhCate = $this->_request->getPost('srhCate');
        $srhArea = $this->_request->getPost('srhArea');
        $srhName = $this->_request->getPost('srhName');
        $srhUrl = $this->_request->getPost('srhUrl');

        require_once 'Admin/Dal/Site.php';
        $dalSite = Admin_Dal_Site::getDefaultInstance();
        require_once 'Admin/Dal/SiteArea.php';
        $dalArea = Admin_Dal_SiteArea::getDefaultInstance();
        require_once 'Admin/Dal/SiteCategory.php';
        $dalCate = Admin_Dal_SiteCategory::getDefaultInstance();
        require_once 'Admin/Dal/SitePage.php';
        $dalPage = Admin_Dal_SitePage::getDefaultInstance();

        $result = $dalSite->getSiteListByFilter($pageIndex, $pageSize, $srhName, $srhUrl, $srhArea, $srhCate);
        $count = (int)$dalSite->getSiteListCountByFilter($srhName, $srhUrl, $srhArea, $srhCate);
        foreach ($result as $key=>$itemSite) {
            $rowArea = $dalArea->getAreaById($itemSite['area_id']);
            $rowCate = $dalCate->getCategoryById($itemSite['category_id']);
            $result[$key]['area_name'] = empty($rowArea) ? '' : $rowArea['name'];
            $result[$key]['cate_name'] = empty($rowCate) ? '' : $rowCate['name'];

            //$result[$key]['page_count'] = $dalPage->getPageCountBySiteId($itemSite['id']);
            //$result[$key]['last_crawling_time'] = empty($result[$key]['page_count']) ? '' : $dalPage->getLastCrawlTimeBySiteId($itemSite['id']);
        }

        $response = array('info' => $result, 'count' => $count);
        $response = Zend_Json::encode($response);
        echo $response;
    }

	/**
     * delete site
     *
     */
    public function delsiteAction()
    {
        if ($this->_request->isPost()) {
            $id = (int)$this->_request->getPost('id');

            if (empty($id)) {
                echo 'false';
                return;
            }

            require_once 'Admin/Bll/Site.php';
            $bllSite = new Admin_Bll_Site();
            $result = $bllSite->delSite($id);

            echo $result ? 'true' : 'false';
        }
    }

	/**
     * add site
     *
     */
    public function addsiteAction()
    {
        if ($this->_request->isPost()) {
            $txtName = $this->_request->getPost('txtName');
            $txtUrl = $this->_request->getPost('txtUrl');
            $txtToppage = $this->_request->getPost('txtToppage');
            $selArea = (int)$this->_request->getPost('selArea');
            $selCate = (int)$this->_request->getPost('selCate');

            if (empty($txtName) || empty($txtUrl) //|| empty($txtToppage)
                || mb_strlen($txtName, 'UTF-8') > 100 || mb_strlen($txtUrl, 'UTF-8') > 200) {
                echo 'false';
                return;
            }

            require_once 'Admin/Bll/Site.php';
            $bllSite = new Admin_Bll_Site();
            $aryInfo = array();
            $aryInfo['name'] = $txtName;
            if (substr($txtUrl, -1, 1) != '/') {
                $txtUrl .= '/';
            }

            //valide url check
            require_once 'Zend/Uri.php';
            $uri = Zend_Uri::factory('http://' . $txtUrl . $txtToppage);
            if (!$uri->valid()) {
                echo 'invalid_url';
                return;
            }
            require_once 'Zend/Http/Client.php';
            $client = new Zend_Http_Client($txtUrl . $txtToppage, array(
                'maxredirects' => 5,
                'timeout'      => 10));
            $isresponse = $client->request()->isSuccessful();

            if (!$isresponse) {
                echo 'invalid_url';
                return;
            }

            $aryInfo['url'] = $txtUrl;
            $aryInfo['toppage'] = $txtToppage;
            $aryInfo['area_id'] = $selArea;
            $aryInfo['category_id'] = $selCate;
            $result = $bllSite->addSite($aryInfo);

            echo $result ? 'true' : 'false';
        }
    }

	/**
     * edit site
     *
     */
    public function editsiteAction()
    {
        if ($this->_request->isPost()) {
            $txtName = $this->_request->getPost('txtName');
            $txtUrl = $this->_request->getPost('txtUrl');
            $txtToppage = $this->_request->getPost('txtToppage');
            $selArea = (int)$this->_request->getPost('selArea');
            $selCate = (int)$this->_request->getPost('selCate');
            $id = $this->_request->getPost('id');

            if (empty($txtName) || empty($txtUrl) //|| empty($txtToppage)
                || mb_strlen($txtName, 'UTF-8') > 100 || mb_strlen($txtUrl, 'UTF-8') > 200) {
                echo 'false';
                return;
            }

            require_once 'Admin/Bll/Site.php';
            $bllSite = new Admin_Bll_Site();
            $aryInfo = array();
            $aryInfo['name'] = $txtName;
            if (substr($txtUrl, -1, 1) != '/') {
                $txtUrl .= '/';
            }

            //valide url check
            require_once 'Zend/Uri.php';
            $uri = Zend_Uri::factory($txtUrl . $txtToppage);
            if (!$uri->valid()) {
                echo 'invalid_url';
                return;
            }
            require_once 'Zend/Http/Client.php';
            $client = new Zend_Http_Client($txtUrl . $txtToppage, array(
                'maxredirects' => 0,
                'timeout'      => 10));
            $isresponse = $client->request()->isSuccessful();

            if (!$isresponse) {
                echo 'invalid_url';
                return;
            }

            $aryInfo['url'] = $txtUrl;
            $aryInfo['toppage'] = $txtToppage;
            $aryInfo['area_id'] = $selArea;
            $aryInfo['category_id'] = $selCate;
            $result = $bllSite->editSite($aryInfo, $id);

            echo $result ? 'true' : 'false';
        }
    }

    /**
     * check is validate admin user before action
     *
     */
    function preDispatch()
    {

    }
}