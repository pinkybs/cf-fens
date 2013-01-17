<?php

require_once 'Admin/Dal/Abstract.php';

/**
 * Dal SiteArea
 * Admin Site Area Data Access Layer
 *
 * @package    Admin/Dal
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2009/03/25    zhangxin
 */
class Admin_Dal_SiteArea extends Admin_Dal_Abstract
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
     * list SiteArea
     *
     * @param Integer $pageindex
     * @param Integer $pagesize
     * @return array
     */
    public function getAreaList($pageindex = 1, $pagesize = 10)
    {
        $start = ($pageindex - 1) * $pagesize;
        $sql = "SELECT * FROM nb_site_area ORDER BY `sort` LIMIT $start, $pagesize ";

        return $this->_rdb->fetchAll($sql);
    }

    /**
     * get list SiteArea count
     *
     * @return integer
     */
    public function getAreaListCount()
    {
        $sql = 'SELECT COUNT(*) FROM nb_site_area';
        return $this->_rdb->fetchOne($sql);
    }

    /**
     * get SiteArea info by id
     *
     * @param Integer $id
     * @return array
     */
    public function getAreaById($id)
    {
        $sql = "SELECT * FROM nb_site_area WHERE id=:id";

        return $this->_rdb->fetchRow($sql, array('id' => $id));
    }

    /**
     * insert SiteArea
     *
     * @param array $info
     * @return integer
     */
    public function insertArea($info)
    {
        $this->_wdb->insert('nb_site_area', $info);
        return $this->_wdb->lastInsertId();
    }

    /**
     * update SiteArea
     *
     * @param array $info
     * @param integer $id
     * @return integer
     */
    public function updateArea($info, $id)
    {
        $where = $this->_wdb->quoteInto('id = ?', $id);
        return $this->_wdb->update('nb_site_area', $info, $where);
    }

    /**
     * delete SiteArea
     *
     * @param integer $id
     * @return integer
     */
    public function deleteArea($id)
    {
        $sql = "DELETE FROM nb_site_area WHERE id=:id ";
        return $this->_wdb->query($sql, array('id' => $id));
    }

}