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
        $query = "UPDATE orders SET status = '$status' WHERE id = $id";
        $this->getData($query, false);
    }
}
