<?php

require_once 'Admin/Dal/Abstract.php';

/**
 * Dal SearchKeywordStat
 * Admin Statistic Of Search Keyword Data Access Layer
 *
 * @package    Admin/Dal
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2009/03/27    zhangxin
 */
class Admin_Dal_SearchKeywordStat extends Admin_Dal_Abstract
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
     * list Statistic Info
     *
     * @param Integer $pageindex
     * @param Integer $pagesize
     * @return array
     */
    public function getStatList($pageindex = 1, $pagesize = 10)
    {
        $start = ($pageindex - 1) * $pagesize;
        $sql = "SELECT search_keyword,SUM(search_count) AS search_count FROM search_keyword_daily_stat
                GROUP BY search_keyword
                ORDER BY search_count DESC LIMIT $start, $pagesize ";

        return $this->_rdb->fetchAll($sql);
    }

    /**
     * get list Statistic Info count
     *
     * @return integer
     */
    public function getStatListCount()
    {
        $sql = 'SELECT COUNT(DISTINCT(search_keyword)) FROM search_keyword_daily_stat';
        return $this->_rdb->fetchOne($sql);
    }

    /**
     * get Daily Search Stat info by Primary Key
     *
     * @param string $date
     * @param string $keyword
     * @return array
     */
    public function getDailyStatByPk($date, $keyword)
    {
        $sql = "SELECT * FROM search_keyword_daily_stat
                WHERE DATE(search_date)=DATE(:search_date) AND search_keyword=:search_keyword";

        return $this->_rdb->fetchRow($sql, array('search_date' => $date, 'search_keyword' => $keyword));
    }

    /**
     * insert Daily Search Stat
     *
     * @param array $info
     * @return integer
     */
    public function insertDailyStat($info)
    {
        return $this->_wdb->insert('search_keyword_daily_stat', $info);
    }

    /**
     * update Daily Search Stat
     *
     * @param array $info
     * @param string $date
     * @param string $keyword
     * @return integer
     */
    public function updateDailyStat($info, $date, $keyword)
    {
        $where = array($this->_wdb->quoteInto('search_date=?', $date),
                       $this->_wdb->quoteInto('search_keyword=?', $keyword));
        return $this->_wdb->update('search_keyword_daily_stat', $info, $where);
    }

    /**
     * delete Daily Search Stat
     *
     * @param string $date
     * @param string $keyword
     * @return integer
     */
    public function deleteDailyStat($date, $keyword)
    {
        $sql = "DELETE FROM search_keyword_daily_stat
                WHERE DATE(search_date)=DATE(:search_date) AND search_keyword=:search_keyword";
        return $this->_wdb->query($sql, array('search_date' => $date, 'search_keyword' => $keyword));
    }

	/**
     * delete Daily Search Stat By Date
     *
     * @param string $date
     * @return integer
     */
    public function deleteDailyStatByDate($date)
    {
        $sql = "DELETE FROM search_keyword_daily_stat WHERE DATE(search_date)=DATE(:search_date) ";
        return $this->_wdb->query($sql, array('search_date' => $date));
    }

   /**
     * get Daily Search Keyword log
     *
     * @param string $seldate
     * @param Integer $pageindex
     * @param Integer $pagesize
     * @return array
     */
    public function getDailySearchKeywordStat($seldate, $pageindex = 1, $pagesize = 10)
    {
        $start = ($pageindex - 1) * $pagesize;
        $sql = "SELECT keyword,COUNT(id) AS search_count FROM search_keyword_log
                WHERE DATE(FROM_UNIXTIME(create_time))=DATE(:seldate)
                GROUP BY keyword ORDER BY search_count DESC LIMIT $start, $pagesize";

        return $this->_rdb->fetchAll($sql, array('seldate' => $seldate));
    }

   /**
     * get Daily Search Keyword log count
     *
     * @param string $seldate
     * @return integer
     */
    public function getDailySearchKeywordStatCount($seldate)
    {
        $sql = 'SELECT COUNT(DISTINCT(keyword)) FROM search_keyword_log
                WHERE DATE(FROM_UNIXTIME(create_time))=DATE(:seldate) ';
        return $this->_rdb->fetchOne($sql, array('seldate' => $seldate));
    }

}