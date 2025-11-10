    <?php
    require_once 'controllers/SteamController.php';
    require_once 'controllers/OrderController.php';
    require_once 'vendor/autoload.php';
    $steamController = new SteamController();
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
            
    }
    ?>