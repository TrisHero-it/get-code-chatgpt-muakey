<?php
require_once "db.php";
class MidasBuyAccount extends db
{
    public function index()
    {
        $query = "SELECT * FROM accounts ORDER BY id DESC";
        return $this->getData3($query);
    }

    public function getAccountById($id)
    {
        $pdo = $this->getConnect3();
        $stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($email, $uid)
    {
        $pdo = $this->getConnect3();
        $stmt = $pdo->prepare("INSERT INTO accounts (email, uid) VALUES (?, ?)");
        $stmt->execute([$email, $uid]);
    }

    public function update($id, $email, $uid)
    {
        $pdo = $this->getConnect3();
        $stmt = $pdo->prepare("UPDATE accounts SET email = ?, uid = ? WHERE id = ?");
        $stmt->execute([$email, $uid, $id]);
    }

    public function delete($id)
    {
        $query = "DELETE FROM accounts WHERE id = $id";
        $this->getData3($query, false);
    }
}
