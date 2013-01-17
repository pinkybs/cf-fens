<?php

/**
 * SiteCategory logic's Operation
 *
 * @package    Admin/Bll
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2009/03/25    zhangxin
 */
final class Admin_Bll_SiteCategory
{
    /**
     * db config
     * @var array
     */
    protected $_config;

    /**
     * db read adapter
     * @var Zend_Db_Abstract
     */
    protected $_rdb;

    /**
     * db write adapter
     * @var Zend_Db_Abstract
     */
    protected $_wdb;

    protected static $_instance;

    /**
     * init the user's variables
     *
     * @param array $config ( config info )
     */
    public function __construct($config = null)
    {
        if (is_null($config)) {
            $config = Zend_Registry::get('dbConfig');
        }

        $this->_config = $config;
        $this->_rdb = $config['readDB'];
        $this->_wdb = $config['writeDB'];
    }

    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * go up an order by category id
     *
     * @param integer $id
     * @return boolean
     */
    public function upSortCategory($id)
    {
        try {
            require_once 'Admin/Dal/SiteCategory.php';
            $dalCate = Admin_Dal_SiteCategory::getDefaultInstance();
            $rowCurCate = $dalCate->getCategoryById($id);
            if (empty($rowCurCate)) {
                return false;
            }

            $rowLastCate = $dalCate->getLastCategoryBeforeSelSort($rowCurCate['sort']);
            //already first row, nothing to do.
            if (empty($rowLastCate)) {
                return true;
            }
            $tmpSort = $rowCurCate['sort'];

            $this->_wdb->beginTransaction();
            //cur  category　↑
            $dalCate->updateCategory(array('sort' => $rowLastCate['sort']), $id);
            //last category ↓
            $dalCate->updateCategory(array('sort' => $tmpSort), $rowLastCate['id']);
            $this->_wdb->commit();
            return true;
        }
        catch (Exception $e) {
            $this->_wdb->rollBack();
            return false;
        }
    }

    /**
     * go down an order by category id
     *
     * @param integer $id
     * @return boolean
     */
    public function downSortCategory($id)
    {
        try {
            require_once 'Admin/Dal/SiteCategory.php';
            $dalCate = Admin_Dal_SiteCategory::getDefaultInstance();
            $rowCurCate = $dalCate->getCategoryById($id);
            if (empty($rowCurCate)) {
                return false;
            }

            $rowNextCate = $dalCate->getNextCategoryAfterSelSort($rowCurCate['sort']);
            //already last row, nothing to do.
            if (empty($rowNextCate)) {
                return true;
            }
            $tmpSort = $rowCurCate['sort'];

            $this->_wdb->beginTransaction();
            //cur  category　↓
            $dalCate->updateCategory(array('sort' => $rowNextCate['sort']), $id);
            //next category ↑
            $dalCate->updateCategory(array('sort' => $tmpSort), $rowNextCate['id']);
            $this->_wdb->commit();
            return true;
        }
        catch (Exception $e) {
            $this->_wdb->rollBack();
            return false;
        }
    }

    /**
     * delete category by id
     *
     * @param integer $id
     * @return boolean
     */
    public function delCategory($id)
    {
        try {
            require_once 'Admin/Dal/SiteCategory.php';
            $dalCate = Admin_Dal_SiteCategory::getDefaultInstance();
            $rowCurCate = $dalCate->getCategoryById($id);
            if (empty($rowCurCate)) {
                return false;
            }

            require_once 'Admin/Dal/Site.php';
            $dalSite = Admin_Dal_Site::getDefaultInstance();

            $this->_wdb->beginTransaction();
            //delete category's site
            $dalSite->clearSiteCateoryByCategoryId($id);
            //delete category
            $dalCate->deleteCategory($id);
            $this->_wdb->commit();
            return true;
        }
        catch (Exception $e) {
            $this->_wdb->rollBack();
            return false;
        }
    }

    /**
     * add category
     *
     * @param array $info
     * @return boolean
     */
    public function addCategory($info)
    {
        try {
            require_once 'Admin/Dal/SiteCategory.php';
            $dalCate = Admin_Dal_SiteCategory::getDefaultInstance();

            $this->_wdb->beginTransaction();
            $id = $dalCate->insertCategory($info);
            $id = $dalCate->updateCategory(array('sort' => $id), $id);
            $this->_wdb->commit();
            return true;
        }
        catch (Exception $e) {
            $this->_wdb->rollBack();
            return false;
        }
    }

    /**
     * edit category
     *
     * @param array $info
     * @param integer $id
     * @return boolean
     */
    public function editCategory($info, $id)
    {
        try {
            require_once 'Admin/Dal/SiteCategory.php';
            $dalCate = Admin_Dal_SiteCategory::getDefaultInstance();
            $rowCate = $dalCate->getCategoryById($id);
            if (empty($rowCate)) {
                return false;
            }

            $this->_wdb->beginTransaction();
            $id = $dalCate->updateCategory($info, $id);
            $this->_wdb->commit();
            return true;
        }
        catch (Exception $e) {
            $this->_wdb->rollBack();
            return false;
        }
    }

}