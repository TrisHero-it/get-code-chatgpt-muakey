<?php
require_once "models/Account.php";

class AccountController extends Account
{
    public function index()
    {
        $account = new Account();
        $accounts = $account->index();
        require_once "views/accounts/index.php";
    }

    public function add()
    {
        require_once "views/accounts/add.php";
    }

    public function store()
    {
        $account = new Account();
        $account->insert($_POST['email'], $_POST['password'], $_POST['type']);
        header("Location: ?act=list");
    }

    public function delete($id)
    {
        $account = new Account();
        $account->delete($id);
        header("Location: ?act=list");
    }
}
