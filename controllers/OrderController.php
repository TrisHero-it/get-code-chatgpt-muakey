<?php
require_once "models/Order.php";

class OrderController extends Order
{
    public function index()
    {
        $order = new Order();
        $orders = $order->index();
        $totalMoney = $order->getTotalMoney();

        require_once "views/orders/index.php";
    }

    public function add()
    {
        require_once "views/orders/add.php";
    }

    public function store()
    {
        $order = new Order();
        $order_id = $_POST['order_id'] ?? '';
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
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

        // Loại bỏ khoảng trắng từ backup_code
        $backup_code = preg_replace('/\s+/', '', $backup_code);

        // Validate backup_code: nếu có giá trị thì phải có đúng 7 ký tự, nếu không có thì cho phép null
        if (!empty($backup_code) && strlen($backup_code) !== 7) {
            header("Location: ?act=order-add&error=" . urlencode('Mã backup steam phải có đúng 7 ký tự hoặc để trống!'));
            exit;
        }

        // Nếu rỗng thì set thành null
        if (empty($backup_code)) {
            $backup_code = null;
        }


        $order->insert($order_id, $username, $password, $backup_code);
        header("Location: ?act=orders");
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

        // Nếu order_id thay đổi, kiểm tra xem có trùng không
        if ($existingOrder['order_id'] !== trim($order_id) && $order->checkOrderExists(trim($order_id))) {
            header("Location: ?act=order-edit&id={$id}&error=" . urlencode('Mã đơn hàng đã tồn tại!'));
            exit;
        }

        $order->updateOrder($id, $order_id, $username, $password, $backup_code);
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
