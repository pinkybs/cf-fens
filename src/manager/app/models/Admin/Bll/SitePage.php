<?php

/**
 * SitePage Page logic's Operation
 *
 * @package    Admin/Bll
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2009/03/26    zhangxin
 */
final class Admin_Bll_SitePage
{
    /**
     * db config
     * @var array
     */
    protected $_config;

    /**
     * db read adapter
     * @var Zend_Db_Abstract
     */
    protected $_rdb;

    /**
     * db write adapter
     * @var Zend_Db_Abstract
     */
    protected $_wdb;

    protected static $_instance;

    /**
     * init the sitepage's variables
     *
     * @param array $config ( config info )
     */
    public function __construct($config = null)
    {
        if (is_null($config)) {
            $config = Zend_Registry::get('dbConfig');
        }

        $this->_config = $config;
        $this->_rdb = $config['readDB'];
        $this->_wdb = $config['writeDB'];
    }

    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * remove fixed site page by id
     *
     * @param integer $id
     * @return boolean
     */
    public function removeFixedSitePage($id)
    {
        try {
            require_once 'Admin/Dal/SitePage.php';
            $dalSitePage = Admin_Dal_SitePage::getDefaultInstance();
            $rowSitePage = $dalSitePage->getFixedPageById($id);
            if (empty($rowSitePage)) {
                return false;
            }

            $this->_wdb->beginTransaction();
            //delete fixed site page
            $dalSitePage->deleteFixedPage($id);
            $this->_wdb->commit();
            return true;
        }
        catch (Exception $e) {
            $this->_wdb->rollBack();
            return false;
        }
    }

    /**
     * add fixed site page
     *
     * @param array $info
     * @return boolean
     */
    public function addFixedSitePage($info)
    {
        try {
            require_once 'Admin/Dal/SitePage.php';
            $dalSitePage = Admin_Dal_SitePage::getDefaultInstance();
            $rowFixed = $dalSitePage->getFixedPageByPageIdAndKeyword($info['page_id'], $info['keyword']);
            if (!empty($rowFixed)) {
                return false;
            }

            $info['create_time'] = date('Y-m-d H:i:s');
            $this->_wdb->beginTransaction();
            $id = $dalSitePage->insertFixedPage($info);
            $dalSitePage->updateFixedPage(array('sort' => $id), $id);
            $this->_wdb->commit();
            return true;
        }
        catch (Exception $e) {
            $this->_wdb->rollBack();
            return false;
        }
    }

	/**
     * go up an order by fixed id and keyword
     *
     * @param integer $id
     * @param string $keyword
     * @return boolean
     */
    public function upSortFixedPage($id, $keyword)
    {
        try {
            require_once 'Admin/Dal/SitePage.php';
            $dalPage = Admin_Dal_SitePage::getDefaultInstance();
            $rowCurPage = $dalPage->getFixedPageById($id);
            if (empty($rowCurPage)) {
                return false;
            }

            $rowLastPage = $dalPage->getLastFixedPageBeforeSelSort($rowCurPage['sort'], $keyword);
            //already first row, nothing to do.
            if (empty($rowLastPage)) {
                return true;
            }

            $tmpSort = $rowCurPage['sort'];
            $this->_wdb->beginTransaction();
            //cur  fixed page　↑
            $dalPage->updateFixedPage(array('sort' => $rowLastPage['sort']), $id);
            //last fixed page ↓
            $dalPage->updateFixedPage(array('sort' => $tmpSort), $rowLastPage['id']);
            $this->_wdb->commit();
            return true;
        }
        catch (Exception $e) {
            $this->_wdb->rollBack();
            return false;
        }
    }

    /**
     * go down an order by fixed id and keyword
     *
     * @param integer $id
     * @param string $keyword
     * @return boolean
     */
    public function downSortFixedPage($id, $keyword)
    {
        try {
            require_once 'Admin/Dal/SitePage.php';
            $dalPage = Admin_Dal_SitePage::getDefaultInstance();
            $rowCurPage = $dalPage->getFixedPageById($id);
            if (empty($rowCurPage)) {
                return false;
            }

            $rowNextPage = $dalPage->getNextFixedPageAfterSelSort($rowCurPage['sort'], $keyword);
            //already last row, nothing to do.
            if (empty($rowNextPage)) {
                return true;
            }
            $tmpSort = $rowCurPage['sort'];

            $this->_wdb->beginTransaction();
            //cur  fixed page　↓
            $dalPage->updateFixedPage(array('sort' => $rowNextPage['sort']), $id);
            //next fixed page ↑
            $dalPage->updateFixedPage(array('sort' => $tmpSort), $rowNextPage['id']);
            $this->_wdb->commit();
            return true;
        }
        catch (Exception $e) {
            $this->_wdb->rollBack();
            return false;
        }
    }

}