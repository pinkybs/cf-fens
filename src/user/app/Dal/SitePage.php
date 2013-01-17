<?php

require_once 'Abstract.php';

/**
 * Dal SitePage
 * Site Page Data Access Layer
 *
 * @package    Dal
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2009/04/09    zhangxin
 */
class Dal_SitePage extends Dal_Abstract
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
     * get SitePage info by id
     *
     * @param Integer $id
     * @return array
     */
    public function getPageById($id)
    {
        $sql = "SELECT * FROM site_page WHERE id=?";
        $stmt = $this->_rdb->prepare($sql);
        $stmt->execute(array($id));
		return $stmt->fetch();
    }

	/**
     * list SitePage by keyword
     *
     * @param Integer $pageindex
     * @param Integer $pagesize
     * @param string $keyword
     * @return array
     */
    //public function getPageListByKeyword($pageindex = 1, $pagesize = 10, $keyword)
    public function getPageListByKeyword($start = 0, $pagesize = 10, $keyword)
    {
        //$start = ($pageindex - 1) * $pagesize;
        $sMode = ' IN BOOLEAN MODE';
        $strParam = '+'.$keyword;
        if (strpos($keyword, ' ') === false) {
            $sMode = '';
            $strParam = $keyword;
        }
		$sql = "SELECT id,site_id,url,title,summary,create_time, MATCH(title,body) AGAINST(?$sMode) AS score FROM site_page " 
		     . "WHERE MATCH(title,body) AGAINST(?$sMode) ";
		if (strpos($keyword, ' ') === false) {
            $sql .= " ORDER BY score DESC ";
        }
		$sql .= " LIMIT $start, $pagesize ";
		$stmt = $this->_rdb->prepare($sql);
        $stmt->execute(array($strParam,$strParam));
		return $stmt->fetchAll();
    }

    /**
     * get list SitePage count by keyword
     *
     * @param string $keyword
     * @return integer
     */
    public function getPageListCountByKeyword($keyword)
    {
    	$sMode = ' IN BOOLEAN MODE';
        $strParam = '+'.$keyword;
        if (strpos($keyword, ' ') === false) {
            $sMode = '';
            $strParam = $keyword;
        }
		$sql = "SELECT COUNT(*) FROM site_page WHERE MATCH(title,body) AGAINST(?$sMode) ";
        $stmt = $this->_rdb->prepare($sql);
        $stmt->execute(array($strParam));
		return $stmt->fetchColumn(0);
    }

	/**
     * list Fixed SitePage by keyword
     *
     * @param Integer $pageindex
     * @param Integer $pagesize
     * @param string $keyword
     * @return array
     */
    public function getFixedPageListByKeyword($pageindex = 1, $pagesize = 10, $keyword)
    {
        $start = ($pageindex - 1) * $pagesize;
        $sql = "SELECT * FROM site_page_fixed_rank WHERE keyword=?
                ORDER BY `sort` LIMIT $start, $pagesize ";

		$stmt = $this->_rdb->prepare($sql);
        $stmt->execute(array($keyword));
		return $stmt->fetchAll();
    }

    /**
     * get list Fixed SitePage count by keyword
     *
     * @param string $keyword
     * @return integer
     */
    public function getFixedPageListCountByKeyword($keyword)
    {
        $sql = 'SELECT count(*) FROM site_page_fixed_rank WHERE keyword=?';
        $stmt = $this->_rdb->prepare($sql);
        $stmt->execute(array($keyword));
		return $stmt->fetchColumn(0);
    }

	/**
     * list SitePage by keyword
     *
     * @param Integer $pageindex
     * @param Integer $pagesize
     * @param string $keyword
     * @param Integer $cateid
     * @param Integer $areaid
     * @return array
     */
    public function getPageListByKeywordAndFilter($pageindex = 1, $pagesize = 10, $keyword='', $cateid=0, $areaid=0)
    {
        $start = ($pageindex - 1) * $pagesize;
		$aryParams = array();
		$sMode = ' IN BOOLEAN MODE';
	    $strParam = '+'.$keyword;
	    
		$sql = "SELECT site_page.id,site_page.site_id,site_page.url,site_page.title,site_page.summary,site_page.create_time ";
		if (!empty($keyword)) {
	        if (strpos($keyword, ' ') === false) {
	            $sMode = '';
	            $strParam = $keyword;
	        }
			$sql .= ", MATCH(title,body) AGAINST(:content$sMode) AS score ";
		}
		$sql .= " FROM site_page,site WHERE site_page.site_id=site.id ";
		if (!empty($cateid)) {
			$sql .= ' AND site.category_id=:cateid ';
			$aryParams['cateid'] = $cateid;
		}
		if (!empty($areaid)) {
			$sql .= ' AND site.area_id=:areaid ';
			$aryParams['areaid'] = $areaid;
		}
		if (!empty($keyword)) {
	        if (strpos($keyword, ' ') === false) {
	            $sMode = '';
	            $strParam = $keyword;
	        }
			$sql .= " AND MATCH(title,body) AGAINST(:content$sMode) ";
			$aryParams['content'] = $strParam;
		}
		$sql .= " ORDER BY site_page.title LIMIT $start, $pagesize ";
		
		$stmt = $this->_rdb->prepare($sql);
        $stmt->execute($aryParams);
		return $stmt->fetchAll();
    }

    /**
     * get list SitePage count by keyword
     *
     * @param string $keyword
     * @param Integer $cateid
     * @param Integer $areaid
     * @return integer
     */
    public function getPageListCountByKeywordAndFilter($keyword='', $cateid=0, $areaid=0)
    {
    	$aryParams = array();
    	$sMode = ' IN BOOLEAN MODE';
	    $strParam = '+'.$keyword;
	    
		$sql = 'SELECT COUNT(site_page.id) FROM site_page,site WHERE site_page.site_id=site.id ';
		if (!empty($keyword)) {
			if (strpos($keyword, ' ') === false) {
	            $sMode = '';
	            $strParam = $keyword;
	        }
			$sql .= " AND MATCH(title,body) AGAINST(:content$sMode) ";
			$aryParams['content'] = $strParam;
		}
		if (!empty($cateid)) {
			$sql .= ' AND site.category_id=:cateid ';
			$aryParams['cateid'] = $cateid;
		}
		if (!empty($areaid)) {
			$sql .= ' AND site.area_id=:areaid ';
			$aryParams['areaid'] = $areaid;
		}
        $stmt = $this->_rdb->prepare($sql);
        $stmt->execute($aryParams);
		return $stmt->fetchColumn(0);
    }
}