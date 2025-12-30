<?php
require_once "models/MidasBuyOrder.php";

class MidasBuyOrderController extends MidasBuyOrder
{
    public function index()
    {
        $order = new MidasBuyOrder();

        // Phân trang
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 30;
        $totalCount = $order->getTotalCount();
        $totalPages = ceil($totalCount / $perPage);

        // Đảm bảo page hợp lệ
        if ($page < 1) $page = 1;
        if ($page > $totalPages && $totalPages > 0) $page = $totalPages;

        $orders = $order->getOrdersPaginated($page, $perPage);
        $currentPage = $page;

        require_once "views/midas-buy/orders/index.php";
    }

    public function add()
    {
        require_once "views/midas-buy/orders/add.php";
    }

    public function store()
    {
        $order = new MidasBuyOrder();
        $order_id = $_POST['order_id'] ?? '';
        $uid = $_POST['uid'] ?? '';
        $token = $_POST['token'] ?? '';

        if (empty(trim($order_id))) {
            header("Location: ?act=midas-order-add&error=" . urlencode('Mã đơn hàng không được để trống!'));
            exit;
        }

        // Kiểm tra order_id đã tồn tại chưa
        if ($order->checkOrderExists(trim($order_id))) {
            header("Location: ?act=midas-order-add&error=" . urlencode('Mã đơn hàng đã tồn tại!'));
            exit;
        }

        if (empty(trim($uid))) {
            header("Location: ?act=midas-order-add&error=" . urlencode('UID không được để trống!'));
            exit;
        }

        if (empty(trim($token)) || !is_numeric($token)) {
            header("Location: ?act=midas-order-add&error=" . urlencode('Token phải là số và không được để trống!'));
            exit;
        }

        $order->insert($order_id, $uid, $token);
        header("Location: ?act=midas-orders");
    }

    public function edit($id)
    {
        $order = new MidasBuyOrder();
        $orderData = $order->getOrderById($id);

        if (!$orderData) {
            header("Location: ?act=midas-orders&error=" . urlencode('Không tìm thấy đơn hàng!'));
            exit;
        }

        $order = $orderData;
        require_once "views/midas-buy/orders/edit.php";
    }

    public function updateOrder()
    {
        $order = new MidasBuyOrder();
        $id = $_POST['id'] ?? '';
        $order_id = $_POST['order_id'] ?? '';
        $uid = $_POST['uid'] ?? '';
        $token = $_POST['token'] ?? '';
        $status = $_POST['status'] ?? 'pending';

        if (empty($id)) {
            header("Location: ?act=midas-orders&error=" . urlencode('ID đơn hàng không hợp lệ!'));
            exit;
        }

        if (empty(trim($order_id))) {
            header("Location: ?act=midas-order-edit&id={$id}&error=" . urlencode('Mã đơn hàng không được để trống!'));
            exit;
        }

        // Kiểm tra order_id có bị trùng với đơn hàng khác không
        if ($order->checkOrderExists(trim($order_id), $id)) {
            header("Location: ?act=midas-order-edit&id={$id}&error=" . urlencode('Mã đơn hàng đã tồn tại!'));
            exit;
        }

        if (empty(trim($uid))) {
            header("Location: ?act=midas-order-edit&id={$id}&error=" . urlencode('UID không được để trống!'));
            exit;
        }

        if (empty(trim($token)) || !is_numeric($token)) {
            header("Location: ?act=midas-order-edit&id={$id}&error=" . urlencode('Token phải là số và không được để trống!'));
            exit;
        }

        // Validate status
        $validStatuses = ['pending', 'success'];
        if (!in_array($status, $validStatuses)) {
            $status = 'pending';
        }

        $order->update($id, $order_id, $uid, $token, $status);
        header("Location: ?act=midas-orders");
    }

    public function delete($id)
    {
        $order = new MidasBuyOrder();
        $order->delete($id);
        header("Location: ?act=midas-orders");
    }

    public function deleteAllExceptPending()
    {
        $order = new MidasBuyOrder();
        $order->deleteAllExceptPending();
        header("Location: ?act=midas-orders");
    }
}
