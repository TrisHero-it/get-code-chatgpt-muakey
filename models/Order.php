<?php
require_once "db.php";
class Order extends db
{
    public function index()
    {
        $query = "SELECT * FROM orders ";
        if (isset($_GET['status']) && $_GET['status'] != '') {
            $query .= " WHERE status = '$_GET[status]'";
        }
        $query .= " ORDER BY id DESC";
        return $this->getData($query);
    }

    public function checkOrderExists($order_id)
    {
        $pdo = $this->getConnect();
        $stmt = $pdo->prepare("SELECT id FROM orders WHERE order_id = ?");
        $stmt->execute([$order_id]);
        return $stmt->fetch() ? true : false;
    }

    public function insert($order_id, $username, $password, $backup_code)
    {
        $pdo = $this->getConnect();
        $stmt = $pdo->prepare("INSERT INTO orders (order_id, username, password, backup_code, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$order_id, $username, $password, $backup_code]);
    }

    public function getOrderById($id)
    {
        $pdo = $this->getConnect();
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateOrder($id, $order_id, $username, $password, $backup_code)
    {
        $pdo = $this->getConnect();
        $stmt = $pdo->prepare("UPDATE orders SET order_id = ?, username = ?, password = ?, backup_code = ? WHERE id = ?");
        $stmt->execute([$order_id, $username, $password, $backup_code, $id]);
    }

    public function delete($id)
    {
        $query = "DELETE FROM orders WHERE id = $id";
        $this->getData($query, false);
    }

    public function deleteAllExceptPending()
    {
        $query = "DELETE FROM orders WHERE status != 'pending'";
        $this->getData($query, false);
    }

    public function getTotalMoney()
    {
        $query = "SELECT * FROM moneys WHERE balance >= 0.5 ORDER BY id ASC LIMIT 1";
        $result = $this->getData($query, false);
        return $result ? $result : null;
    }

    public function updateMoney($id, $balance, $code)
    {
        $pdo = $this->getConnect();
        $stmt = $pdo->prepare("UPDATE moneys SET balance = ?, code = ? WHERE id = ?");
        $stmt->execute([$balance, $code, $id]);
    }

    public function updateMoney2($id, $balance)
    {
        $pdo = $this->getConnect();
        $stmt = $pdo->prepare("UPDATE moneys SET balance = ? WHERE id = ?");
        $stmt->execute([$balance, $id]);
    }

    public function insertPaymentCodes($codes)
    {
        $pdo = $this->getConnect();
        $stmt = $pdo->prepare("INSERT INTO moneys (code, balance) VALUES (?, 10)");
        $inserted = 0;
        $skipped = 0;

        foreach ($codes as $code) {
            $code = trim($code);
            if (empty($code)) {
                continue;
            }

            // Kiểm tra code đã tồn tại chưa
            $checkStmt = $pdo->prepare("SELECT id FROM moneys WHERE code = ?");
            $checkStmt->execute([$code]);
            if ($checkStmt->fetch()) {
                $skipped++;
                continue;
            }

            $stmt->execute([$code]);
            $inserted++;
        }

        return ['inserted' => $inserted, 'skipped' => $skipped];
    }

    public function getNextAvailableCode()
    {
        $query = "SELECT * FROM moneys WHERE balance >= 0.5 ORDER BY id ASC LIMIT 1";
        $result = $this->getData($query, false);
        return $result ? $result : null;
    }

    public function getAllPaymentCodes()
    {
        $query = "SELECT * FROM moneys ORDER BY id ASC";
        return $this->getData($query);
    }

    public function deleteAllPaymentCodes()
    {
        $query = "DELETE FROM moneys";
        $this->getData($query, false);
    }

    public function deletePaymentCode($id)
    {
        $query = "DELETE FROM moneys WHERE id = $id";
        $this->getData($query, false);
    }
}
