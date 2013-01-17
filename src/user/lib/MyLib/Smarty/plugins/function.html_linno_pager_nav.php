<?php

function smarty_function_html_linno_pager_nav($params, &$smarty)
{
    $count = $params['totalCount'];
	$pageindex = $params['pageIndex'];
	$pagesize = $params['pageSize'];
	$pagecount = $params['pageCount'];
	$action = $params['urlAction'];

    if (empty($pagecount)) {
        $pagecount = 10;
    }

    if (empty($action)) {
        $action = 'javascript:void(0);';
    }

    if ($count <= $pagesize) {
        return '';
    }

    $nav = '';
    $forward = '';
    $pagerleft = '';
    $pagercurrent = '';
    $pagerright = '';
    $next = '';
    $maxpage = ceil($count/$pagesize);

    if ($pageindex > 1) {
        $forward .= '<li class="nextBtn"><a href="' . $action . '&pageIndex=' . ($pageindex - 1) . '">＜前へ</a></li>';
    }

    if ($maxpage > $pageindex) {
        $next = '<li class="nextBtn"><a href="' . $action . '&pageIndex=' . ($pageindex + 1) . '">次へ＞</a></li>';
    }

    $page = ceil($pagecount/2);

    //all page count
    $i = 1;

    //left nav
    $left = 0;
    for ($left = $pageindex - 1; $left > 0 && $left > $pageindex - $page; $left--) {
        $i++;
        $pagerleft = '<li><a href="' . $action . '&pageIndex=' . $left . '">' . $left . '</a></li>' . $pagerleft;
    }

    //current nav number
    $pagercurrent = '<li class="stay">' . $pageindex . '</li>';//<a href="' . $action . '&pageIndex=' . $pageindex . '">' . $pageindex . '</a>

    //right nav
    $right = 0;
    for ($right = $pageindex + 1; $right <= $maxpage && $right < $pageindex + $page ; $right++) {
        $i++;
        $pagerright = $pagerright . '<li><a href="' . $action . '&pageIndex=' . $right . '">' . $right . '</a></li>';
    }

    //If right side is not enough, show the page number for left until the page number number is up to 1
    if ($i < $pagecount && $left >= 1) {
        for (; $left > 0 && $i < $pagecount; $left--,$i++) {
            $pagerleft = '<li><a href="' . $action . '&pageIndex=' . $left . '">' . $left . '</a></li>' . $pagerleft;
        }
    }

    //If left side is not enough, showthe page number for right until the page number number is up to max
    if ($i < $pagecount && $right <= $maxpage) {
        for (; $right <= $maxpage && $i < $pagecount; $right++,$i++) {
            $pagerright = $pagerright . '<li><a href="' . $action . '&pageIndex=' . $right . '">' . $right . '</a></li>';
        }
    }

    $nav = '<ul id="pageNavBottom">' . $forward . $pagerleft . $pagercurrent . $pagerright . $next . '</ul>';

    return $nav;
}

?>