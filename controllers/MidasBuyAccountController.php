<?php
require_once "models/MidasBuyAccount.php";

class MidasBuyAccountController extends MidasBuyAccount
{
    public function index()
    {
        $account = new MidasBuyAccount();
        $accounts = $account->index();
        require_once "views/midas-buy/accounts/index.php";
    }

    public function add()
    {
        require_once "views/midas-buy/accounts/add.php";
    }

    public function store()
    {
        $account = new MidasBuyAccount();
        $email = $_POST['email'] ?? '';
        $uid = $_POST['uid'] ?? '';

        if (empty(trim($email)) || empty(trim($uid))) {
            header("Location: ?act=midas-account-add&error=" . urlencode('Email và UID không được để trống!'));
            exit;
        }

        $account->insert($email, $uid);
        header("Location: ?act=midas-accounts");
    }

    public function edit($id)
    {
        $account = new MidasBuyAccount();
        $accountData = $account->getAccountById($id);

        if (!$accountData) {
            header("Location: ?act=midas-accounts&error=" . urlencode('Không tìm thấy tài khoản!'));
            exit;
        }

        $account = $accountData;
        require_once "views/midas-buy/accounts/edit.php";
    }

    public function update2()
    {
        $account = new MidasBuyAccount();
        $id = $_POST['id'] ?? '';
        $email = $_POST['email'] ?? '';
        $uid = $_POST['uid'] ?? '';

        if (empty($id)) {
            header("Location: ?act=midas-accounts&error=" . urlencode('ID tài khoản không hợp lệ!'));
            exit;
        }

        if (empty(trim($email)) || empty(trim($uid))) {
            header("Location: ?act=midas-account-edit&id={$id}&error=" . urlencode('Email và UID không được để trống!'));
            exit;
        }

        $account->update($id, $email, $uid);
        header("Location: ?act=midas-accounts");
    }

    public function delete($id)
    {
        $account = new MidasBuyAccount();
        $account->delete($id);
        header("Location: ?act=midas-accounts");
    }
}
