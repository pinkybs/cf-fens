<?php

/**
 * Admin Auth Controller(modules/admin/controllers/Admin_AuthController.php)
 * Fens Admin Auth Controller
 *
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create    2009/03/20    zhangxin
 */
class Admin_AuthController extends Zend_Controller_Action
{
    /**
     * admin website base URL
     * @var string
     */
    protected $_baseUrl;

    /**
     * page init
     *
     */
    function init()
    {
        //get admin website base url
        $this->_baseUrl = $this->_request->getBaseUrl();
        $this->view->baseUrl = $this->_baseUrl;
        $this->view->staticUrl = Zend_Registry::get('static');
        $this->view->version = Zend_Registry::get('version');
    }

    /**
     * auth controller index action
     *
     */
    public function indexAction()
    {
        $this->_forward('login', 'auth', 'admin');
        return;
    }

    /**
     * auth controller login action
     *
     */
    public function loginAction()
    {
        //if is post
        if ($this->_request->isPost()) {
            //get posted data from client
            $loginId = $this->_request->getPost('txtId');
            $password = $this->_request->getPost('txtPw');
            $this->view->errmsg = '';
            $this->view->adminId = $loginId;

            //check validate
            if (empty($loginId) || empty($password)) {
                $this->view->errmsg = 'ログインID、あるいはパスワードを入力して下さい。';
            }
            else if (strlen($loginId) > 40) {
                $this->view->errmsg = 'ログインIDは40字以下で入力してください。';
            }
            else if (strlen($password) > 12 || strlen($password) < 6) {
                $this->view->errmsg = 'パスワードは6字以上12字以下で入力してください。';
            }

            else {
                require_once 'Admin/Bll/Auth.php';
                $result = Admin_Bll_Auth::authenticate($loginId, sha1($password));

                if ($result == 1) {
                    //$user = Admin_Bll_Auth::getIdentity();
                    $this->_redirect($this->_baseUrl . '/manage');
                    return;
                }
                //reject to pass
                else {
                    $this->view->errmsg = 'ログインできませんでした。ログインID、あるいはパスワードが異なります。';
                }
            }
        }
        else {
            require_once 'Admin/Bll/Auth.php';
            $auth = Admin_Bll_Auth::getAuthInstance();
            if ($auth->hasIdentity()) {
                $this->_redirect($this->_baseUrl . '/manage');
                return;
            }
        }

        $this->view->title = 'ログイン｜FENS ADMIN';
        $this->render();
    }

    /**
     * auth controller logout action
     *
     */
    public function logoutAction()
    {
        //clear admin session
        require_once 'Admin/Bll/Auth.php';
        $auth = Admin_Bll_Auth::getAuthInstance();
        if ($auth->hasIdentity()) {
            //clear Session
            $auth->clearIdentity();
        }

        Zend_Session::regenerateId();
        //$this->_redirect($this->_baseUrl . '/');
        //return;
        $this->view->title = 'ログアウト｜FENS ADMIN';
        $this->render();
    }

    /**
     * call
     *
     */
    function __call($methodName, $args)
    {
        return $this->_forward('notfound', 'error', 'admin');
    }
}