<?php

$hidSrhArea = (int)$_REQUEST['hidSrhArea'];
if (empty($hidSrhArea)) {
	header("Location:" . BASE_URL);
	return;
}

//get area info

require_once(DAL_DIR."/SiteArea.php");
$dalArea = new Dal_SiteArea();
/*
$aryAreaList = $dalArea->getAreaList(1, 100);
foreach ($aryAreaList as $key=>$dataArea) {
	$aryAreaList[$key]['bind_count'] = $dalArea->getAreaBindSiteCount($dataArea['id']);
}
*/
$rowArea = $dalArea->getAreaById($hidSrhArea);

//get search info
$hidCheck = isset($_REQUEST['hidCheck']) ? $_REQUEST['hidCheck'] : 3;
$hidCheck = empty($hidCheck) ? 3 : $hidCheck;
$keyword = isset($_REQUEST['hidSrhKey']) ? urldecode($_REQUEST['hidSrhKey']) : '';
$pageIndex = isset($_REQUEST['pageIndex']) ? (int)$_REQUEST['pageIndex'] : 1;
$pageSize = 10;
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
	$aryList = $dalPage->getPageListByKeywordAndFilter($pageIndex, $pageSize, $keyword, 0, (3==$hidCheck ? $hidSrhArea : ''));
//}
$count = (int)$dalPage->getPageListCountByKeywordAndFilter($keyword, 0, (3==$hidCheck ? $hidSrhArea : ''));

$pagingUrl = 'area?hidSrhArea=' . $hidSrhArea . '&hidCheck=' . $hidCheck;
if (!empty($keyword)) {
	$pagingUrl .= '&hidSrhKey=' . $keyword;
}
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

$smarty->assign('areaName', empty($rowArea) ? '' : $rowArea['name']);
//$smarty->assign('lstArea', $aryAreaList);
$smarty->assign('hidSrhKey', trim($keyword));
$smarty->assign('hidSrhArea', $hidSrhArea);
$smarty->assign('hidChkArea', $hidCheck);
$smarty->display('area.tpl');