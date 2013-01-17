<?php

/**
 * Admin Bll BatchDaily
 * DB Auto Statistic Batch Daily Business Logic Layer
 *
 * @package    Admin/Bll
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2009/03/27    zhangxin
 */
final class Admin_Bll_BatchDaily
{
    /**
     * db read adapter
     * @var Zend_Db_Abstract
     */
    protected $_srcDb;

    /**
     * db write adapter
     * @var Zend_Db_Abstract
     */
    protected $_tarDb;

    /**
     * construct
     *
     * @param array $srcDb ( client db config )
        $params = array();
        $params['host'] = '192.168.0.14';
        $params['username'] = 'mysql';
        $params['password'] = 'mysql';
        $params['dbname'] = 'fens';
        $params['driver_options'] = array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true);
        $dbAdapter = Zend_Db::factory('PDO_MYSQL', $params);
		$dbAdapter->query("SET NAMES utf8");
     * @param array $tarDb ( server db config )
     */
    public function __construct($srcDb, $tarDb)
    {
        $this->_srcDb = $srcDb;
        $this->_tarDb = $tarDb;
    }

    /**
     * do daily batch statistic
     *
     * @param datetime $runingDate
     * @return boolean
     */
    public function doKeywordRankBatchStat($runingDate)
    {
        require_once 'Admin/Dal/SearchKeywordStat.php';
        $logName = 'DailyStat_SearchKeywordRank';
        info_log('', $logName);
        info_log("***** Start *****" . "***** Date:$runingDate ***** SearchKeywordRank Daily Batch begin!", $logName);

        try {
            //source DB and get data
            $dalStatRead = new Admin_Dal_SearchKeywordStat($this->_srcDb);

            //count ==0 nothing to do
            $cntSrhKeyword = $dalStatRead->getDailySearchKeywordStatCount($runingDate);
            if (empty($cntSrhKeyword)) {
                info_log('Nothing to do with this date:' . $runingDate, $logName);
                info_log("***** Date:$runingDate ***** SearchKeywordRank Daily Batch End!" . "***** End *****", $logName);
                return true;
            }

            //target DB and write data
            $dalStatWrite = new Admin_Dal_SearchKeywordStat($this->_tarDb);
            //$this->_tarDb->beginTransaction();
            //delete stat info if exist
            $dalStatWrite->deleteDailyStatByDate($runingDate);

            //200 rows per deal
            $cntDealTimes = ceil($cntSrhKeyword/200);
            for ($i = 1; $i <= $cntDealTimes; $i++ ) {
                $aryDailyInfo = $dalStatRead->getDailySearchKeywordStat($runingDate, $i, 200);
                //insert into admin site db
                foreach ($aryDailyInfo as $key => $item) {
                    $aryStat = array();
                    $aryStat['search_date'] = $runingDate;
                    $aryStat['search_keyword'] = $item['keyword'];
                    $aryStat['search_count'] = $item['search_count'];
                    $aryStat['create_time'] = date('Y-m-d H:i:s');
                    $dalStatWrite->insertDailyStat($aryStat);
                    $logRowInfo = ' Keyword(SearchCount):' . $item['keyword'] . '(' . $item['search_count'] . ')';
                    info_log('Insert succeed!--No:' . (($i-1)*200 + ((int)$key + 1)) . $logRowInfo, $logName);
                }
            }
            //$this->_tarDb->commit();

            info_log("***** Date:$runingDate ***** SearchKeywordRank Daily Batch End!" . "***** End *****", $logName);
            info_log('', $logName);
            return true;
        }
        catch (Exception $e) {
            //$this->_tarDb->rollBack();
            info_log('Error Happened! --PV Batch', $logName);
            info_log($e, $logName);
            info_log($e->__toString(), $logName);
            return false;
        }
    }

	/**
     * call proc update page count
     *
     * @return boolean
     */
    public function callProc()
    {
        $logName = 'DailyStat_PageCountProc';
        info_log('', $logName);
        info_log("***** Start *****" . "***** Date:" . date('Y-m-d H:i:s') . " ***** proc begin!", $logName);

        try {
            //update site count
            require_once 'Admin/Dal/Site.php';
            $dalSite = new Admin_Dal_site($this->_tarDb);
            $rst = $dalSite->callUpdateSiteCount();
            info_log($rst, $logName);
            info_log("***** Date:" . date('Y-m-d H:i:s') . " ***** proc End!" . "***** End *****", $logName);
            info_log('', $logName);
            return true;
        }
        catch (Exception $e) {
            info_log('Error Happened! --proc Batch', $logName);
            info_log($e, $logName);
            info_log($e->__toString(), $logName);
            return false;
        }
    }
}