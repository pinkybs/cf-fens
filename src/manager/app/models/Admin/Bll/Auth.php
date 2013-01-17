<?php

/**
 * Auth
 * authenticate,getIdentity,loaduser
 *
 * @package    Admin/Bll
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2009/03/20    zhangxin
 */
final class Admin_Bll_Auth
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
     * PC Authentication
     *
     * @param string $loginId
     * @param string $password
     * @return boolean
     */
    public static function authenticate($loginId, $password)
    {
        require_once 'Zend/Auth/Adapter/DbTable.php';
        $db = Zend_Registry::get('db');

        $authAdapter = new Zend_Auth_Adapter_DbTable($db);
        $authAdapter->setTableName('admin_user');
        $authAdapter->setIdentityColumn('login_id');
        $authAdapter->setCredentialColumn('password');

        $authAdapter->setIdentity($loginId);
        $authAdapter->setCredential($password);

        //do the Authentication
        $auth = Zend_Auth::getInstance();
        $result = $authAdapter->authenticate();
        if ($result->isValid()) {
            $user = $authAdapter->getResultRowObject(array('uid', 'login_id', 'name', 'status'));

            //check status
            if (1 != $user->status) {
                return 0;
            }
            $adminStorage = new Zend_Auth_Storage_Session('Zend_Auth_Admin');
            $auth->setStorage($adminStorage);
            $auth->getStorage()->write($user->uid);
            return 1;
        }
        else {
            return 0;
        }
    }

    public static function getAuthInstance()
    {
        $auth = Zend_Auth::getInstance();
        $adminStorage = new Zend_Auth_Storage_Session('Zend_Auth_Admin');
        $auth->setStorage($adminStorage);
        return $auth;
    }

    /**
     * get user identity
     *
     * @return class user
     */
    public static function getIdentity()
    {
        $uid = self::getAuthInstance()->getIdentity();

        require_once 'Admin/Bll/User.php';
        $userArray = Admin_Bll_User::getUserInfo($uid);
        $user = new stdClass();
        if (is_array($userArray)) {
            foreach ($userArray as $resultColumn => $resultValue) {
                if ($resultColumn != 'password') {
                    $user->{$resultColumn} = $resultValue;
                }
            }
        }

        return $user;
    }

}