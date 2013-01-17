<?php

/** @see Zend_Json */
require_once 'Zend/Json.php';

/**
 * Admin Search Ajax Controller
 * Search ajax operation
 *
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create    2009/03/26    zhangxin
 */
class Ajax_SearchController extends MyLib_Zend_Controller_Action_Ajax
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
     * fixed site page list view
     *
     */
    public function listfixedpageAction()
    {
        $pageIndex = (int)$this->_request->getPost('pageIndex', 1);
        $pageSize = (int)$this->_request->getPost('pageSize', 10);
        $srhFixedKeyword = $this->_request->getPost('srhFixedKeyword');
        $srhFixedKeyword = trim($srhFixedKeyword);

        require_once 'Admin/Dal/SitePage.php';
        $dalPage = Admin_Dal_SitePage::getDefaultInstance();
        $result = $dalPage->getFixedPageListByKeyword($pageIndex, $pageSize, $srhFixedKeyword);
        $count = (int)$dalPage->getFixedPageListCountByKeyword($srhFixedKeyword);
        foreach ($result as $key=>$itemSite) {
            //get page info
            $rowPage = $dalPage->getPageById($itemSite['page_id']);
            $result[$key]['page_url'] = empty($rowPage) ? '' : $rowPage['url'];
            $result[$key]['page_title'] = empty($rowPage) ? '' : $rowPage['title'];
        }

        $response = array('info' => $result, 'count' => $count);
        $response = Zend_Json::encode($response);
        echo $response;
    }

	/**
     * site page list view
     *
     */
    public function listpageAction()
    {
        $pageIndex = (int)$this->_request->getPost('pageIndex', 1);
        $pageSize = (int)$this->_request->getPost('pageSize', 10);
        $srhKeyword = $this->_request->getPost('srhKeyword');
        $srhFixedKeyword = $this->_request->getPost('srhFixedKeyword');
        $srhKeyword = trim($srhKeyword);
        $srhFixedKeyword = trim($srhFixedKeyword);

        require_once 'Admin/Dal/SitePage.php';
        $dalPage = Admin_Dal_SitePage::getDefaultInstance();

        $result = $dalPage->getPageListByKeyword($pageIndex, $pageSize, empty($srhKeyword) ? $srhFixedKeyword : $srhKeyword);
        $count = (int)$dalPage->getPageListCountByKeyword(empty($srhKeyword) ? $srhFixedKeyword : $srhKeyword);
        foreach ($result as $key=>$itemSite) {
            $result[$key]['last_update_time'] = empty($itemSite['create_time']) ? '' : date('Y-m-d H:i:s', $itemSite['create_time']);
            $rowExist = $dalPage->getFixedPageByPageIdAndKeyword($itemSite['id'], $srhFixedKeyword);
            $result[$key]['is_fixed'] = 0;
            if (!empty($rowExist)) {
                $result[$key]['is_fixed'] = 1;
            }
        }
        $response = array('info' => $result, 'count' => $count);
        $response = Zend_Json::encode($response);

        echo $response;
    }


	/**
     * remove fixed page
     *
     */
    public function removepageAction()
    {
        if ($this->_request->isPost()) {
            $id = (int)$this->_request->getPost('id');

            if (empty($id)) {
                echo 'false';
                return;
            }

            require_once 'Admin/Bll/SitePage.php';
            $bllPage = new Admin_Bll_SitePage();
            $result = $bllPage->removeFixedSitePage($id);

            echo $result ? 'true' : 'false';
        }
    }

	/**
     * add page to fixed
     *
     */
    public function addpageAction()
    {
        if ($this->_request->isPost()) {
            $id = (int)$this->_request->getPost('id');
            $fixedKeyword = $this->_request->getPost('fixedKeyword');
            $fixedKeyword = trim($fixedKeyword);

            if (empty($id) || empty($fixedKeyword)) {
                echo 'false';
                return;
            }

            require_once 'Admin/Bll/SitePage.php';
            $bllPage = new Admin_Bll_SitePage();
            $aryInfo = array();
            $aryInfo['keyword'] = $fixedKeyword;
            $aryInfo['page_id'] = $id;
            $result = $bllPage->addFixedSitePage($aryInfo);

            echo $result ? 'true' : 'false';
        }
    }

	/**
     * change fixed page sort
     *
     */
    public function sortfixedpageAction()
    {
        if ($this->_request->isPost()) {
            $upOrDown = (int)$this->_request->getPost('op');
            $id = (int)$this->_request->getPost('id');
            $fixedKeyword = $this->_request->getPost('fixedKeyword');

            if (empty($upOrDown) || empty($id) || empty($fixedKeyword)) {
                echo 'false';
                return;
            }

            require_once 'Admin/Bll/SitePage.php';
            $bllPage = new Admin_Bll_SitePage();
            $result = false;
            //up sort
            if (1 == $upOrDown) {
                $result = $bllPage->upSortFixedPage($id, $fixedKeyword);
            }
            //down sort
            else if (2 == $upOrDown) {
                $result = $bllPage->downSortFixedPage($id, $fixedKeyword);
            }

            echo $result ? 'true' : 'false';
        }
    }

    /**
     * search keyword rank stat list view
     *
     */
    public function keywordrankAction()
    {
        $pageIndex = (int)$this->_request->getPost('pageIndex', 1);
        $pageSize = (int)$this->_request->getPost('pageSize', 10);

        require_once 'Admin/Dal/SearchKeywordStat.php';
        $dalStat = Admin_Dal_SearchKeywordStat::getDefaultInstance();
        $result = $dalStat->getStatList($pageIndex, $pageSize);
        $count = (int)$dalStat->getStatListCount();

        $response = array('info' => $result, 'count' => $count);
        $response = Zend_Json::encode($response);
        echo $response;
    }

    /**
     * check is validate admin user before action
     *
     */
    function preDispatch()
    {

    }
}