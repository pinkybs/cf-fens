<?php

require_once 'Admin/Dal/Abstract.php';

/**
 * Dal SitePage
 * Admin Site Page Data Access Layer
 *
 * @package    Admin/Dal
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2009/03/25    zhangxin
 */
class Admin_Dal_SitePage extends Admin_Dal_Abstract
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
     * list SitePage
     *
     * @param Integer $pageindex
     * @param Integer $pagesize
     * @return array
     */
    public function getPageList($pageindex = 1, $pagesize = 10)
    {
        $start = ($pageindex - 1) * $pagesize;
        $sql = "SELECT * FROM site_page ORDER BY site_id,id LIMIT $start, $pagesize ";

        return $this->_rdb->fetchAll($sql);
    }

    /**
     * get list SitePage count
     *
     * @return integer
     */
    public function getPageListCount()
    {
        $sql = 'SELECT count(*) FROM site_page';
        return $this->_rdb->fetchOne($sql);
    }

    /**
     * get Site'Page count by site id
     *
     * @param Integer $sid
     * @return integer
     */
    public function getPageCountBySiteId($sid)
    {
        $sql = 'SELECT count(id) FROM site_page WHERE site_id=:site_id';
        return $this->_rdb->fetchOne($sql, array('site_id' => $sid));
    }

    /**
     * get Site'last update page time by site id
     *
     * @param Integer $sid
     * @return integer
     */
    public function getLastCrawlTimeBySiteId($sid)
    {
        $sql = 'SELECT MAX(FROM_UNIXTIME(create_time)) FROM site_page WHERE site_id=:site_id';
        return $this->_rdb->fetchOne($sql, array('site_id' => $sid));
    }

    /**
     * get SitePage info by id
     *
     * @param Integer $id
     * @return array
     */
    public function getPageById($id)
    {
        $sql = "SELECT * FROM site_page WHERE id=:id";

        return $this->_rdb->fetchRow($sql, array('id' => $id));
    }

    /**
     * insert SitePage
     *
     * @param array $info
     * @return integer
     */
    public function insertPage($info)
    {
        $this->_wdb->insert('site_page', $info);
        return $this->_wdb->lastInsertId();
    }

    /**
     * update SitePage
     *
     * @param array $info
     * @param integer $id
     * @return integer
     */
    public function updatePage($info, $id)
    {
        $where = $this->_wdb->quoteInto('id = ?', $id);
        return $this->_wdb->update('site_page', $info, $where);
    }

    /**
     * delete SitePage
     *
     * @param integer $id
     * @return integer
     */
    public function deletePage($id)
    {
        $sql = "DELETE FROM site_page WHERE id=:id ";
        return $this->_wdb->query($sql, array('id' => $id));
    }

    /**
     * delete SitePage by site id
     *
     * @param integer $sid
     * @return integer
     */
    public function deletePageBySiteId($sid)
    {
        $sql = "DELETE FROM site_page WHERE site_id=:id ";
        return $this->_wdb->query($sql, array('id' => $sid));
    }

    /***************** Fixed site page *****************/
	/**
     * list SitePage by keyword
     *
     * @param Integer $pageindex
     * @param Integer $pagesize
     * @param string $keyword
     * @param string $filter
     * @return array
     */
    public function getPageListByKeyword($pageindex = 1, $pagesize = 10, $keyword, $filter = '')
    {
        $aryParams = array();
        $start = ($pageindex - 1) * $pagesize;

//        $sql = "SELECT * FROM site_page WHERE (title LIKE :title OR body LIKE :body) ";
//        $aryParams['title'] = '%' . $keyword . '%';
//        $aryParams['body'] = '%' . $keyword . '%';

        $sMode = ' IN BOOLEAN MODE';
        $aryParams['content'] = '+'.$keyword;
        if (strpos($keyword, ' ') === false) {
            $sMode = '';
            $aryParams['content'] = $keyword;
        }
        $sql = "SELECT id,site_id,url,title,summary,create_time, MATCH(title,body) AGAINST(:content$sMode) AS score FROM site_page
        		WHERE MATCH(title,body) AGAINST(:content$sMode) ";


        if (!empty($filter)) {
            $sql .= " OR (url LIKE :filter3) ";
            //$sql .= " AND (title LIKE :filter1 OR body LIKE :filter2 OR url LIKE :filter3) ";
            //$aryParams['filter1'] = '%' . $filter . '%';
            //$aryParams['filter2'] = '%' . $filter . '%';
            $aryParams['filter3'] = '%' . $keyword . '%';
        }
        if (strpos($keyword, ' ') === false) {
            $sql .= " ORDER BY score DESC ";
        }
        $sql .= " LIMIT $start, $pagesize ";

        return $this->_rdb->fetchAll($sql, $aryParams);
    }

    /**
     * get list SitePage count by keyword
     *
     * @param string $keyword
     * @param string $filter
     * @return integer
     */
    public function getPageListCountByKeyword($keyword, $filter = '')
    {
        $aryParams = array();
//        $sql = 'SELECT COUNT(*) FROM site_page
//        		WHERE (title LIKE :title OR body LIKE :body) ';
//        $aryParams['title'] = '%' . $keyword . '%';
//        $aryParams['body'] = '%' . $keyword . '%';

        $sMode = ' IN BOOLEAN MODE';
        $aryParams['content'] = '+'.$keyword;
        if (strpos($keyword, ' ') === false) {
            $sMode = '';
            $aryParams['content'] = $keyword;
        }
        $sql = "SELECT COUNT(*) FROM site_page WHERE MATCH(title,body) AGAINST(:content$sMode) ";

        if (!empty($filter)) {
            $sql .= " OR (url LIKE :filter3) ";
            //$sql .= " AND (title LIKE :filter1 OR body LIKE :filter2 OR url LIKE :filter3) ";
            //$aryParams['filter1'] = '%' . $filter . '%';
            //$aryParams['filter2'] = '%' . $filter . '%';
            $aryParams['filter3'] = '%' . $keyword . '%';
        }
        return $this->_rdb->fetchOne($sql, $aryParams);
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
        $sql = "SELECT * FROM site_page_fixed_rank WHERE keyword=:keyword
                ORDER BY `sort` LIMIT $start, $pagesize ";

        return $this->_rdb->fetchAll($sql, array('keyword' => $keyword));
    }

    /**
     * get list Fixed SitePage count by keyword
     *
     * @param string $keyword
     * @return integer
     */
    public function getFixedPageListCountByKeyword($keyword)
    {
        $sql = 'SELECT COUNT(*) FROM site_page_fixed_rank WHERE keyword=:keyword';
        return $this->_rdb->fetchOne($sql, array('keyword' => $keyword));
    }

	/**
     * get Fixed SitePage by id
     *
     * @param Integer $id
     * @return array
     */
    public function getFixedPageById($id)
    {
        $sql = "SELECT * FROM site_page_fixed_rank WHERE id=:id";

        return $this->_rdb->fetchRow($sql, array('id' => $id));
    }

	/**
     * get Fixed SitePage by page id and keyword
     *
     * @param Integer $pageId
     * @param string $keyword
     * @return array
     */
    public function getFixedPageByPageIdAndKeyword($pageId, $keyword)
    {
        $sql = "SELECT * FROM site_page_fixed_rank WHERE page_id=:page_id AND keyword=:keyword";
        return $this->_rdb->fetchRow($sql, array('page_id' => $pageId, 'keyword' => $keyword));
    }

    /**
     * insert Fixed SitePage
     *
     * @param array $info
     * @return integer
     */
    public function insertFixedPage($info)
    {
        $this->_wdb->insert('site_page_fixed_rank', $info);
        return $this->_wdb->lastInsertId();
    }

    /**
     * update Fixed SitePage
     *
     * @param array $info
     * @param integer $id
     * @return integer
     */
    public function updateFixedPage($info, $id)
    {
        $where = $this->_wdb->quoteInto('id = ?', $id);
        return $this->_wdb->update('site_page_fixed_rank', $info, $where);
    }

    /**
     * delete Fixed SitePage
     *
     * @param integer $id
     * @return integer
     */
    public function deleteFixedPage($id)
    {
        $sql = "DELETE FROM site_page_fixed_rank WHERE id=:id ";
        return $this->_wdb->query($sql, array('id' => $id));
    }


	/**
     * get last one fixed page before sel sort and keyword
     *
     * @param Integer $sort
     * @param string $keyword
     * @return array
     */
    public function getLastFixedPageBeforeSelSort($sort, $keyword)
    {
        $sql = "SELECT * FROM site_page_fixed_rank WHERE sort<:sort AND keyword=:keyword
                ORDER BY sort DESC LIMIT 0,1";
        return $this->_rdb->fetchRow($sql, array('sort' => $sort, 'keyword' => $keyword));
    }

    /**
     * get last one fixed page before sel sort and keyword
     *
     * @param Integer $sort
     * @param string $keyword
     * @return array
     */
    public function getNextFixedPageAfterSelSort($sort, $keyword)
    {
        $sql = "SELECT * FROM site_page_fixed_rank WHERE sort>:sort AND keyword=:keyword
                ORDER BY sort ASC LIMIT 0,1";
        return $this->_rdb->fetchRow($sql, array('sort' => $sort, 'keyword' => $keyword));
    }
}