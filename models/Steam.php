<?php
require_once "db.php";
class Steam extends db
{
    public function getOrders()
    {
        $query = "SELECT * FROM orders where status = 'pending'";
        return $this->getData($query);
    }

    public function updateOrder($id, $status)
    {
        $pdo = $this->getConnect();
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }
}
