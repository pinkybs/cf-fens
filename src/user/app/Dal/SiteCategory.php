<?php

require_once 'Abstract.php';

/**
 * Dal SiteCategory
 * Site Category Data Access Layer
 *
 * @package    Dal
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2009/04/03    zhangxin
 */
class Dal_SiteCategory extends Dal_Abstract
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
     * get SiteCategory info by id
     *
     * @param Integer $id
     * @return array
     */
    public function getCategoryById($id)
    {
        $sql = "SELECT * FROM nb_site_category WHERE id=?";
		$stmt = $this->_rdb->prepare($sql);
        $stmt->execute(array($id));
		return $stmt->fetch();
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

        $stmt = $this->_rdb->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * get list SiteCategory count
     *
     * @return integer
     */
    public function getCategoryListCount()
    {
        $sql = 'SELECT count(*) FROM nb_site_category';
        $stmt = $this->_rdb->query($sql);
        return $stmt->fetchColumn(0);
    }

    /**
     * get SiteCategory Bind count
     *
     * @param Integer $id
     * @return integer
     */
    public function getCategoryBindSiteCount($id)
    {
        $sql = 'SELECT COUNT(*) FROM site WHERE category_id=?';
        $stmt = $this->_rdb->prepare($sql);
        $stmt->execute(array($id));
		return $stmt->fetchColumn(0);
    }

}