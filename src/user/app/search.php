<?php

$keyword = urldecode($_REQUEST['hidSrhKey']);
if (empty($keyword)) {
	header("Location:" . BASE_URL);
	return;
}

//current page index
$pageIndex = isset($_REQUEST['pageIndex']) ? (int)$_REQUEST['pageIndex'] : 1;
$pageSize = 10;
$pageSize = empty($pageSize) ? 10 : $pageSize;
$keyword = trim($keyword);

require_once(DAL_DIR."/SitePage.php");
$dalPage = new Dal_SitePage();
$fixedCount = (int)$dalPage->getFixedPageListCountByKeyword($keyword);
$fixedCount = $fixedCount > $pageSize ? $pageSize : $fixedCount;
//get fixed result
if (1 == $pageIndex) {
	$fixedList = $dalPage->getFixedPageListByKeyword(1, 10, $keyword);
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
	$otherList = $dalPage->getPageListByKeyword(0, ($pageSize - $fixedCount), $keyword);
	$aryList = array_merge($fixedList,$otherList);
}
//get search result
else {
	$aryList = $dalPage->getPageListByKeyword(($pageIndex-1)*$pageSize-$fixedCount, $pageSize, $keyword);
}
$count = (int)$dalPage->getPageListCountByKeyword($keyword) + $fixedCount;

$pagingUrl = 'search?hidSrhKey=' . urlencode($keyword);
$numstart = 0;
$numend = 0;
if ($count != 0) {
	$numstart = (int)($pageIndex - 1) * $pageSize + 1;
	$numend = ($numstart + $pageSize - 1) > $count ? $count : ($numstart + $pageSize - 1);
}
$smarty->assign('numStart', ($numstart));
$smarty->assign('numEnd', ($numend));
$smarty->assign('pagingUrl', ($pagingUrl));
$smarty->assign('pageIndex', $pageIndex);
$smarty->assign('pageSize', $pageSize);
$smarty->assign('lstData', $aryList);
$smarty->assign('cntData', $count);
$smarty->assign('lightKey', '<span style="background:#ffff99;">' . htmlspecialchars($keyword) . '</span>');
$smarty->assign('hidSrhKey', ($keyword));
$smarty->display('search.tpl');