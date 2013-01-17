<?php

require_once 'Admin/Dal/Abstract.php';

/**
 * Dal User
 * AdminUser Data Access Layer
 *
 * @package    Admin/Dal
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2009/03/20    zhangxin
 */
class Admin_Dal_User extends Admin_Dal_Abstract
{

    protected static $_instance;

    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * get user info
     *
     * @param Integer $uid
     * @return array
     */
    public function getUser($uid)
    {
        $sql = "SELECT * FROM admin_user WHERE uid=:uid";

        return $this->_rdb->fetchRow($sql, array('uid' => $uid));
    }

    /**
     * get user info by loginid
     *
     * @param string $loginId
     * @return array
     */
    public function getUserByLoginId($loginId)
    {
        $sql = "SELECT * FROM admin_user WHERE login_id=:login_id";

        return $this->_rdb->fetchRow($sql, array('login_id' => $loginId));
    }

    /**
     * insert user
     *
     * @param array $info
     * @return integer
     */
    public function insertUser($info)
    {
        $this->_wdb->insert('admin_user', $info);
        return $this->_wdb->lastInsertId();
    }

    /**
     * update user
     *
     * @param array $info
     * @param integer $id
     * @return integer
     */
    public function updateUser($info, $id)
    {
        $where = $this->_wdb->quoteInto('uid = ?', $id);
        return $this->_wdb->update('admin_user', $info, $where);
    }

    /**
     * delete user
     *
     * @param integer $id
     * @return integer
     */
    public function deleteUser($id)
    {
        $sql = "DELETE FROM admin_user WHERE uid=:uid ";
        return $this->_wdb->query($sql, array('uid' => $id));
    }

}