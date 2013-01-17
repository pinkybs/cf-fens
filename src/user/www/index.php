<?php
//phpinfo();
date_default_timezone_set('Asia/Tokyo');
define("LIB_DIR",'../lib');
define("DAL_DIR",'../app/Dal');
define("BLL_DIR",'../app/Bll');
set_include_path(LIB_DIR . PATH_SEPARATOR . DAL_DIR . PATH_SEPARATOR . BLL_DIR . PATH_SEPARATOR . get_include_path());

define("BASE_URL", 'http://fens.communityfactory.net');
define("STATIC_URL", 'http://fens.communityfactory.net/static');
define("VERSION", '20090625');
define("MYSQL_DSN", 'mysql:host=202.224.192.117;dbname=fens');
define("MYSQL_USER", 'mysql');
define("MYSQL_PASS", 'mysql');

// load Smarty library 
require_once(LIB_DIR."/Smarty/Smarty.class.php");
$smarty = new Smarty; 
$smarty->template_dir = '../templates/'; 
$smarty->compile_dir = '../templates_c/'; 
$smarty->left_delimiter = '{%';
$smarty->right_delimiter = '%}';
$smarty->plugins_dir = array('plugins', LIB_DIR . '/MyLib/Smarty/plugins');

$version = array('js'=>VERSION, 'css'=>VERSION);
$smarty->assign('version', $version);
$smarty->assign('baseUrl', BASE_URL);
$smarty->assign('staticUrl', STATIC_URL);
$smarty->assign('title', 'WSS');

if (0 === stripos($_SERVER['REQUEST_URI'], '/search')) {
	require("../app/search.php"); 
}
else if (0 === stripos($_SERVER['REQUEST_URI'], '/category')) {
	require("../app/category.php"); 
}
else if (0 === stripos($_SERVER['REQUEST_URI'], '/area')) {
	require("../app/area.php"); 
}
else if (0 === stripos($_SERVER['REQUEST_URI'], '/ajaxsearch')) {
	require("../app/ajaxsearch.php"); 
}
else if (0 === stripos($_SERVER['REQUEST_URI'], '/ajaxcate')) {
	require("../app/ajaxcate.php"); 
}
else if (0 === stripos($_SERVER['REQUEST_URI'], '/ajaxarea')) {
	require("../app/ajaxarea.php"); 
}
else if (0 === stripos($_SERVER['REQUEST_URI'], '/ajaxlog')) {
	require("../app/ajaxlog.php"); 
}
else {
	require("../app/top.php");
}
