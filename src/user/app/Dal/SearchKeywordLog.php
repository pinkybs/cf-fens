<?php

require_once 'Abstract.php';

/**
 * Dal SearchKeywordLog
 * Statistic Of Search Keyword Data Access Layer
 *
 * @package    Dal
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2009/04/09    zhangxin
 */
class Dal_SearchKeywordLog extends Dal_Abstract
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
     * insert Daily Search Log
     *
     * @param array $info
     * @return integer
     */
    public function insertDailyLog($info)
    {
        $sql = 'INSERT INTO search_keyword_log (keyword,ip_address,browser,create_time) VALUES (?,?,?,?)';
        $stmt = $this->_rdb->prepare($sql);
        $stmt->execute(array($info['keyword'],$info['ip_address'],$info['browser'],$info['create_time']));
        $result = $stmt->rowCount();
        return $result;
    }
}