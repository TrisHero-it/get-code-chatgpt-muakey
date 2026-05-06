<?php
require_once "db.php";
class CodeMidasBuy extends db
{
    public function getCodeByToken(int $token)
    {
        $query = "SELECT * FROM codes WHERE token = $token LIMIT 1";
        return $this->getData($query, false);
    }

    public function insertCode(int $token, string $code)
    {
        $query = "INSERT INTO codes (token, code) VALUES ($token, '$code')";
        $this->getData($query, false);
    }

    public function getAllCodes()
    {
        $query = "SELECT * FROM codes ORDER BY id DESC";
        return $this->getData($query);
    }
}
