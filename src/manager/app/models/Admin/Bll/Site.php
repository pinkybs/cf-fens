<?php

/**
 * Site logic's Operation
 *
 * @package    Admin/Bll
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2009/03/25    zhangxin
 */
final class Admin_Bll_Site
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
     * init the user's variables
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
     * delete site by id
     *
     * @param integer $id
     * @return boolean
     */
    public function delSite($id)
    {
        try {
            require_once 'Admin/Dal/Site.php';
            $dalSite = Admin_Dal_Site::getDefaultInstance();
            $rowCurSite = $dalSite->getSiteById($id);
            if (empty($rowCurSite)) {
                return false;
            }

            require_once 'Admin/Dal/SitePage.php';
            $dalPage = Admin_Dal_SitePage::getDefaultInstance();

            $this->_wdb->beginTransaction();
            //delete site pages
            $dalPage->deletePageBySiteId($id);
            //delete site
            $dalSite->deleteSite($id);
            $this->_wdb->commit();
            return true;
        }
        catch (Exception $e) {
            $this->_wdb->rollBack();
            return false;
        }
    }

    /**
     * add site
     *
     * @param array $info
     * @return boolean
     */
    public function addSite($info)
    {
        try {
            require_once 'Admin/Dal/Site.php';
            $dalSite = Admin_Dal_Site::getDefaultInstance();

            $info['create_time'] = date('Y-m-d H:i:s');
            $this->_wdb->beginTransaction();
            $id = $dalSite->insertSite($info);
            $this->_wdb->commit();
            return true;
        }
        catch (Exception $e) {
            $this->_wdb->rollBack();
            return false;
        }
    }

    /**
     * edit site
     *
     * @param array $info
     * @param integer $id
     * @return boolean
     */
    public function editSite($info, $id)
    {
        try {
            require_once 'Admin/Dal/Site.php';
            $dalSite = Admin_Dal_Site::getDefaultInstance();
            $rowSite = $dalSite->getSiteById($id);
            if (empty($rowSite)) {
                return false;
            }

            $this->_wdb->beginTransaction();
            $id = $dalSite->updateSite($info, $id);
            $this->_wdb->commit();
            return true;
        }
        catch (Exception $e) {
            $this->_wdb->rollBack();
            return false;
        }
    }

}