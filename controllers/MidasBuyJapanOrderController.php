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

    /**
     * API lấy ảnh đơn hàng theo ID.
     * Query: ?act=get-midas-japan-order-image&id={id}
     * Trả về: { success, id, image }
     */
    public function getOrderImage()
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID đơn hàng không hợp lệ!']);
            return;
        }

        $order = new MidasBuyJapanOrder();
        $orderData = $order->getOrderById($id);

        if (!$orderData) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng!']);
            return;
        }

        echo json_encode([
            'success' => true,
            'id' => (int)$orderData['id'],
            'image' => $orderData['image'] ?? null,
        ]);
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

    /**
     * API thêm đơn midas-buy-japan (POST JSON hoặc form).
     * Body: { "order_id", "uid", "card", "image" (tùy chọn), "status", "sales_agent_id" (tùy chọn) }
     */
    public function apiStore()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method Not Allowed. Use POST.']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) {
            $input = $_POST;
        }

        $uid = $input['uid'] ?? '';
        $card = $input['card'] ?? '';
        $image = !empty(trim($input['image'] ?? '')) ? trim($input['image']) : null;
        $order_id = !empty(trim($input['order_id'] ?? '')) ? trim($input['order_id']) : null;
        $order_id = ($order_id !== null && is_numeric($order_id)) ? (int)$order_id : null;
        $sales_agent_id = isset($input['sales_agent_id']) && $input['sales_agent_id'] !== '' && is_numeric($input['sales_agent_id'])
            ? (int)$input['sales_agent_id']
            : null;

        if (empty(trim($uid)) || !is_numeric($uid)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'UID phải là số và không được để trống!']);
            return;
        }

        if (empty(trim($card))) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Card không được để trống!']);
            return;
        }

        if (strlen($card) > 30) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Card tối đa 30 ký tự!']);
            return;
        }

        $order = new MidasBuyJapanOrder();
        if ($order_id !== null && $order->checkOrderIdExists($order_id)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Mã đơn hàng (Order ID) "' . $order_id . '" đã tồn tại trong hệ thống!']);
            return;
        }

        $status = $input['status'] ?? 'pending';
        $validStatuses = ['pending', 'success', 'cancelled', 'refunded'];
        if (!in_array($status, $validStatuses)) {
            $status = 'pending';
        }

        $id = $order->insert($uid, $card, $image, $status, $order_id, $sales_agent_id);
        echo json_encode([
            'success' => true,
            'message' => 'Đã thêm đơn hàng thành công!',
            'id' => (int)$id,
        ]);
    }

    public function store()
    {
        $order = new MidasBuyJapanOrder();
        $uid = $_POST['uid'] ?? '';
        $card = $_POST['card'] ?? '';
        $image = !empty(trim($_POST['image'] ?? '')) ? trim($_POST['image']) : null;
        $order_id = !empty(trim($_POST['order_id'] ?? '')) ? trim($_POST['order_id']) : null;
        $order_id = ($order_id !== null && is_numeric($order_id)) ? (int)$order_id : null;
        $sales_agent_id = isset($_POST['sales_agent_id']) && $_POST['sales_agent_id'] !== '' && is_numeric($_POST['sales_agent_id'])
            ? (int)$_POST['sales_agent_id']
            : null;

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
        $order->insert($uid, $card, $image, $status, $order_id, $sales_agent_id);
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
        $order_id = !empty(trim($_POST['order_id'] ?? '')) ? trim($_POST['order_id']) : null;
        $order_id = ($order_id !== null && is_numeric($order_id)) ? (int)$order_id : null;
        $sales_agent_id = isset($_POST['sales_agent_id']) && $_POST['sales_agent_id'] !== '' && is_numeric($_POST['sales_agent_id'])
            ? (int)$_POST['sales_agent_id']
            : null;

        if (empty($id)) {
            header("Location: ?act=midas-japan-orders&error=" . urlencode('ID đơn hàng không hợp lệ!'));
            exit;
        }

        // Lấy thông tin đơn hàng hiện tại
        $existingOrder = $order->getOrderById($id);
        if (!$existingOrder) {
            header("Location: ?act=midas-japan-orders&error=" . urlencode('Không tìm thấy đơn hàng!'));
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

        // Xử lý upload image
        $image = $existingOrder['image'] ?? null; // Giữ nguyên giá trị cũ mặc định

        // Nếu có file được upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];

            // Validate file type (chỉ cho phép ảnh)
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = $file['type'];

            if (!in_array($fileType, $allowedTypes)) {
                header("Location: ?act=midas-japan-order-edit&id={$id}&error=" . urlencode('Chỉ cho phép upload file ảnh (JPEG, PNG, GIF, WebP)!'));
                exit;
            }

            // Validate file size (tối đa 5MB)
            $maxSize = 5 * 1024 * 1024; // 5MB
            if ($file['size'] > $maxSize) {
                header("Location: ?act=midas-japan-order-edit&id={$id}&error=" . urlencode('File ảnh quá lớn! Tối đa 5MB.'));
                exit;
            }

            // Tạo thư mục uploads nếu chưa có
            $uploadDir = 'uploads/midas-japan-orders/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Tạo tên file unique
            $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = 'midas_japan_' . $id . '_' . time() . '_' . uniqid() . '.' . $fileExtension;
            $filePath = $uploadDir . $fileName;

            // Upload file
            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                // Xóa file cũ nếu có
                if (!empty($existingOrder['image']) && file_exists($existingOrder['image'])) {
                    @unlink($existingOrder['image']);
                }
                $image = $filePath;
            } else {
                header("Location: ?act=midas-japan-order-edit&id={$id}&error=" . urlencode('Không thể upload file!'));
                exit;
            }
        }

        $status = $_POST['status'] ?? 'pending';
        $validStatuses = ['pending', 'success', 'cancelled', 'refunded'];
        if (!in_array($status, $validStatuses)) $status = 'pending';
        $order->update($id, $uid, $card, $image, $status, $order_id, $sales_agent_id);
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
