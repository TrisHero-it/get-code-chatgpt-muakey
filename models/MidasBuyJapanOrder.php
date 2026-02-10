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
            $status = in_array($_GET['status'], ['pending', 'success', 'cancelled']) ? $_GET['status'] : '';
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

    /** Kiểm tra order_id đã tồn tại chưa (bảng orders) */
    public function checkOrderIdExists($order_id)
    {
        if ($order_id === null || $order_id === '') return false;
        $pdo = $this->getConnect4();
        $stmt = $pdo->prepare("SELECT id FROM orders WHERE order_id = ?");
        $stmt->execute([$order_id]);
        return $stmt->fetch() ? true : false;
    }

    public function insert($uid, $card, $image = null, $status = 'pending', $order_id = null)
    {
        $pdo = $this->getConnect4();
        $stmt = $pdo->prepare("INSERT INTO orders (order_id, uid, card, image, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$order_id ?: null, $uid, $card, $image, $status]);
    }

    public function update($id, $uid, $card, $image = null, $status = 'pending', $order_id = null)
    {
        $pdo = $this->getConnect4();
        $stmt = $pdo->prepare("UPDATE orders SET order_id = ?, uid = ?, card = ?, image = ?, status = ? WHERE id = ?");
        $stmt->execute([$order_id ?: null, $uid, $card, $image, $status, $id]);
    }

    public function delete($id)
    {
        $query = "DELETE FROM orders WHERE id = $id";
        $this->getData4($query, false);
    }
}
