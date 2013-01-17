<?php

$keyword = $_REQUEST['hidSrhKey'];
if (empty($keyword)) {
	echo 'none';
	exit(0);
}
$keyword = trim($keyword);

require_once(DAL_DIR."/SearchKeywordLog.php");
$dalLog = new Dal_SearchKeywordLog();
$aryInfo = array();
$aryInfo['keyword'] = $keyword;
$aryInfo['ip_address'] = $_SERVER['REMOTE_ADDR'];
$aryInfo['browser'] = $_SERVER['HTTP_USER_AGENT'];
$aryInfo['create_time'] = time();
$result = $dalLog->insertDailyLog($aryInfo);
echo $result;
