<?php
require_once "db.php";

class WwmOrder extends db
{
    /**
     * Kiểm tra order_id đã tồn tại chưa
     */
    public function checkOrderIdExists($order_id)
    {
        $pdo = $this->getConnect();
        $stmt = $pdo->prepare("SELECT id FROM wwm_orders WHERE order_id = ?");
        $stmt->execute([$order_id]);
        return $stmt->fetch() ? true : false;
    }

    /**
     * Thêm bản ghi mới vào bảng wwm_orders
     */
    public function insert($order_id, $uid, $product_id, $category = '', $server = '', $region = '')
    {
        $pdo = $this->getConnect();
        $stmt = $pdo->prepare("INSERT INTO wwm_orders (order_id, uid, product_id, category, server, region, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$order_id, $uid, $product_id, $category, $server, $region]);
    }

    /**
     * Lấy danh sách tất cả wwm_orders
     */
    public function index($category = null)
    {
        $query = "SELECT * FROM wwm_orders";
        if ($category !== null && $category !== '') {
            $pdo = $this->getConnect();
            $stmt = $pdo->prepare("SELECT * FROM wwm_orders WHERE category = ? ORDER BY id DESC");
            $stmt->execute([$category]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        $query .= " ORDER BY id DESC";
        return $this->getData($query);
    }

    /**
     * Lấy danh sách các category có trong database
     */
    public function getCategories()
    {
        $query = "SELECT DISTINCT category FROM wwm_orders WHERE category IS NOT NULL AND category != '' ORDER BY category ASC";
        $result = $this->getData($query);
        return array_column($result, 'category');
    }

    /**
     * Xóa một wwm_order theo ID
     */
    public function delete($id)
    {
        $query = "DELETE FROM wwm_orders WHERE id = $id";
        $this->getData($query, false);
    }

    public function getOrders()
    {
        $query = "SELECT * FROM wwm_orders WHERE status = 'pending' limit 1";
        return $this->getData($query);
    }

    public function updateOrder($id, $status)
    {
        $pdo = $this->getConnect();
        $stmt = $pdo->prepare("UPDATE wwm_orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }

    /**
     * Lấy một wwm_order theo ID
     */
    public function getOrderById($id)
    {
        $pdo = $this->getConnect();
        $stmt = $pdo->prepare("SELECT * FROM wwm_orders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cập nhật wwm_order với đầy đủ thông tin bao gồm image
     */
    public function updateOrderFull($id, $order_id, $uid, $product_id, $category = '', $server = '', $region = '', $status = null, $image = null)
    {
        $pdo = $this->getConnect();

        // Nếu status không được truyền vào (null), thì không cập nhật status
        if ($status === null) {
            $stmt = $pdo->prepare("UPDATE wwm_orders SET order_id = ?, uid = ?, product_id = ?, category = ?, server = ?, region = ?, image = ? WHERE id = ?");
            $stmt->execute([$order_id, $uid, $product_id, $category, $server, $region, $image, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE wwm_orders SET order_id = ?, uid = ?, product_id = ?, category = ?, server = ?, region = ?, status = ?, image = ? WHERE id = ?");
            $stmt->execute([$order_id, $uid, $product_id, $category, $server, $region, $status, $image, $id]);
        }
    }

    /**
     * Xóa tất cả wwm_orders ngoại trừ các orders có status là 'pending'
     */
    public function deleteAllExceptPending()
    {
        $query = "DELETE FROM wwm_orders WHERE status != 'pending' OR status IS NULL";
        $this->getData($query, false);
    }
}
