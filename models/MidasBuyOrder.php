<?php
require_once "db.php";
class MidasBuyOrder extends db
{
    public function getOrdersPaginated($page = 1, $perPage = 30)
    {
        $offset = ($page - 1) * $perPage;
        $query = "SELECT * FROM orders ";
        if (isset($_GET['status']) && $_GET['status'] != '') {
            $query .= " WHERE status = '$_GET[status]'";
        }
        $query .= " ORDER BY id DESC LIMIT $perPage OFFSET $offset";
        return $this->getData3($query);
    }

    public function getTotalCount()
    {
        $query = "SELECT COUNT(*) as total FROM orders ";
        if (isset($_GET['status']) && $_GET['status'] != '') {
            $query .= " WHERE status = '$_GET[status]'";
        }
        $result = $this->getData3($query, false);
        return $result ? (int)$result['total'] : 0;
    }

    public function getOrderById($id)
    {
        $pdo = $this->getConnect3();
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkOrderExists($order_id, $excludeId = null)
    {
        $pdo = $this->getConnect3();
        if ($excludeId) {
            $stmt = $pdo->prepare("SELECT id FROM orders WHERE order_id = ? AND id != ?");
            $stmt->execute([$order_id, $excludeId]);
        } else {
            $stmt = $pdo->prepare("SELECT id FROM orders WHERE order_id = ?");
            $stmt->execute([$order_id]);
        }
        return $stmt->fetch() ? true : false;
    }

    public function insert($order_id, $uid, $token)
    {
        $pdo = $this->getConnect3();
        $stmt = $pdo->prepare("INSERT INTO orders (order_id, uid, token, status, created_at) VALUES (?, ?, ?, 'pending', NOW())");
        $stmt->execute([$order_id, $uid, $token]);
    }

    public function update($id, $order_id, $uid, $token, $status)
    {
        $pdo = $this->getConnect3();
        $stmt = $pdo->prepare("UPDATE orders SET order_id = ?, uid = ?, token = ?, status = ? WHERE id = ?");
        $stmt->execute([$order_id, $uid, $token, $status, $id]);
    }

    public function delete($id)
    {
        $query = "DELETE FROM orders WHERE id = $id";
        $this->getData3($query, false);
    }

    public function deleteAllExceptPending()
    {
        $query = "DELETE FROM orders WHERE status != 'pending'";
        $this->getData3($query, false);
    }
}
