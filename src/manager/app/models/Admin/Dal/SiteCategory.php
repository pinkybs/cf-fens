<?php

require_once 'Admin/Dal/Abstract.php';

/**
 * Dal SiteCategory
 * Admin Site Category Data Access Layer
 *
 * @package    Admin/Dal
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2009/03/25    zhangxin
 */
class Admin_Dal_SiteCategory extends Admin_Dal_Abstract
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
     * list SiteCategory
     *
     * @param Integer $pageindex
     * @param Integer $pagesize
     * @return array
     */
    public function getCategoryList($pageindex = 1, $pagesize = 10)
    {
        $start = ($pageindex - 1) * $pagesize;
        $sql = "SELECT * FROM nb_site_category ORDER BY `sort` LIMIT $start, $pagesize ";

        return $this->_rdb->fetchAll($sql);
    }

    /**
     * get list SiteCategory count
     *
     * @return integer
     */
    public function getCategoryListCount()
    {
        $sql = 'SELECT COUNT(*) FROM nb_site_category';
        return $this->_rdb->fetchOne($sql);
    }

    /**
     * get last one category before sel sort
     *
     * @param Integer $sort
     * @return array
     */
    public function getLastCategoryBeforeSelSort($sort)
    {
        $sql = "SELECT * FROM nb_site_category WHERE sort<:sort ORDER BY sort DESC LIMIT 0,1";
        return $this->_rdb->fetchRow($sql, array('sort' => $sort));
    }

    /**
     * get last one category before sel sort
     *
     * @param Integer $sort
     * @return array
     */
    public function getNextCategoryAfterSelSort($sort)
    {
        $sql = "SELECT * FROM nb_site_category WHERE sort>:sort ORDER BY sort ASC LIMIT 0,1";
        return $this->_rdb->fetchRow($sql, array('sort' => $sort));
    }

    /**
     * get SiteCategory info by id
     *
     * @param Integer $id
     * @return array
     */
    public function getCategoryById($id)
    {
        $sql = "SELECT * FROM nb_site_category WHERE id=:id";

        return $this->_rdb->fetchRow($sql, array('id' => $id));
    }

    /**
     * insert SiteCategory
     *
     * @param array $info
     * @return integer
     */
    public function insertCategory($info)
    {
        $this->_wdb->insert('nb_site_category', $info);
        return $this->_wdb->lastInsertId();
    }

    /**
     * update SiteCategory
     *
     * @param array $info
     * @param integer $id
     * @return integer
     */
    public function updateCategory($info, $id)
    {
        $where = $this->_wdb->quoteInto('id = ?', $id);
        return $this->_wdb->update('nb_site_category', $info, $where);
    }

    /**
     * delete SiteCategory
     *
     * @param integer $id
     * @return integer
     */
    public function deleteCategory($id)
    {
        $sql = "DELETE FROM nb_site_category WHERE id=:id ";
        return $this->_wdb->query($sql, array('id' => $id));
    }

}