<?php

/**
 * Admin System Setting Controller(modules/admin/controllers/Admin_SettingController.php)
 * Linno Admin System Setting Controller
 *
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create    2009/03/23    zhangxin
 */
class Admin_SettingController extends MyLib_Zend_Controller_Action_Admin
{

    /**
     * post-Initialize
     * called after parent::init method execution.
     * it can override
     * @return void
     */
    public function postInit()
    {
    }

    /**
     * sys setting controller index action
     *
     */
    public function indexAction()
    {
        return $this->_forward('top', 'setting', 'admin');
    }

    /**
     * sys setting controller top action
     *
     */
    public function topAction()
    {
        $this->view->title = 'FENS様検索エンジン管理システム｜FENS';
        $this->render();
    }

    /**
     * sys setting controller set crawl info action
     *
     */
    public function crawlAction()
    {
        require_once 'Admin/Dal/CrawlSetting.php';
        $dalSet = Admin_Dal_CrawlSetting::getDefaultInstance();

        //if is post
        if ($this->_request->isPost()) {
            //change crawl setting
            $selRate = (int)$this->_request->getPost('selRate');
            $selCount = (int)$this->_request->getPost('selCount');
            $selRateAll = (int)$this->_request->getPost('selRateAll');

            if (empty($selRate) || empty($selCount) || empty($selRateAll)) {
                $this->view->errMsg = '入力内容に誤りがあります。';
            }
            //update db
            else {
                $rowSet = $dalSet->getCrawlSetting();
                if (empty($rowSet)) {
                    $dalSet->insertCrawlSetting(array('number' => $selCount, 'timing' => $selRate, 'new_timing' => $selRateAll));
                }
                else {
                    $dalSet->updateCrawlSetting(array('number' => $selCount, 'timing' => $selRate, 'new_timing' => $selRateAll));
                }
                $this->view->completeMsg = 'クローリング設定が完了しました。';
            }
        }
        //init page
        $this->view->crawlSet = $dalSet->getCrawlSetting();
        $this->view->title = 'FENS様検索エンジン管理システム｜FENS';
        $this->render();
    }

	/**
     * sys setting controller set sync db info action
     *
     */
    public function syncdbAction()
    {
        require_once 'Admin/Dal/CrawlSetting.php';
        $dalSet = Admin_Dal_CrawlSetting::getDefaultInstance();

        $this->view->disableSync = 0;
        //if is post
        if ($this->_request->isPost()) {
            $btnSetting = $this->_request->getPost('btnSetting');
            $hidSync = (int)$this->_request->getPost('hidSync');
            $hidTk = $this->_request->getPost('hidTk');

            //check is repeat submit
            if (isset($_SESSION['VALIDATE_SYNCDB_GUID']) && $hidTk == $_SESSION['VALIDATE_SYNCDB_GUID']) {
                //synchronize db
                if (empty($btnSetting) && (1 == $hidSync)) {
                    //synchronize already begin
                    if ($dalSet->isAnySyncDbExcuting()) {
                        $this->view->completeMsg1 = 'DB同期処理中です。。。お待ちでください。';
                        $this->view->disableSync = 1;
                    }
                    //synchronize db right now
                    else {
                        try {

                            $cmd = Zend_Registry::get('cmdsyncdb')
                                 . ' -a -c '
                                 . Zend_Registry::get('cmdconfigpath')
                                 . ' > /dev/null &';
                            info_log('Manual SyncDb time:' . date('Y-m-d H:i:s') . ' Path:' . $cmd, 'sync-db');
                            system($cmd);
                        }
                        catch (Exception $e) {
                            info_log('Error: ' . $e->getMessage(), 'sync-db');
                        }
                        $this->view->completeMsg1 = 'DB同期処理を開始しました。最終的に同期処理が完了したことを確認するには、再度この画面を確認して下さい。';
                        $this->view->disableSync = 1;
                    }
                }

                //change sync db setting
                else if (!empty($btnSetting)) {
                    $selSync = (int)$this->_request->getPost('selSync');

                    if (empty($selSync)) {
                        $this->view->errMsg = '入力内容に誤りがあります。';
                    }
                    //update db
                    else {
                        $rowSet = $dalSet->getCrawlSetting();
                        if (empty($rowSet)) {
                            $dalSet->insertCrawlSetting(array('db_synchronize_timing' => $selSync));
                        }
                        else {
                            $dalSet->updateCrawlSetting(array('db_synchronize_timing' => $selSync));
                        }
                        $this->view->completeMsg = 'DB同期設定が完了しました。';
                    }
                    //synchronize already begin
                    if ($dalSet->isAnySyncDbExcuting()) {
                        $this->view->completeMsg1 = 'DB同期処理中です。。。お待ちでください。';
                        $this->view->disableSync = 1;
                    }
                }
            }

        }
        else {
            //if synchronize already begin
            if ($dalSet->isAnySyncDbExcuting()) {
                $this->view->completeMsg1 = 'DB同期処理中です。。。お待ちでください。';
                $this->view->disableSync = 1;
            }
        }
        //init page
        $_SESSION['VALIDATE_SYNCDB_GUID'] = Admin_Bll_Secret::getUUID();
        $this->view->validTk = $_SESSION ['VALIDATE_SYNCDB_GUID'];
        $this->view->crawlSet = $dalSet->getCrawlSetting();
        $this->view->lastSyncDate = $dalSet->getLastSyncDbLogTime();
        $this->view->title = 'FENS様検索エンジン管理システム｜FENS';
        $this->render();
    }

    /**
     * sys setting controller changepass action
     *
     */
    public function changepassAction()
    {
        $this->view->userId = $this->_user->login_id;

        //if is post
        if ($this->_request->isPost()) {
            $mode = (int)$this->_request->getPost('hidMode');

            //change id
            if (1 == $mode) {
                $txtId = $this->_request->getPost('txtId');
                if (empty($txtId)) {
                    $this->view->errMsg1 = '入力内容に誤りがあります。';
                }
                else if (strlen($txtId) > 40) {
                    $this->view->errMsg1 = '入力内容に誤りがあります。';
                }
                //update db
                else {
                    require_once 'Admin/Bll/User.php';
                    $bllUser = new Admin_Bll_User();
                    $rst = $bllUser->changeUserLoginId($this->_user->uid, $txtId);
                    if ($rst) {
                        $this->view->completeMsg1 = 'システムログインID設定が完了しました。';
                    }
                    else {
                        $this->view->errMsg1 = 'ログインID設定失敗しました。';
                    }
                }
                $this->view->userId = $txtId;
            } //end change id


            //change pass
            else if (2 == $mode) {
                $txtNewPw = $this->_request->getPost('txtNewPw');
                $txtConfirmPw = $this->_request->getPost('txtConfirmPw');
                if (empty($txtNewPw) || empty($txtConfirmPw)) {
                    $this->view->errMsg2 = '入力内容に誤りがあります。';
                }
                else if (strlen($txtNewPw) > 12 || strlen($txtConfirmPw) > 12) {
                    $this->view->errMsg2 = '入力内容に誤りがあります。';
                }
                else if ($txtNewPw != $txtConfirmPw) {
                    $this->view->errMsg2 = '入力内容に誤りがあります。';
                }
                //update db
                else {
                    require_once 'Admin/Bll/User.php';
                    $bllUser = new Admin_Bll_User();
                    $rst = $bllUser->changeUserPassword($this->_user->uid, $txtNewPw);
                    if ($rst) {
                        $this->view->completeMsg2 = 'システムログインパスワード設定が完了しました。';
                    }
                    else {
                        $this->view->errMsg2 = 'パスワード設定失敗しました。';
                    }
                }
            } //end change pass
        }

        //init page
        $this->view->title = 'FENS様検索エンジン管理システム｜FENS';
        $this->render();
    }

    /**
     * preDispatch
     *
     */
    function preDispatch()
    {
    }
}