<?php
require_once "models/CodeMidasBuy.php";

class CodeTokenMidasBuyController extends CodeMidasBuy
{
    public function index()
    {
        $codes = $this->getAllCodes();
        require_once "views/midas-buy-token/codes/index.php";
    }
}
