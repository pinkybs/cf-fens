<?php

$hidSrhCate = (int)$_REQUEST['hidSrhCate'];
if (empty($hidSrhCate)) {
	header("Location:" . BASE_URL);
	return;
}

//get category info
require_once(DAL_DIR."/SiteCategory.php");
$dalCate = new Dal_SiteCategory();
$aryCateList = $dalCate->getCategoryList(1, 100);
foreach ($aryCateList as $key=>$dataCate) {
	$aryCateList[$key]['bind_count'] = $dalCate->getCategoryBindSiteCount($dataCate['id']);
}
$rowCate = $dalCate->getCategoryById($hidSrhCate);

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
	$aryList = $dalPage->getPageListByKeywordAndFilter($pageIndex, $pageSize, $keyword, (3==$hidCheck ? $hidSrhCate : ''), 0);
//}
$count = (int)$dalPage->getPageListCountByKeywordAndFilter($keyword, (3==$hidCheck ? $hidSrhCate : ''), 0);

$pagingUrl = 'category?hidSrhCate=' . $hidSrhCate . '&hidCheck=' . $hidCheck;
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

$smarty->assign('cateName', empty($rowCate) ? '' : $rowCate['name']);
$smarty->assign('lstCate', $aryCateList);
$smarty->assign('hidSrhKey', trim($keyword));
$smarty->assign('hidSrhCate', $hidSrhCate);
$smarty->assign('hidChkCate', $hidCheck);
$smarty->display('category.tpl');