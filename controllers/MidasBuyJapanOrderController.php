<?php
require_once "models/MidasBuyJapanOrder.php";

class MidasBuyJapanOrderController extends MidasBuyJapanOrder
{
    public function getOrders()
    {
        header('Content-Type: application/json; charset=utf-8');
        $order = new MidasBuyJapanOrder();
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = isset($_GET['per_page']) ? min(100, max(1, (int)$_GET['per_page'])) : 30;
        $orders = $order->getOrdersPaginated($page, $perPage);
        $total = $order->getTotalCount();
        echo json_encode($orders);
    }

    public function index()
    {
        $order = new MidasBuyJapanOrder();

        // Phân trang
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 30;
        $totalCount = $order->getTotalCount();
        $totalPages = ceil($totalCount / $perPage);

        if ($page < 1) $page = 1;
        if ($page > $totalPages && $totalPages > 0) $page = $totalPages;

        $orders = $order->getOrdersPaginated2($page, $perPage);
        $currentPage = $page;

        require_once "views/midas-buy-japan/orders/index.php";
    }

    public function add()
    {
        require_once "views/midas-buy-japan/orders/add.php";
    }

    public function store()
    {
        $order = new MidasBuyJapanOrder();
        $uid = $_POST['uid'] ?? '';
        $card = $_POST['card'] ?? '';
        $image = !empty(trim($_POST['image'] ?? '')) ? trim($_POST['image']) : null;
        $order_id = !empty(trim($_POST['order_id'] ?? '')) ? trim($_POST['order_id']) : null;
        $order_id = ($order_id !== null && is_numeric($order_id)) ? (int)$order_id : null;

        if (empty(trim($uid)) || !is_numeric($uid)) {
            header("Location: ?act=midas-japan-order-add&error=" . urlencode('UID phải là số và không được để trống!'));
            exit;
        }

        if (empty(trim($card))) {
            header("Location: ?act=midas-japan-order-add&error=" . urlencode('Card không được để trống!'));
            exit;
        }

        if (strlen($card) > 30) {
            header("Location: ?act=midas-japan-order-add&error=" . urlencode('Card tối đa 30 ký tự!'));
            exit;
        }

        if ($order_id !== null && $order->checkOrderIdExists($order_id)) {
            header("Location: ?act=midas-japan-order-add&error=" . urlencode('Mã đơn hàng (Order ID) "' . $order_id . '" đã tồn tại trong hệ thống!'));
            exit;
        }

        $status = $_POST['status'] ?? 'pending';
        $validStatuses = ['pending', 'success', 'cancelled', 'refunded'];
        if (!in_array($status, $validStatuses)) $status = 'pending';
        $order->insert($uid, $card, $image, $status, $order_id);
        header("Location: ?act=midas-japan-order-add&success=" . urlencode('Đã thêm đơn hàng thành công!'));
    }

    public function edit($id)
    {
        $orderModel = new MidasBuyJapanOrder();
        $orderData = $orderModel->getOrderById($id);

        if (!$orderData) {
            header("Location: ?act=midas-japan-orders&error=" . urlencode('Không tìm thấy đơn hàng!'));
            exit;
        }

        $order = $orderData;
        require_once "views/midas-buy-japan/orders/edit.php";
    }

    public function updateOrder()
    {
        $order = new MidasBuyJapanOrder();
        $id = $_POST['id'] ?? '';
        $uid = $_POST['uid'] ?? '';
        $card = $_POST['card'] ?? '';
        $image = !empty(trim($_POST['image'] ?? '')) ? trim($_POST['image']) : null;
        $order_id = !empty(trim($_POST['order_id'] ?? '')) ? trim($_POST['order_id']) : null;
        $order_id = ($order_id !== null && is_numeric($order_id)) ? (int)$order_id : null;

        if (empty($id)) {
            header("Location: ?act=midas-japan-orders&error=" . urlencode('ID đơn hàng không hợp lệ!'));
            exit;
        }

        if (empty(trim($uid)) || !is_numeric($uid)) {
            header("Location: ?act=midas-japan-order-edit&id={$id}&error=" . urlencode('UID phải là số và không được để trống!'));
            exit;
        }

        if (empty(trim($card))) {
            header("Location: ?act=midas-japan-order-edit&id={$id}&error=" . urlencode('Card không được để trống!'));
            exit;
        }

        if (strlen($card) > 30) {
            header("Location: ?act=midas-japan-order-edit&id={$id}&error=" . urlencode('Card tối đa 30 ký tự!'));
            exit;
        }

        $status = $_POST['status'] ?? 'pending';
        $validStatuses = ['pending', 'success', 'cancelled', 'refunded'];
        if (!in_array($status, $validStatuses)) $status = 'pending';
        $order->update($id, $uid, $card, $image, $status, $order_id);
        header("Location: ?act=midas-japan-orders");
    }

    public function delete($id)
    {
        $order = new MidasBuyJapanOrder();
        $order->delete($id);
        header("Location: ?act=midas-japan-orders");
    }

    public function refund($id)
    {
        $order = new MidasBuyJapanOrder();
        $orderData = $order->getOrderById($id);

        if (!$orderData) {
            header("Location: ?act=midas-japan-orders&error=" . urlencode('Không tìm thấy đơn hàng!'));
            exit;
        }

        if ($orderData['status'] !== 'cancelled') {
            header("Location: ?act=midas-japan-orders&error=" . urlencode('Chỉ có thể hoàn tiền cho đơn hàng đã huỷ!'));
            exit;
        }

        $order->updateStatus($id, 'refunded');
        header("Location: ?act=midas-japan-orders&success=" . urlencode('Đã hoàn tiền thành công!'));
    }
}
