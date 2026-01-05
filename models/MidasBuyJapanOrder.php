<?php
require_once "db.php";
class MidasBuyJapanOrder extends db
{
    public function getOrdersPaginated($page = 1, $perPage = 30)
    {
        $offset = ($page - 1) * $perPage;
        $query = "SELECT * FROM orders WHERE status = 'pending'";
        $query .= " ORDER BY id DESC LIMIT $perPage OFFSET $offset";
        return $this->getData4($query);
    }

    public function getOrdersPaginated2($page = 1, $perPage = 30)
    {
        $offset = ($page - 1) * $perPage;
        $query = "SELECT * FROM orders";
        $query .= " ORDER BY id DESC LIMIT $perPage OFFSET $offset";
        return $this->getData4($query);
    }

    public function getTotalCount()
    {
        $query = "SELECT COUNT(*) as total FROM orders ";
        if (isset($_GET['status']) && $_GET['status'] != '') {
            $status = in_array($_GET['status'], ['pending', 'success']) ? $_GET['status'] : '';
            if ($status) $query .= " WHERE status = '$status'";
        }
        $result = $this->getData4($query, false);
        return $result ? (int)$result['total'] : 0;
    }

    public function getOrderById($id)
    {
        $pdo = $this->getConnect4();
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($uid, $card, $image = null, $status = 'pending')
    {
        $pdo = $this->getConnect4();
        $stmt = $pdo->prepare("INSERT INTO orders (uid, card, image, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$uid, $card, $image, $status]);
    }

    public function update($id, $uid, $card, $image = null, $status = 'pending')
    {
        $pdo = $this->getConnect4();
        $stmt = $pdo->prepare("UPDATE orders SET uid = ?, card = ?, image = ?, status = ? WHERE id = ?");
        $stmt->execute([$uid, $card, $image, $status, $id]);
    }

    public function delete($id)
    {
        $query = "DELETE FROM orders WHERE id = $id";
        $this->getData4($query, false);
    }
}
