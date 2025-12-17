<?php
require_once "models/Order.php";

class OrderController extends Order
{
    public function index()
    {
        $order = new Order();

        // Phân trang
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 30;
        $totalCount = $order->getTotalCount();
        $totalPages = ceil($totalCount / $perPage);

        // Đảm bảo page hợp lệ
        if ($page < 1) $page = 1;
        if ($page > $totalPages && $totalPages > 0) $page = $totalPages;

        $orders = $order->getOrdersPaginated($page, $perPage);
        $totalMoney = $order->getTotalMoney();

        // Truyền biến phân trang vào view
        $currentPage = $page;

        require_once "views/orders/index.php";
    }

    public function add()
    {
        require_once "views/orders/add.php";
    }

    public function store()
    {
        $order = new Order();

        // Nếu có order_data từ textarea, parse nó
        if (isset($_POST['order_data']) && !empty(trim($_POST['order_data']))) {
            $parsed = $this->parseOrderData($_POST['order_data']);
            // Ưu tiên giá trị từ parsed, nếu không có thì lấy từ input
            $order_id = !empty($parsed['order_id']) ? $parsed['order_id'] : ($_POST['order_id'] ?? '');
            $username = !empty($parsed['username']) ? $parsed['username'] : ($_POST['username'] ?? '');
            $password = !empty($parsed['password']) ? $parsed['password'] : ($_POST['password'] ?? '');
        } else {
            // Nếu không có order_data, lấy từ input fields
            $order_id = $_POST['order_id'] ?? '';
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
        }

        $backup_code = $_POST['backup_code'] ?? '';

        // Validate order_id không được để trống
        if (empty(trim($order_id))) {
            header("Location: ?act=order-add&error=" . urlencode('Mã đơn hàng không được để trống!'));
            exit;
        }

        // Kiểm tra đơn hàng đã tồn tại chưa
        if ($order->checkOrderExists(trim($order_id))) {
            header("Location: ?act=order-add&error=" . urlencode('Mã đơn hàng đã tồn tại'));
            exit;
        }

        // Xử lý backup_code: cho phép từ 1-3 mã, mỗi mã 7 ký tự
        $backup_code = trim($backup_code);
        if (!empty($backup_code)) {
            // Loại bỏ khoảng trắng
            $backup_code_clean = preg_replace('/\s+/', '', $backup_code);

            // Validate: phải là bội số của 7 và từ 7 đến 21 ký tự (1-3 mã)
            if (strlen($backup_code_clean) % 7 !== 0 || strlen($backup_code_clean) < 7 || strlen($backup_code_clean) > 21) {
                header("Location: ?act=order-add&error=" . urlencode('Mã backup steam phải có từ 1 đến 3 mã, mỗi mã đúng 7 ký tự!'));
                exit;
            }

            // Lấy mã đầu tiên (7 ký tự đầu) để lưu vào database
            $backup_code = substr($backup_code_clean, 0, 7);
        } else {
            $backup_code = null;
        }

        $order->insert($order_id, $username, $password, $backup_code);
        header("Location: ?act=orders");
    }

    private function parseOrderData($text)
    {
        $result = [
            'order_id' => '',
            'username' => '',
            'password' => ''
        ];

        // Parse Mã đơn hàng: "Mã đơn hàng: 798163"
        if (preg_match('/Mã đơn hàng:\s*(\d+)/i', $text, $matches)) {
            $result['order_id'] = trim($matches[1]);
        }

        // Parse Username: "Tài khoản Steam cần chuyển: ThoaiThanh2112"
        if (preg_match('/Tài khoản Steam:\s*(.+?)(?:\n|$)/i', $text, $matches)) {
            $result['username'] = trim($matches[1]);
        }

        // Parse Password: "Mật Khẩu Steam cần chuyển: thanhtang99+"
        if (preg_match('/Mật Khẩu Steam:\s*(.+?)(?:\n|$)/i', $text, $matches)) {
            $result['password'] = trim($matches[1]);
        }

        return $result;
    }

    public function edit($id)
    {
        $order = new Order();
        $orderData = $order->getOrderById($id);

        if (!$orderData) {
            header("Location: ?act=orders&error=" . urlencode('Không tìm thấy đơn hàng!'));
            exit;
        }

        $order = $orderData;
        require_once "views/orders/edit.php";
    }

    public function update()
    {
        $order = new Order();
        $id = $_POST['id'] ?? '';
        $order_id = $_POST['order_id'] ?? '';
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $backup_code = $_POST['backup_code'] ?? '';

        // Validate id không được để trống
        if (empty($id)) {
            header("Location: ?act=orders&error=" . urlencode('ID đơn hàng không hợp lệ!'));
            exit;
        }

        // Validate order_id không được để trống
        if (empty(trim($order_id))) {
            header("Location: ?act=order-edit&id={$id}&error=" . urlencode('Mã đơn hàng không được để trống!'));
            exit;
        }

        // Kiểm tra order_id có bị trùng với đơn hàng khác không
        $existingOrder = $order->getOrderById($id);
        if (!$existingOrder) {
            header("Location: ?act=orders&error=" . urlencode('Không tìm thấy đơn hàng!'));
            exit;
        }

        // Loại bỏ khoảng trắng từ backup_code
        $backup_code = preg_replace('/\s+/', '', $backup_code);

        // Validate backup_code: nếu có giá trị thì phải có đúng 7 ký tự, nếu không có thì cho phép null
        if (!empty($backup_code) && strlen($backup_code) !== 7) {
            header("Location: ?act=order-edit&id={$id}&error=" . urlencode('Mã backup steam phải có đúng 7 ký tự hoặc để trống!'));
            exit;
        }

        // Nếu rỗng thì set thành null
        if (empty($backup_code)) {
            $backup_code = null;
        }

        // Xử lý upload image_error
        $image_error = $existingOrder['image_error'] ?? null; // Giữ nguyên giá trị cũ mặc định

        // Nếu có file được upload
        if (isset($_FILES['image_error']) && $_FILES['image_error']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image_error'];

            // Validate file type (chỉ cho phép ảnh)
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = $file['type'];

            if (!in_array($fileType, $allowedTypes)) {
                header("Location: ?act=order-edit&id={$id}&error=" . urlencode('Chỉ cho phép upload file ảnh (JPEG, PNG, GIF, WebP)!'));
                exit;
            }

            // Validate file size (tối đa 5MB)
            $maxSize = 5 * 1024 * 1024; // 5MB
            if ($file['size'] > $maxSize) {
                header("Location: ?act=order-edit&id={$id}&error=" . urlencode('File ảnh quá lớn! Tối đa 5MB.'));
                exit;
            }

            // Tạo thư mục uploads nếu chưa có
            $uploadDir = 'uploads/error_images/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Tạo tên file unique
            $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = 'error_' . $id . '_' . time() . '_' . uniqid() . '.' . $fileExtension;
            $filePath = $uploadDir . $fileName;

            // Upload file
            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                // Xóa file cũ nếu có
                if (!empty($existingOrder['image_error']) && file_exists($existingOrder['image_error'])) {
                    @unlink($existingOrder['image_error']);
                }
                $image_error = $filePath;
            } else {
                header("Location: ?act=order-edit&id={$id}&error=" . urlencode('Không thể upload file!'));
                exit;
            }
        }

        // Lấy status từ form
        $status = $_POST['status'] ?? '';

        // Nếu status rỗng thì không cập nhật status
        if (empty($status)) {
            $order->updateOrder($id, $order_id, $username, $password, $backup_code, null, $image_error);
        } else {
            // Validate status phải là một trong các giá trị hợp lệ
            $validStatuses = ['pending', 'processing', 'completed', 'cancelled'];
            if (!in_array($status, $validStatuses)) {
                $status = 'pending';
            }
            $order->updateOrder($id, $order_id, $username, $password, $backup_code, $status, $image_error);
        }

        header("Location: ?act=orders");
    }

    public function delete($id)
    {
        $order = new Order();
        $order->delete($id);
        header("Location: ?act=orders");
    }

    public function deleteAllExceptPending()
    {
        $order = new Order();
        $order->deleteAllExceptPending();

        // Xóa tất cả ảnh trong thư mục uploads/error_images/
        $uploadDir = 'uploads/error_images/';
        if (file_exists($uploadDir) && is_dir($uploadDir)) {
            $files = glob($uploadDir . '*'); // Lấy tất cả file trong thư mục
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file); // Xóa file
                }
            }
        }

        header("Location: ?act=orders");
    }

    public function editMoney()
    {
        $order = new Order();
        $money = $order->getTotalMoney();
        require_once "views/orders/edit-money.php";
    }

    public function updateMoneyData()
    {
        $order = new Order();
        $id = $_POST['id'] ?? 1;
        $balance = $_POST['balance'] ?? 0;
        $code = $_POST['code'] ?? '';

        // Validate balance phải là số
        if (!is_numeric($balance)) {
            header("Location: ?act=money-edit&error=" . urlencode('Số tiền phải là số hợp lệ!'));
            exit;
        }

        $order->updateMoney($id, $balance, $code);
        header("Location: ?act=orders");
    }

    public function updateMoney2($id, $balance)
    {
        $order = new Order();
        $order->updateMoney2($id, $balance);
    }

    public function addPaymentCodes()
    {
        require_once "views/orders/add-payment-codes.php";
    }

    public function storePaymentCodes()
    {
        $order = new Order();
        $codesText = $_POST['codes'] ?? '';

        if (empty(trim($codesText))) {
            header("Location: ?act=payment-codes-add&error=" . urlencode('Vui lòng nhập ít nhất một code!'));
            exit;
        }

        // Tách các code theo dòng mới
        $codes = preg_split('/\r\n|\r|\n/', $codesText);
        $codes = array_filter(array_map('trim', $codes)); // Loại bỏ khoảng trắng và dòng trống

        if (empty($codes)) {
            header("Location: ?act=payment-codes-add&error=" . urlencode('Không tìm thấy code hợp lệ!'));
            exit;
        }

        $result = $order->insertPaymentCodes($codes);

        $message = "Đã thêm {$result['inserted']} code thành công";
        if ($result['skipped'] > 0) {
            $message .= ", bỏ qua {$result['skipped']} code đã tồn tại";
        }

        header("Location: ?act=payment-codes-add&success=" . urlencode($message));
    }

    public function listPaymentCodes()
    {
        $order = new Order();
        $codes = $order->getAllPaymentCodes();
        require_once "views/orders/list-payment-codes.php";
    }

    public function deleteAllPaymentCodes()
    {
        $order = new Order();
        $order->deleteAllPaymentCodes();
        header("Location: ?act=payment-codes-list");
    }

    public function deletePaymentCode($id)
    {
        $order = new Order();
        $order->deletePaymentCode($id);
        header("Location: ?act=payment-codes-list");
    }
}
