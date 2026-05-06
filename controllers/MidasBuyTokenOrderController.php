<?php
require_once "models/MidasBuyToken.php";

class MidasBuyTokenOrderController extends MidasBuyToken
{
    public function index()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 30;

        $orders = $this->getOrdersPaginated($page, $perPage);
        $totalCount = $this->getTotalCount();
        $totalPages = ceil($totalCount / $perPage);

        require_once "views/midas-buy-token/orders/index.php";
    }

    public function add()
    {

        require_once "views/midas-buy-token/orders/add.php";
    }

    public function edit($id)
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $order = $this->getOrderById($id);
        if (!$order) {
            header("Location: ?act=midas-token-orders&error=Đơn hàng không tồn tại");
            exit;
        }
        require_once "views/midas-buy-token/orders/edit.php";
    }

    public function update($id, array $data)
    {

        $uid = $_POST['uid'] ?? '';
        $token = $_POST['product_id'] ?? '';
        $code = $_POST['code'] ?? '';
        $order_id = !empty(trim($_POST['order_id'] ?? '')) ? trim($_POST['order_id']) : null;
        $order_id = ($order_id !== null && is_numeric($order_id)) ? (int)$order_id : null;
        $sale_agent_id = isset($_POST['sale_agent_id']) && $_POST['sale_agent_id'] !== '' && is_numeric($_POST['sale_agent_id'])
            ? (int)$_POST['sale_agent_id']
            : null;

        $image = $_FILES['image']['name'] != '' ? $_FILES['image'] : null; // Giữ nguyên giá trị cũ mặc định
        // Nếu có file được upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) { 
            $file = $_FILES['image'];

            // Validate file type (chỉ cho phép ảnh)
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = $file['type'];

            if (!in_array($fileType, $allowedTypes)) {
                header("Location: ?act=medit-midas-token-order&id={$id}&error=" . urlencode('Chỉ cho phép upload file ảnh (JPEG, PNG, GIF, WebP)!'));
                exit;
            }

            // Validate file size (tối đa 5MB)
            $maxSize = 5 * 1024 * 1024; // 5MB
            if ($file['size'] > $maxSize) {
                header("Location: ?act=medit-midas-token-order&id={$id}&error=" . urlencode('File ảnh quá lớn! Tối đa 5MB.'));
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
                $image = $filePath;
            } else {
                header("Location: ?act=edit-midas-token-order&id={$id}&error=" . urlencode('Không thể upload file!'));
                exit;
            }
        }

        $data = [
            'order_id' => $order_id,
            'uid' => $uid,
            'code' => $code,
            'token' => $token,
            'image' => $image,
            'sales_agent_id' => $sale_agent_id,
            'status' => $_POST['status'] ?? 'pending'
        ];

        $this->updateOrderById($id, $data);
    }
}
