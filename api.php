<?php
require_once 'controllers/SteamController.php';
require_once 'controllers/OrderController.php';
require_once 'controllers/WwmOrderController.php';
require_once 'controllers/MidasBuyJapanOrderController.php';
require_once 'vendor/autoload.php';

$username = 'admin';
$password = 'Muakey@@111';

if (
    !isset($_SERVER['PHP_AUTH_USER']) ||
    !isset($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] !== $username ||
    $_SERVER['PHP_AUTH_PW'] !== $password
) {
    header('WWW-Authenticate: Basic realm="API"');
    header('HTTP/1.0 401 Unauthorized');
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$steamController = new SteamController();
$wwmOrderController = new WwmOrderController();

// REST: route từ /api/xxx (rewrite -> api.php?route=xxx)
$route = isset($_GET['route']) ? trim($_GET['route'], '/') : '';
$method = $_SERVER['REQUEST_METHOD'];

// Phân tách route có id: wwm-orders/123 -> resource=wwm-orders, id=123
$pathParts = $route ? explode('/', $route) : [];
$resource = $pathParts[0] ?? '';
$resourceId = isset($pathParts[1]) && $pathParts[1] !== '' ? $pathParts[1] : null;
$sub = isset($pathParts[1]) ? $pathParts[1] : null;

if ($resource === 'wwm-orders') {
    if ($resourceId && $resourceId !== 'check-uid') {
        // PUT/PATCH /api/wwm-orders/{id}
        if (in_array($method, ['PUT', 'PATCH'])) {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $status = $input['status'] ?? $_GET['status'] ?? null;
            if ($status !== null) {
                $wwmOrderController->updateOrder($resourceId, $status);
            } else {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'status required']);
            }
            exit;
        }
    }
    if ($sub === 'check-uid') {
        // GET /api/wwm-orders/check-uid?uid=xxx
        if ($method === 'GET') {
            header('Content-Type: application/json');
            $uid = $_GET['uid'] ?? '';
            $url = 'https://pay.neteasegames.com/gameclub/wherewindsmeet/-1/login-role';
            $params = [
                'deviceid' => 208134903679537732,
                'traceid' => '0b6f9253-6974-4744-ab87-99a1c05f4723',
                'timestamp' => (string)(round(microtime(true) * 1000)),
                'gc_client_version' => '1.12.5',
                'roleid' => $uid,
                'client_type' => 'gameclub',
            ];
            $ch = curl_init($url . '?' . http_build_query($params));
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ['User-Agent: Mozilla/5.0', 'Referer: https://pay.neteasegames.com/'],
            ]);
            echo curl_exec($ch);
            curl_close($ch);
        } else {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
        }
        exit;
    }
    if ($method === 'GET') {
        $wwmOrderController->getOrders();
        exit;
    }
    if ($method === 'POST') {
        $wwmOrderController->apiStore();
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

if ($resource === 'steam-orders') {
    if ($method === 'GET') {
        $steamController->getOrders();
        exit;
    }
    if ($method === 'PUT' || $method === 'PATCH') {
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = $resourceId ?? ($input['id'] ?? $_GET['id'] ?? null);
        $status = $input['status'] ?? $_GET['status'] ?? null;
        if ($id !== null && $status !== null) {
            $steamController->updateOrder($id, $status);
        } else {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'id and status required']);
        }
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

if ($resource === 'midas-japan-orders' && $method === 'GET') {
    $midasJapanOrderController = new MidasBuyJapanOrderController();
    $midasJapanOrderController->getOrders();
    exit;
}

// Fallback: gọi theo ?act= (giữ tương thích cũ)
if ($route === '' && isset($_GET['act'])) {
    switch ($_GET['act']) {
        case 'get-steam':
            $steamController->getOrders();
            break;
        case 'update-steam':
            $id = $_GET['id'] ?? ($_POST['id'] ?? null);
            $status = $_GET['status'] ?? ($_POST['status'] ?? null);
            if ($id !== null && $status !== null) {
                $steamController->updateOrder($id, $status);
            }
            break;
        case 'update-money':
            $orderController = new OrderController();
            $orderController->updateMoney2($_GET['id'], $_GET['balance']);
            break;
        case 'get-wwm-orders':
            $wwmOrderController->getOrders();
            break;
        case 'wwm-orders-store':
            $wwmOrderController->apiStore();
            break;
        case 'get-midas-japan-orders':
            $midasJapanOrderController = new MidasBuyJapanOrderController();
            $midasJapanOrderController->getOrders();
            break;
        case 'update-wwm-order':
            $wwmOrderController->updateOrder($_GET['id'], $_GET['status']);
            break;
        case 'check-uid-wwm':
            header('Content-Type: application/json');
            $uid = $_GET['uid'] ?? '';
            $url = 'https://pay.neteasegames.com/gameclub/wherewindsmeet/-1/login-role';
            $params = [
                'deviceid' => 208134903679537732,
                'traceid' => '0b6f9253-6974-4744-ab87-99a1c05f4723',
                'timestamp' => (string)(round(microtime(true) * 1000)),
                'gc_client_version' => '1.12.5',
                'roleid' => $uid,
                'client_type' => 'gameclub',
            ];
            $ch = curl_init($url . '?' . http_build_query($params));
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ['User-Agent: Mozilla/5.0', 'Referer: https://pay.neteasegames.com/'],
            ]);
            echo curl_exec($ch);
            curl_close($ch);
            break;
        default:
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Not Found']);
    }
    exit;
}

if ($route !== '') {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Not Found']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Missing act or route']);
