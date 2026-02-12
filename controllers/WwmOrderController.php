<?php
require_once "models/WwmOrder.php";

class WwmOrderController extends WwmOrder
{
    public function getOrders()
    {
        header('Content-Type: application/json; charset=utf-8');
        $wwmOrder = new WwmOrder();
        $wwmOrders = $wwmOrder->getOrders();
        echo json_encode($wwmOrders);
    }

    public function checkStatusOrders($orderId)
    {
        header('Content-Type: application/json; charset=utf-8');
        $wwmOrder = new WwmOrder();
        $wwmOrders = $wwmOrder->getAllOrders($orderId);
        echo json_encode($wwmOrders);
    }

    public function add()
    {
        $products = $this->getProducts();
        $iosProducts = array_values(array_filter($products, function ($item) {
            return isset($item['platform']) && $item['platform'] === 'ios';
        }));
        $productsOneHuman = $this->productsOneHuman();
        require_once "views/orders/add-wwm-order.php";
    }

    public function store()
    {
        // Nếu có order_data từ textarea, parse nó
        if (isset($_POST['order_data']) && !empty(trim($_POST['order_data']))) {
            $parsed = $this->parseOrderData($_POST['order_data']);
            $order_id = $parsed['order_id'] ?? '';
            $uid = $parsed['uid'] ?? '';
            $product_id = $parsed['product_id'] ?? '';
            $category = $parsed['category'] ?? '';
            $server = $parsed['server'] ?? '';
            $region = $parsed['region'] ?? '';
        } else {
            // Nếu không có order_data, lấy từ hidden fields (fallback)
            $order_id = $_POST['order_id'] ?? '';
            $uid = $_POST['uid'] ?? '';
            $product_id = $_POST['product_id'] ?? '';
            $category = $_POST['category'] ?? '';
            $server = $_POST['server'] ?? '';
            $region = $_POST['region'] ?? '';
        }

        $order_id = trim($order_id);
        $uid = trim($uid);
        $product_id = trim($product_id);
        $category = trim($category);
        $server = trim($server);
        $region = trim($region);

        if ($order_id === '' || $uid === '' || $product_id === '') {
            header("Location: ?act=wwm-order-add&error=" . urlencode('Vui lòng dán đầy đủ thông tin đơn hàng để hệ thống có thể nhận diện Order ID, UID và Product ID!'));
            exit;
        }

        // Validate UID phải có đúng 10 chữ số
        if (!preg_match('/^\d{10}$/', $uid)) {
            header("Location: ?act=wwm-order-add&error=" . urlencode('UID phải có đúng 10 chữ số! Ví dụ: 4059837621'));
            exit;
        }

        // Kiểm tra order_id đã tồn tại chưa
        $wwmOrder = new WwmOrder();
        if ($wwmOrder->checkOrderIdExists($order_id)) {
            header("Location: ?act=wwm-order-add&error=" . urlencode('Order ID "' . $order_id . '" đã tồn tại trong hệ thống!'));
            exit;
        }

        $this->insert($order_id, $uid, $product_id, $category, $server, $region);

        // Kiểm tra xem có muốn tiếp tục thêm không
        $continue_add = isset($_POST['continue_add']) && $_POST['continue_add'] == '1';

        if ($continue_add) {
            header("Location: ?act=wwm-order-add&success=" . urlencode('Thêm wwm_order thành công! Bạn có thể tiếp tục thêm đơn hàng mới.'));
        } else {
            header("Location: ?act=wwm-orders&success=" . urlencode('Thêm wwm_order thành công!'));
        }
        exit;
    }

    /**
     * Resolve product_name -> product_id (và category) theo danh sách getProducts() + productsOneHuman().
     * Trả về ['product_id' => ..., 'category' => ...] hoặc null nếu không khớp.
     */
    private function resolveProductNameToId($productName)
    {
        if ($productName === null || trim((string)$productName) === '') return null;
        $name = trim((string)$productName);
        $normalize = function ($n) {
            $n = preg_replace('/\s+x\s+\d+$/i', '', $n);
            $n = preg_replace('/\s+(ID|Bản Mobile)\s*$/i', '', $n);
            $n = preg_replace('/\s+(ID|Bản Mobile)\s*$/i', '', $n);
            $n = preg_replace('/\s+Chỉ Cần ID\s*$/i', '', $n);
            return trim($n);
        };
        $iosProducts = array_values(array_filter($this->getProducts(), function ($item) {
            return isset($item['platform']) && $item['platform'] === 'ios';
        }));
        $productsOneHuman = $this->productsOneHuman();
        $wwmProductIds = array_map(function ($p) {
            return $p['goodsid'];
        }, $iosProducts);
        $oneHumanProductIds = array_map(function ($p) {
            return $p['goodsid'];
        }, $productsOneHuman);
        $allProducts = array_merge($iosProducts, $productsOneHuman);
        $cleanInput = $normalize($name);
        $cleanLower = mb_strtolower($cleanInput, 'UTF-8');
        foreach ($allProducts as $product) {
            if (!isset($product['goodsinfo'])) continue;
            $normalized = $normalize($product['goodsinfo']);
            $normalizedLower = mb_strtolower($normalized, 'UTF-8');
            if (
                $normalizedLower === $cleanLower
                || mb_strpos($normalizedLower, $cleanLower, 0, 'UTF-8') !== false
                || mb_strpos($cleanLower, $normalizedLower, 0, 'UTF-8') !== false
            ) {
                $category = '';
                if (in_array($product['goodsid'], $wwmProductIds)) $category = 'where winds meet';
                elseif (in_array($product['goodsid'], $oneHumanProductIds)) $category = 'one human';
                return ['product_id' => $product['goodsid'], 'category' => $category];
            }
        }
        return null;
    }

    /**
     * API: Thêm WWM order(s) qua JSON (POST).
     * Chấp nhận product_id hoặc product_name (sẽ map theo danh sách sản phẩm).
     * Một đơn: { "order_id": "...", "uid": "...", "product_id": "..." } hoặc "product_name": "Monthly Pass Where Winds Meet Bản Mobile x 1"
     * Nhiều đơn: { "orders": [ {...}, ... ] }
     */
    public function apiStore()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method Not Allowed. Use POST.']);
            return;
        }

        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid JSON. Use { "order_id", "uid", "product_id" } or { "product_name": "..." } or { "orders": [ {...} ] }'
            ]);
            return;
        }

        $list = isset($data['orders']) && is_array($data['orders']) ? $data['orders'] : [$data];
        $wwmOrder = new WwmOrder();
        $iosProducts = array_values(array_filter($this->getProducts(), function ($item) {
            return isset($item['platform']) && $item['platform'] === 'ios';
        }));
        $productsOneHuman = $this->productsOneHuman();
        $wwmProductIds = array_map(function ($p) {
            return $p['goodsid'];
        }, $iosProducts);
        $oneHumanProductIds = array_map(function ($p) {
            return $p['goodsid'];
        }, $productsOneHuman);

        $inserted = 0;
        $errors = [];

        foreach ($list as $index => $item) {
            $order_id = isset($item['order_id']) ? trim((string)$item['order_id']) : '';
            $uid = isset($item['uid']) ? trim((string)$item['uid']) : '';
            $product_id = isset($item['product_id']) ? trim((string)$item['product_id']) : '';
            $product_name = isset($item['product_name']) ? trim((string)$item['product_name']) : '';
            $category = isset($item['category']) ? trim((string)$item['category']) : '';
            $server = isset($item['server']) ? trim((string)$item['server']) : '';
            $region = isset($item['region']) ? trim((string)$item['region']) : '';

            if ($order_id === '') {
                $errors[] = ['index' => $index, 'order_id' => $order_id, 'message' => 'order_id is required'];
                continue;
            }
            if ($uid === '') {
                $errors[] = ['index' => $index, 'order_id' => $order_id, 'message' => 'uid is required'];
                continue;
            }
            if (!preg_match('/^\d{10}$/', $uid)) {
                $errors[] = ['index' => $index, 'order_id' => $order_id, 'message' => 'uid must be exactly 10 digits'];
                continue;
            }
            if ($product_id === '' && $product_name !== '') {
                $resolved = $this->resolveProductNameToId($product_name);
                if ($resolved) {
                    $product_id = $resolved['product_id'];
                    if ($category === '' && $resolved['category'] !== '') {
                        $category = $resolved['category'];
                    }
                } else {
                    $errors[] = ['index' => $index, 'order_id' => $order_id, 'message' => 'product_name not found: ' . $product_name];
                    continue;
                }
            }
            if ($product_id === '') {
                $errors[] = ['index' => $index, 'order_id' => $order_id, 'message' => 'product_id or product_name is required'];
                continue;
            }
            if ($wwmOrder->checkOrderIdExists($order_id)) {
                $errors[] = ['index' => $index, 'order_id' => $order_id, 'message' => 'order_id already exists'];
                continue;
            }

            if ($category === '') {
                if (in_array($product_id, $wwmProductIds)) {
                    $category = 'where winds meet';
                } elseif (in_array($product_id, $oneHumanProductIds)) {
                    $category = 'one human';
                }
            }

            try {
                $wwmOrder->insert($order_id, $uid, $product_id, $category, $server, $region);
                $inserted++;
            } catch (\Throwable $e) {
                $errors[] = ['index' => $index, 'order_id' => $order_id, 'message' => $e->getMessage()];
            }
        }

        echo json_encode([
            'success' => true,
            'inserted' => $inserted,
            'errors' => $errors
        ]);
    }

    private function parseOrderData($text)
    {
        $result = [
            'order_id' => '',
            'uid' => '',
            'product_id' => '',
            'category' => '',
            'server' => '',
            'region' => '',
            'purchase_date' => ''
        ];

        // Parse Order ID: "Mã đơn hàng: 836396" hoặc "Mã ĐH: 797741"
        if (preg_match('/Mã\s+đơn\s+hàng:\s*(\d+)/i', $text, $matches)) {
            $result['order_id'] = trim($matches[1]);
        } elseif (preg_match('/Mã\s+ĐH:\s*(\d+)/i', $text, $matches)) {
            $result['order_id'] = trim($matches[1]);
        }

        // Parse UID: "Character ID Where Winds Meet: 4039549251" hoặc "UID: User ID: 4033075547" hoặc "UID: 4033075547" hoặc "UID Once Human: 154191896"
        if (preg_match('/Character ID Where Winds Meet:\s*(\d+)/i', $text, $matches)) {
            $result['uid'] = trim($matches[1]);
        } elseif (preg_match('/UID:.*?User ID:\s*(\d+)/i', $text, $matches)) {
            $result['uid'] = trim($matches[1]);
        } elseif (preg_match('/UID Once Human:\s*(\d+)/i', $text, $matches)) {
            $result['uid'] = trim($matches[1]);
        } elseif (preg_match('/UID:\s*(\d+)/i', $text, $matches)) {
            $result['uid'] = trim($matches[1]);
        }

        // Parse Server Once Human: "Sever Once Human: E_Dream-Y0002" (lưu ý: có thể là "Sever" hoặc "Server")
        if (preg_match('/(?:Sever|Server) Once Human:\s*(.+?)(?:\n|$)/i', $text, $matches)) {
            $result['server'] = trim($matches[1]);
        }

        // Parse Region Once Human: "Region Once Human: Southeast Asia Server"
        if (preg_match('/Region Once Human:\s*(.+?)(?:\n|$)/i', $text, $matches)) {
            $result['region'] = trim($matches[1]);
        }

        // Parse Purchase Date: "Ngày mua: 15:26:40 03/01/2026"
        if (preg_match('/Ngày mua:\s*(.+?)(?:\n|$)/i', $text, $matches)) {
            $result['purchase_date'] = trim($matches[1]);
        }

        // Parse Product Name và tìm product_id: "Tên sản phẩm: ..." hoặc "Sản phẩm: ..."
        $productName = null;
        if (preg_match('/Tên\s+sản\s+phẩm:\s*(.+?)(?:\n|$)/i', $text, $matches)) {
            $productName = trim($matches[1]);
        } elseif (preg_match('/Sản\s+phẩm:\s*(.+?)(?:\n|$)/i', $text, $matches)) {
            $productName = trim($matches[1]);
        }

        if ($productName) {

            // Hàm normalize sản phẩm: loại bỏ các phần thừa
            $normalizeProductName = function ($name) {
                // Loại bỏ " x 1", " x 2" ở cuối
                $name = preg_replace('/\s+x\s+\d+$/i', '', $name);
                // Loại bỏ " ID", " Bản Mobile" ở cuối (có thể có cả hai)
                $name = preg_replace('/\s+(ID|Bản Mobile)\s*$/i', '', $name);
                $name = preg_replace('/\s+(ID|Bản Mobile)\s*$/i', '', $name); // Loại bỏ lần nữa nếu còn
                // Loại bỏ "Chỉ Cần ID" ở cuối (cho One Human)
                $name = preg_replace('/\s+Chỉ Cần ID\s*$/i', '', $name);
                return trim($name);
            };

            $cleanProductName = $normalizeProductName($productName);

            // Lấy danh sách products
            $products = $this->getProducts();
            $iosProducts = array_values(array_filter($products, function ($item) {
                return isset($item['platform']) && $item['platform'] === 'ios';
            }));
            $productsOneHuman = $this->productsOneHuman();

            // Danh sách product IDs thuộc "where winds meet"
            $wwmProductIds = array_map(function ($product) {
                return $product['goodsid'];
            }, $iosProducts);

            // Danh sách product IDs thuộc "one human"
            $oneHumanProductIds = array_map(function ($product) {
                return $product['goodsid'];
            }, $productsOneHuman);

            // Gộp tất cả products để tìm kiếm
            $allProducts = array_merge($iosProducts, $productsOneHuman);

            // Tìm product_id từ danh sách products
            foreach ($allProducts as $product) {
                if (!isset($product['goodsinfo'])) continue;

                // Normalize tên sản phẩm trong danh sách
                $normalizedProductName = $normalizeProductName($product['goodsinfo']);

                // So sánh tên đã normalize (case-insensitive)
                $normalizedLower = mb_strtolower($normalizedProductName, 'UTF-8');
                $cleanLower = mb_strtolower($cleanProductName, 'UTF-8');

                if (
                    $normalizedLower === $cleanLower ||
                    mb_strpos($normalizedLower, $cleanLower, 0, 'UTF-8') !== false ||
                    mb_strpos($cleanLower, $normalizedLower, 0, 'UTF-8') !== false
                ) {
                    $result['product_id'] = $product['goodsid'];
                    // Kiểm tra category dựa trên product_id
                    if (in_array($product['goodsid'], $wwmProductIds)) {
                        $result['category'] = 'where winds meet';
                    } elseif (in_array($product['goodsid'], $oneHumanProductIds)) {
                        $result['category'] = 'one human';
                    }
                    break;
                }
            }
        }

        return $result;
    }

    public function index($category = null)
    {
        $products = $this->getProducts();
        $iosProducts = array_values(array_filter($products, function ($item) {
            return isset($item['platform']) && $item['platform'] === 'ios';
        }));
        $productsOneHuman = $this->productsOneHuman();
        $wwmOrder = new WwmOrder();

        // Lấy category filter từ GET nếu không có tham số
        if ($category === null) {
            $category = isset($_GET['category']) && $_GET['category'] !== '' ? $_GET['category'] : null;
        }

        // Lấy search order_id từ GET
        $searchOrderId = isset($_GET['search_order_id']) && $_GET['search_order_id'] !== '' ? trim($_GET['search_order_id']) : null;

        // Phân trang
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 30;
        $totalCount = $wwmOrder->getTotalCount($category, $searchOrderId);
        $totalPages = ceil($totalCount / $perPage);

        // Đảm bảo page hợp lệ
        if ($page < 1) $page = 1;
        if ($page > $totalPages && $totalPages > 0) $page = $totalPages;

        $wwmOrders = $wwmOrder->getOrdersPaginated($page, $perPage, $category, $searchOrderId);

        // Lấy danh sách categories để hiển thị trong dropdown
        $categories = $wwmOrder->getCategories();

        // Truyền biến phân trang vào view
        $currentPage = $page;

        require_once "views/orders/index-wwm-order.php";
    }

    public function delete($id)
    {
        $wwmOrder = new WwmOrder();
        $wwmOrder->delete($id);
        header("Location: ?act=wwm-orders");
    }

    public function deleteAllExceptPending()
    {
        $wwmOrder = new WwmOrder();
        $wwmOrder->deleteAllExceptPending();
        header("Location: ?act=wwm-orders&success=" . urlencode('Đã xóa tất cả WWM orders ngoại trừ các orders đang chờ (pending)!'));
    }

    public function getProducts()
    {
        // Mảng sản phẩm tĩnh - bạn có thể chỉnh sửa mảng này theo nhu cầu
        return [
            [
                'goodsid' => 'yysls.60cmz.oversea',
                'goodsinfo' => '60 Echo Beads Where Winds Meet ID x 1',
                'platform' => 'ios'
            ],
            [
                'goodsid' => 'yysls.180cmz.oversea',
                'goodsinfo' => '180 Echo Beads Where Winds Meet Bản Mobile x 1',
                'platform' => 'ios'
            ],
            [
                'goodsid' => 'yysls.monthlycard.oversea',
                'goodsinfo' => 'Monthly Pass Where Winds Meet Bản Mobile x 1',
                'platform' => 'ios'
            ],
            [
                'goodsid' => 'yysls.300cmz.oversea',
                'goodsinfo' => '300 Echo Beads Where Winds Meet Bản Mobile x 1',
                'platform' => 'ios'
            ],
            [
                'goodsid' => 'yysls.600cmz.oversea',
                'goodsinfo' => '600 Echo Beads Where Winds Meet Bản Mobile x 1',
                'platform' => 'ios'
            ],
            [
                'goodsid' => 'yysls.battlepass.oversea',
                'goodsinfo' => 'Elite Battle Pass Where Winds Meet Bản Mobile x 1',
                'platform' => 'ios'
            ],
            [
                'goodsid' => 'yysls.900cmz.oversea',
                'goodsinfo' => '900 Echo Beads Where Winds Meet Bản Mobile x 1',
                'platform' => 'ios'
            ],
            [
                'goodsid' => 'yysls.battlepasspro.oversea',
                'goodsinfo' => 'Premium Battle Pass Where Winds Meet Bản Mobile x 1',
                'platform' => 'ios'
            ],
            [
                'goodsid' => 'yysls.1800cmz.oversea',
                'goodsinfo' => '1800 Echo Beads Where Winds Meet Bản Mobile x 1',
                'platform' => 'ios'
            ],
            [
                'goodsid' => 'yysls.3000cmz.oversea',
                'goodsinfo' => '3000 Echo Beads Where Winds Meet Bản Mobile x 1',
                'platform' => 'ios'
            ],
            [
                'goodsid' => 'yysls.6000cmz.oversea',
                'goodsinfo' => '6000 Echo Beads Where Winds Meet Bản Mobile x 1',
                'platform' => 'ios'
            ],
        ];
    }

    public function productsOneHuman()
    {
        return [
            [
                'goodsid' => 'h73web.yj6',
                'goodsinfo' => '62 Crystgin Once Human Chỉ Cần ID',
                'platform' => 'ios'
            ],
            [
                'goodsid' => 'h73web.yj30',
                'goodsinfo' => '339 Crystgin Once Human Chỉ Cần ID',
                'platform' => 'ios'
            ],
            [
                'goodsid' => 'h73web.yj98',
                'goodsinfo' => '1120 Crystgin Once Human Chỉ Cần ID',
                'platform' => 'ios'
            ],
            [
                'goodsid' => 'h73web.yj198',
                'goodsinfo' => '2340 Crystgin Once Human Chỉ Cần ID',
                'platform' => 'ios'
            ],
            [
                'goodsid' => 'h73web.yj328',
                'goodsinfo' => '3979 Crystgin Once Human Chỉ Cần ID',
                'platform' => 'ios'
            ],
            [
                'goodsid' => 'h73web.yj648',
                'goodsinfo' => '8075 Crystgin Once Human Chỉ Cần ID',
                'platform' => 'ios'
            ],
            [
                'goodsid' => 'h73web.qyk',
                'goodsinfo' => 'Meta Pass (30Days) Once Human Chỉ Cần ID',
                'platform' => 'ios'
            ],
            [
                'goodsid' => 'h73web.bp68',
                'goodsinfo' => 'Battle Pass (Advanced) Once Human Chỉ Cần ID',
                'platform' => 'ios'
            ],
            [
                'goodsid' => 'h73web.bp168',
                'goodsinfo' => 'Battle Pass (Deluxe) Once Human Chỉ Cần ID',
                'platform' => 'ios'
            ],
            [
                'goodsid' => 'h73web.newbiepack1',
                'goodsinfo' => 'Shining Star Pack Once Human Chỉ Cần ID',
                'platform' => 'ios'
            ],
            [
                'goodsid' => 'ios_h73web.bundle1.x78',
                'goodsinfo' => 'Cosmetics DLC - Double Agent Theme Pack Once Human Chỉ Cần ID',
                'platform' => 'ios'
            ],
            [
                'goodsid' => 'ios_h73web.bundle2.x78',
                'goodsinfo' => 'Cosmetics DLC - Ascend Daily Theme Pack Once Human Chỉ Cần ID',
                'platform' => 'ios'
            ]
        ];
    }

    public function update($id, $status)
    {
        $wwmOrder = new WwmOrder();
        $wwmOrder->updateOrder($id, $status);
        header("Location: ?act=wwm-orders");
    }

    public function edit($id)
    {
        $wwmOrder = new WwmOrder();
        $orderData = $wwmOrder->getOrderById($id);

        if (!$orderData) {
            header("Location: ?act=wwm-orders&error=" . urlencode('Không tìm thấy đơn hàng!'));
            exit;
        }

        $products = $this->getProducts();
        $iosProducts = array_values(array_filter($products, function ($item) {
            return isset($item['platform']) && $item['platform'] === 'ios';
        }));

        $order = $orderData;
        require_once "views/orders/edit-wwm-order.php";
    }

    public function updateFull()
    {
        $wwmOrder = new WwmOrder();
        $id = $_POST['id'] ?? '';
        $order_id = $_POST['order_id'] ?? '';
        $uid = $_POST['uid'] ?? '';
        $product_id = $_POST['product_id'] ?? '';
        $category = $_POST['category'] ?? '';
        $server = $_POST['server'] ?? '';
        $region = $_POST['region'] ?? '';

        // Nếu không có category từ POST, tự động xác định category dựa trên product_id
        if (empty($category)) {
            $products = $this->getProducts();
            $iosProducts = array_values(array_filter($products, function ($item) {
                return isset($item['platform']) && $item['platform'] === 'ios';
            }));
            $productsOneHuman = $this->productsOneHuman();

            $wwmProductIds = array_map(function ($product) {
                return $product['goodsid'];
            }, $iosProducts);

            $oneHumanProductIds = array_map(function ($product) {
                return $product['goodsid'];
            }, $productsOneHuman);

            if (in_array($product_id, $wwmProductIds)) {
                $category = 'where winds meet';
            } elseif (in_array($product_id, $oneHumanProductIds)) {
                $category = 'one human';
            }
        }

        // Validate id không được để trống
        if (empty($id)) {
            header("Location: ?act=wwm-orders&error=" . urlencode('ID đơn hàng không hợp lệ!'));
            exit;
        }

        // Validate order_id không được để trống
        if (empty(trim($order_id))) {
            header("Location: ?act=wwm-order-edit&id={$id}&error=" . urlencode('Mã đơn hàng không được để trống!'));
            exit;
        }

        // Validate uid không được để trống
        if (empty(trim($uid))) {
            header("Location: ?act=wwm-order-edit&id={$id}&error=" . urlencode('UID không được để trống!'));
            exit;
        }

        // Validate product_id không được để trống
        if (empty(trim($product_id))) {
            header("Location: ?act=wwm-order-edit&id={$id}&error=" . urlencode('Product ID không được để trống!'));
            exit;
        }

        // Kiểm tra order_id có bị trùng với đơn hàng khác không
        $existingOrder = $wwmOrder->getOrderById($id);
        if (!$existingOrder) {
            header("Location: ?act=wwm-orders&error=" . urlencode('Không tìm thấy đơn hàng!'));
            exit;
        }

        // Kiểm tra order_id có trùng với đơn hàng khác không
        $checkOrder = $wwmOrder->checkOrderIdExists($order_id);
        if ($checkOrder && $existingOrder['order_id'] != $order_id) {
            header("Location: ?act=wwm-order-edit&id={$id}&error=" . urlencode('Mã đơn hàng đã tồn tại!'));
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
                header("Location: ?act=wwm-order-edit&id={$id}&error=" . urlencode('Chỉ cho phép upload file ảnh (JPEG, PNG, GIF, WebP)!'));
                exit;
            }

            // Validate file size (tối đa 5MB)
            $maxSize = 5 * 1024 * 1024; // 5MB
            if ($file['size'] > $maxSize) {
                header("Location: ?act=wwm-order-edit&id={$id}&error=" . urlencode('File ảnh quá lớn! Tối đa 5MB.'));
                exit;
            }

            // Tạo thư mục uploads nếu chưa có
            $uploadDir = 'uploads/error_images/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Tạo tên file unique
            $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = 'wwm_error_' . $id . '_' . time() . '_' . uniqid() . '.' . $fileExtension;
            $filePath = $uploadDir . $fileName;

            // Upload file
            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                // Xóa file cũ nếu có
                if (!empty($existingOrder['image']) && file_exists($existingOrder['image'])) {
                    @unlink($existingOrder['image']);
                }
                $image = $filePath;
            } else {
                header("Location: ?act=wwm-order-edit&id={$id}&error=" . urlencode('Không thể upload file!'));
                exit;
            }
        }

        // Lấy status từ form
        $status = $_POST['status'] ?? '';

        // Nếu status rỗng thì không cập nhật status
        if (empty($status)) {
            $wwmOrder->updateOrderFull($id, $order_id, $uid, $product_id, $category, $server, $region, null, $image);
        } else {
            // Validate status phải là một trong các giá trị hợp lệ
            $validStatuses = ['pending', 'processing', 'completed', 'cancelled'];
            if (!in_array($status, $validStatuses)) {
                $status = 'pending';
            }
            $wwmOrder->updateOrderFull($id, $order_id, $uid, $product_id, $category, $server, $region, $status, $image);
        }

        header("Location: ?act=wwm-orders");
    }
}
