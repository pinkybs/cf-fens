<?php

/**
 * User logic's Operation
 *
 * @package    Admin/Bll
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2009/03/20    zhangxin
 */
final class Admin_Bll_User
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

    /**
     * get user info
     *
     * @param Integer $uid
     * @return array
     */
    public static function getUserInfo($uid)
    {
        require_once 'Admin/Dal/User.php';
        $dalUser = Admin_Dal_User::getDefaultInstance();
        return $dalUser->getUser($uid);
    }

	/**
     * change user login id
     *
     * @param Integer $uid
     * @param string $loginId
     * @return boolean
     */
    public function changeUserLoginId($uid, $loginId)
    {
        if (empty($uid) || empty($loginId)) {
            return false;
        }

        try {
            require_once 'Admin/Dal/User.php';
            $dalUser = Admin_Dal_User::getDefaultInstance();
            $rowUser = $dalUser->getUser($uid);
            //user exist
            if (empty($rowUser)) {
                return false;
            }

            //update login id
            $dalUser->updateUser(array('login_id' => $loginId), $uid);

            return true;
        }
        catch (Exception $e) {
            return false;
        }
    }

 	/**
     * change user password
     *
     * @param Integer $uid
     * @param string $newPass
     * @return boolean
     */
    public function changeUserPassword($uid, $newPass)
    {
        if (empty($uid) || empty($newPass)) {
            return false;
        }

        try {
            require_once 'Admin/Dal/User.php';
            $dalUser = Admin_Dal_User::getDefaultInstance();
            $rowUser = $dalUser->getUser($uid);
            //user exist
            if (empty($rowUser)) {
                return false;
            }

            //update password
            $dalUser->updateUser(array('password' => sha1($newPass)), $uid);

            return true;
        }
        catch (Exception $e) {
            return false;
        }
    }

}