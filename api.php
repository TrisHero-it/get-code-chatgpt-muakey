    <?php
    require_once 'controllers/SteamController.php';
    require_once 'controllers/OrderController.php';
    require_once 'controllers/WwmOrderController.php';
    require_once 'controllers/MidasBuyJapanOrderController.php';
    require_once 'vendor/autoload.php';
    $steamController = new SteamController();
    $wwmOrderController = new WwmOrderController();
    $username = 'admin';
    $password = 'Muakey@@111';
    switch ($_GET['act']) {
        case "get-steam":
            $steamController->getOrders();
            break;
        case "update-steam":
            $id = $_GET['id'] ?? ($_POST['id'] ?? null);
            $status = $_GET['status'] ?? ($_POST['status'] ?? null);

            if ($id !== null && $status !== null) {
                $steamController->updateOrder($id, $status);
            }
            break;
        case "update-money":
            $orderController = new OrderController();
            $orderController->updateMoney2($_GET['id'], $_GET['balance']);
            break;
        case "get-wwm-orders":
            $wwmOrderController->getOrders();
            break;
        case "get-midas-japan-orders":
            $midasJapanOrderController = new MidasBuyJapanOrderController();
            $midasJapanOrderController->getOrders();
            break;
        case "update-wwm-order":
            $wwmOrderController->updateOrder($_GET['id'], $_GET['status']);
            break;

            
        case "check-uid-wwm":
            header('Content-Type: application/json');

            $url = 'https://pay.neteasegames.com/gameclub/wherewindsmeet/-1/login-role';

            $params = [
                'deviceid' => 208134903679537732,
                'traceid' => "0b6f9253-6974-4744-ab87-99a1c05f4723",
                'timestamp' => (string)(round(microtime(true) * 1000)),
                'gc_client_version' => '1.12.5',
                'roleid' => $_GET['uid'],
                'client_type' => 'gameclub',
            ];

            $ch = curl_init($url . '?' . http_build_query($params));

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'User-Agent: Mozilla/5.0',
                    'Referer: https://pay.neteasegames.com/',
                ],
            ]);

            $res = curl_exec($ch);
            curl_close($ch);

            echo $res;

            break;
    }
    ?>