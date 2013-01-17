<?php

require_once 'Admin/Dal/Abstract.php';

/**
 * Dal Site
 * Admin Site Data Access Layer
 *
 * @package    Admin/Dal
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2009/03/25    zhangxin
 */
class Admin_Dal_Site extends Admin_Dal_Abstract
{

    protected static $_instance;

    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * list Site
     *
     * @param Integer $pageindex
     * @param Integer $pagesize
     * @return array
     */
    public function getSiteList($pageindex = 1, $pagesize = 10)
    {
        $start = ($pageindex - 1) * $pagesize;
        $sql = "SELECT * FROM site ORDER BY create_time DESC LIMIT $start, $pagesize ";

        return $this->_rdb->fetchAll($sql);
    }

    /**
     * get list Site count
     *
     * @return integer
     */
    public function getSiteListCount()
    {
        $sql = 'SELECT COUNT(id) FROM site';
        return $this->_rdb->fetchOne($sql);
    }

	/**
     * list Site by filter
     *
     * @param Integer $pageindex
     * @param Integer $pagesize
     * @return array
     */
    public function getSiteListByFilter($pageindex = 1, $pagesize = 10, $srhName = '', $srhUrl = '', $srhArea = '', $srhCate = '')
    {
        $start = ($pageindex - 1) * $pagesize;
        $aryParams = array();

        $sql = "SELECT * FROM site WHERE 1=1 ";
        if (!empty($srhName)) {
            $sql .= " AND name LIKE :name ";
            $aryParams['name'] = '%' .$srhName . '%';
        }
        if (!empty($srhUrl)) {
            $sql .= " AND url LIKE :url ";
            $aryParams['url'] = '%' .$srhUrl . '%';
        }
        if (!empty($srhArea)) {
            $sql .= " AND area_id=:area_id ";
            $aryParams['area_id'] = $srhArea;
        }
        if (!empty($srhCate)) {
            $sql .= " AND category_id=:category_id ";
            $aryParams['category_id'] = $srhCate;
        }
        $sql .= " ORDER BY create_time DESC LIMIT $start, $pagesize ";

        return $this->_rdb->fetchAll($sql, $aryParams);
    }

    /**
     * get list Site count by filter
     *
     * @return integer
     */
    public function getSiteListCountByFilter($srhName = '', $srhUrl = '', $srhArea = '', $srhCate = '')
    {
        $aryParams = array();
        $sql = 'SELECT COUNT(id) FROM site WHERE 1=1 ';

        if (!empty($srhName)) {
            $sql .= " AND name LIKE :name ";
            $aryParams['name'] = '%' .$srhName . '%';
        }
        if (!empty($srhUrl)) {
            $sql .= " AND url LIKE :url ";
            $aryParams['url'] = '%' .$srhUrl . '%';
        }
        if (!empty($srhArea)) {
            $sql .= " AND area_id=:area_id ";
            $aryParams['area_id'] = $srhArea;
        }
        if (!empty($srhCate)) {
            $sql .= " AND category_id=:category_id ";
            $aryParams['category_id'] = $srhCate;
        }
        return $this->_rdb->fetchOne($sql, $aryParams);
    }

    /**
     * get Site info by id
     *
     * @param Integer $id
     * @return array
     */
    public function getSiteById($id)
    {
        $sql = "SELECT * FROM site WHERE id=:id";

        return $this->_rdb->fetchRow($sql, array('id' => $id));
    }

    /**
     * insert Site
     *
     * @param array $info
     * @return integer
     */
    public function insertSite($info)
    {
        $this->_wdb->insert('site', $info);
        return $this->_wdb->lastInsertId();
    }

    /**
     * update Site
     *
     * @param array $info
     * @param integer $id
     * @return integer
     */
    public function updateSite($info, $id)
    {
        $where = $this->_wdb->quoteInto('id = ?', $id);
        return $this->_wdb->update('site', $info, $where);
    }

    /**
     * delete Site
     *
     * @param integer $id
     * @return integer
     */
    public function deleteSite($id)
    {
        $sql = "DELETE FROM site WHERE id=:id ";
        return $this->_wdb->query($sql, array('id' => $id));
    }

	/**
     * updateSite By Site Category id
     *
     * @param integer $cid
     * @return integer
     */
    public function clearSiteCateoryByCategoryId($cid)
    {
        $sql = "UPDATE site SET category_id=NULL WHERE category_id=:id ";
        return $this->_wdb->query($sql, array('id' => $cid));
    }

    /**
     * get Site count by category id
     *
     * @param integer $cid
     * @return integer
     */
    public function getSiteCountByCategoryId($cid)
    {
        $sql = 'SELECT COUNT(id) FROM site WHERE category_id=:id';
        return $this->_rdb->fetchOne($sql, array('id' => $cid));
    }

	/**
     * call proc
     *
     * @return integer
     */
    public function callUpdateSiteCount()
    {
        $sql = "CALL pd_get_crawl_site_pgcount_pglasttime; ";
        $stmt = $this->_rdb->prepare($sql);
        $stmt->execute();
        $stmt->closeCursor();
        return 1;
    }
}