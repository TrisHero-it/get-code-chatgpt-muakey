<?php
require_once "db.php";
class MidasBuyToken extends db
{
    public function getOrdersPaginated($page = 1, $perPage = 30)
    {
        $offset = ($page - 1) * $perPage;
        $query = "SELECT * FROM midasbuy_token_orders";
        $query .= " ORDER BY id DESC LIMIT $perPage OFFSET $offset";
        return $this->getData($query);
    }

    public function getTotalCount()
    {
        $query = "SELECT COUNT(*) as count FROM midasbuy_token_orders";
        $result = $this->getData($query, false);
        return $result['count'] ?? 0;
    }

    public function storeOrder(array $data)
    {
        $order_id = $data['order_id'] ?? '';
        $uid = $data['uid'] ?? '';
        $product_id = $data['product_id'] ?? '';
        $code = $data['code'] ?? '';
        $image = $data['image'] ?? '';
        $sales_agent_id = $data['sales_agent_id'] ?? null;
        $status = $data['status'] ?? 'pending';

        $query = "INSERT INTO midasbuy_token_orders (order_id, uid, product_id, code, image, sale_agent_id, status) 
        VALUES ($order_id, '$uid', '$product_id', '$code', '$image', " . ($sales_agent_id ? $sales_agent_id : 'NULL') . ", '$status')";
        return $this->getData($query, false);
    }

    public function updateOrderById($id, array $data)
    {
        $order_id = $data['order_id'] ?? '';
        $uid = $data['uid'] ?? '';
        $token = $data['token'] ?? '';
        $code = $data['code'] ?? '';
        $image = $data['image'] ?? null;
        $sales_agent_id = $data['sales_agent_id'] ?? null;
        $status = $data['status'] ?? 'pending';

        $query = "UPDATE midasbuy_token_orders 
        SET order_id = $order_id, 
        uid = '$uid', 
        product_id = '$token', 
        code = '$code', 
        image = '$image', 
        sale_agent_id = " . ($sales_agent_id ? $sales_agent_id : 'NULL') . ", 
        status = '$status' WHERE id = $id";

        return $this->getData($query, false);
    }

    public function updateOrderStatus($order_id, $status)
    {
        $query = "UPDATE midasbuy_token_orders SET status = '$status' WHERE order_id = $order_id";
        return $this->getData($query, false);
    }

    function getOrderPending()
    {
        $query = "SELECT * FROM midasbuy_token_orders WHERE status = 'pending' limit 1";
        return $this->getData($query, false);
    }

    public function getOrderById($id)
    {
        $query = "SELECT * FROM midasbuy_token_orders WHERE id = $id";
        return $this->getData($query, false);
    }

    public function deleteOrder($order_id)
    {
        $query = "DELETE FROM midasbuy_token_orders WHERE id = $order_id";
        return $this->getData($query, false);
    }
}
