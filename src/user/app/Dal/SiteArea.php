<?php

require_once 'Abstract.php';

/**
 * Dal SiteArea
 * Site Area Data Access Layer
 *
 * @package    Dal
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2009/04/03    zhangxin
 */
class Dal_SiteArea extends Dal_Abstract
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
     * get SiteArea info by id
     *
     * @param Integer $id
     * @return array
     */
    public function getAreaById($id)
    {
        $sql = "SELECT * FROM nb_site_area WHERE id=?";
		$stmt = $this->_rdb->prepare($sql);
        $stmt->execute(array($id));
		return $stmt->fetch();
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

		$stmt = $this->_rdb->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * get list SiteArea count
     *
     * @return integer
     */
    public function getAreaListCount()
    {
        $sql = 'SELECT COUNT(*) AS count FROM nb_site_area';
        $stmt = $this->_rdb->query($sql);
        return $stmt->fetchColumn(0);
    }

    /**
     * get SiteArea Bind count
     *
     * @param Integer $id
     * @return integer
     */
    public function getAreaBindSiteCount($id)
    {
        $sql = 'SELECT COUNT(*) FROM site WHERE area_id=?';
        $stmt = $this->_rdb->prepare($sql);
        $stmt->execute(array($id));
		return $stmt->fetchColumn(0);
    }

}