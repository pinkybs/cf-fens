<?php

//get category info
require_once(DAL_DIR."/SiteCategory.php");
$dalCate = new Dal_SiteCategory();
$aryCateList = $dalCate->getCategoryList(1, 100);
foreach ($aryCateList as $key=>$dataCate) {
	$aryCateList[$key]['bind_count'] = $dalCate->getCategoryBindSiteCount($dataCate['id']);
}

//get area info
/*
require_once(DAL_DIR."/SiteArea.php");
$dalArea = new Dal_SiteArea();
$aryAreaList = $dalArea->getAreaList(1, 100);
foreach ($aryAreaList as $key=>$dataArea) {
	$aryAreaList[$key]['bind_count'] = $dalArea->getAreaBindSiteCount($dataArea['id']);
}
*/

$smarty->assign('lstCate', $aryCateList);
//$smarty->assign('lstArea', $aryAreaList);
$smarty->display('top.tpl');