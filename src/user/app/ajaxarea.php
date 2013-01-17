<?php
/** @see Zend_Json */
require_once(LIB_DIR."/Zend/Json.php");

$keyword = $_REQUEST['hidSrhKey'];
$hidSrhArea = (int)$_REQUEST['hidSrhArea'];
if (empty($keyword) && empty($hidSrhArea)) {
	echo 'none';
	exit(0);
}
$pageIndex = (int)$_REQUEST['pageIndex'];
$pageSize = (int)$_REQUEST['pageSize'];
$pageIndex = empty($pageIndex) ? 1 : $pageIndex;
$pageSize = empty($pageSize) ? 10 : $pageSize;
$keyword = trim($keyword);

require_once(DAL_DIR."/SitePage.php");
$dalPage = new Dal_SitePage();
/*
//get fixed result
if (1 == $pageIndex) {
	$fixedList = $dalPage->getFixedPageListByKeyword(1, 10, $keyword);
	$fixedCount = (int)$dalPage->getFixedPageListCountByKeyword($keyword);
	foreach ($fixedList as $key=>$itemPage) {
        //get fixed page info
        $rowPage = $dalPage->getPageById($itemPage['page_id']);
        $fixedList[$key]['url'] = empty($rowPage) ? '' : $rowPage['url'];
        $fixedList[$key]['title'] = empty($rowPage) ? '' : $rowPage['title'];
        $fixedList[$key]['body'] = empty($rowPage) ? '' : $rowPage['body'];
        $fixedList[$key]['summary'] = empty($rowPage) ? '' : $rowPage['summary'];
        $fixedList[$key]['create_time'] = empty($rowPage) ? '' : $rowPage['create_time'];
        $fixedList[$key]['fixed_id'] = $itemPage['id'];
        $fixedList[$key]['id'] = $itemPage['page_id'];
    }
	$otherList = $dalPage->getPageListByKeyword(1, (10 - $fixedCount), $keyword);
	$aryList = array_merge($fixedList,$otherList);
}
*/
//get search result
//else {
	$aryList = $dalPage->getPageListByKeywordAndFilter($pageIndex, $pageSize, $keyword, 0, $hidSrhArea);
//}
$count = (int)$dalPage->getPageListCountByKeywordAndFilter($keyword, 0, $hidSrhArea);

$response = array('info' => $aryList, 'count' => $count);
$response = Zend_Json::encode($response);
echo $response;
