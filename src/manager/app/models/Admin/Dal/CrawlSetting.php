<?php

require_once 'Admin/Dal/Abstract.php';

/**
 * Dal CrawlSetting
 * Admin Crawl Setting Data Access Layer
 *
 * @package    Admin/Dal
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2009/03/23    zhangxin
 */
class Admin_Dal_CrawlSetting extends Admin_Dal_Abstract
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
     * get CrawlSetting info
     *
     * @param null
     * @return array
     */
    public function getCrawlSetting()
    {
        $sql = "SELECT * FROM crawler_setting";
        return $this->_rdb->fetchRow($sql);
    }

    /**
     * insert CrawlSetting
     *
     * @param array $info
     * @return integer
     */
    public function insertCrawlSetting($info)
    {
        return $this->_wdb->insert('crawler_setting', $info);
    }

    /**
     * update CrawlSetting
     *
     * @param array $info
     * @return integer
     */
    public function updateCrawlSetting($info)
    {
        return $this->_wdb->update('crawler_setting', $info);
    }

    /**
     * delete CrawlSetting
     *
     * @return integer
     */
    public function deleteCrawlSetting()
    {
        $sql = "DELETE FROM crawler_setting";
        return $this->_wdb->query($sql);
    }

    /**
     * get last db sync time
     *
     * @param null
     * @return string
     */
    public function getLastSyncDbLogTime()
    {
        $sql = "SELECT FROM_UNIXTIME(MAX(end_time)) AS last_time FROM db_synchronize_log WHERE is_ended=1";
        return $this->_rdb->fetchOne($sql);
    }

    /**
     * is db synchronize excuting...
     *
     * @param null
     * @return boolean
     */
    public function isAnySyncDbExcuting()
    {
        $sql = "SELECT COUNT(*) FROM db_synchronize_log WHERE is_ended=0";
        $result = $this->_rdb->fetchOne($sql);
        return $result > 0 ? true : false;
    }

    /**
     * insert db_synchronize_log
     *
     * @param array $info
     * @return integer
     */
    public function insertSyncDbLog($info)
    {
        $this->_wdb->insert('db_synchronize_log', $info);
        return $this->_wdb->lastInsertId();
    }

    /**
     * update db_synchronize_log
     *
     * @param array $info
     * @param integer $id
     * @return integer
     */
    public function updateSyncDbLog($info, $id)
    {
        $where = $this->_wdb->quoteInto('id = ?', $id);
        return $this->_wdb->update('db_synchronize_log', $info, $where);
    }

}