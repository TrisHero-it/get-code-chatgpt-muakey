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

// Xử lý theo ?act=
if (isset($_GET['act'])) {
    switch ($_GET['act']) {
        case 'get-steam':
            $steamController->getOrders();
            break;
        case 'update-steam':
            $input = json_decode(file_get_contents('php://input'), true) ?: [];
            $id = $_GET['id'] ?? ($_POST['id'] ?? ($input['id'] ?? null));
            $status = $_GET['status'] ?? ($_POST['status'] ?? ($input['status'] ?? null));
            if ($id !== null && $status !== null) {
                $steamController->updateOrder($id, $status);
            } else {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'id and status required']);
            }
            break;
        case 'update-money':
            $orderController = new OrderController();
            $orderController->updateMoney2($_GET['id'], $_GET['balance']);
            break;
        case 'get-wwm-orders':
            $wwmOrderController->getOrders();
            break;

        case 'check-status-wwm-orders':
            $orderId = $_GET['order_id'] ?? '';
            $wwmOrderController->checkStatusOrders($orderId);
            break;
        case 'wwm-orders-store':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method Not Allowed. POST required']);
                exit;
            }
            $wwmOrderController->apiStore();
            break;
        case 'get-midas-japan-orders':
            $midasJapanOrderController = new MidasBuyJapanOrderController();
            $midasJapanOrderController->getOrders();
            break;
        case 'update-wwm-order':
            $input = json_decode(file_get_contents('php://input'), true) ?: [];
            $id = $_GET['id'] ?? ($_POST['id'] ?? ($input['id'] ?? null));
            $status = $_GET['status'] ?? ($_POST['status'] ?? ($input['status'] ?? null));
            if ($id !== null && $status !== null) {
                $wwmOrderController->updateOrder($id, $status);
            } else {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'id and status required']);
            }
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

header('Content-Type: application/json; charset=utf-8');
http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Missing act parameter']);
