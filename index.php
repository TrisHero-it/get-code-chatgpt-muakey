<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhận code</title>
    <link rel="shortcut icon" href="css/logo/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body>
    <?php
    require_once 'controllers/CodeController.php';
    require_once 'controllers/AccountController.php';
    require_once 'controllers/SteamController.php';
    require_once 'controllers/OrderController.php';
    require_once 'controllers/WwmOrderController.php';
    require_once 'controllers/MidasBuyAccountController.php';
    require_once 'controllers/MidasBuyOrderController.php';
    require_once 'controllers/MidasBuyJapanOrderController.php';
    require_once 'vendor/autoload.php';
    $codeController = new CodeController();
    $steamController = new SteamController();
    $wwmOrderController = new WwmOrderController();
    if (isset($_GET['act'])) {
        // Xác thực HTTP Basic Authentication
        $username = 'admin';
        $password = 'Muakey@@111';

        $accountController = new AccountController();
        switch ($_GET['act']) {
            case 'add':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $accountController->add();
                break;
            case 'store':
                $accountController->store();
                break;
            case 'delete':
                $accountController->delete($_GET['id']);
                break;
            case 'list':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {

                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $accountController->index();
                break;
            case 'export':
                $accountController->exportExcel();
                break;

            case "get-steam":
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {

                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $steamController->getOrders();
                break;
            case "update-steam":
                $steamController->updateOrder($_GET['id'], $_GET['status']);
                break;
            case 'wwm-order-add':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }

                $wwmOrderController->add();
                break;
            case 'wwm-order-store':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $wwmOrderController->store();
                break;
            case 'wwm-orders':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $wwmOrderController->index();
                break;
            case 'wwm-order-edit':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $wwmOrderController->edit($_GET['id']);
                break;
            case 'wwm-order-update':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $wwmOrderController->updateFull();
                break;
            case 'wwm-order-delete':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $wwmOrderController->delete($_GET['id']);
                break;
            case 'wwm-order-delete-all':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $wwmOrderController->deleteAllExceptPending();
                break;
            case 'orders-dashboard':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                require_once "views/orders/dashboard.php";
                break;
            case 'order-add':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $orderController = new OrderController();
                $orderController->add();
                break;
            case 'order-store':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $orderController = new OrderController();
                $orderController->store();
                break;
            case 'order-edit':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $orderController = new OrderController();
                $orderController->edit($_GET['id']);
                break;
            case 'order-update':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $orderController = new OrderController();
                $orderController->update();
                break;
            case 'orders':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $orderController = new OrderController();
                $orderController->index();
                break;
            case 'order-delete':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $orderController = new OrderController();
                $orderController->delete($_GET['id']);
                break;
            case 'order-delete-all':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $orderController = new OrderController();
                $orderController->deleteAllExceptPending();
                break;
            case 'money-edit':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $orderController = new OrderController();
                $orderController->editMoney();
                break;
            case 'money-update':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $orderController = new OrderController();
                $orderController->updateMoneyData();
                break;
            case 'payment-codes-add':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $orderController = new OrderController();
                $orderController->addPaymentCodes();
                break;
            case 'payment-codes-store':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $orderController = new OrderController();
                $orderController->storePaymentCodes();
                break;
            case 'payment-codes-list':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $orderController = new OrderController();
                $orderController->listPaymentCodes();
                break;
            case 'payment-codes-delete-all':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $orderController = new OrderController();
                $orderController->deleteAllPaymentCodes();
                break;
            case 'payment-code-delete':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $orderController = new OrderController();
                $orderController->deletePaymentCode($_GET['id']);
                break;
            case 'midas-accounts':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $midasAccountController = new MidasBuyAccountController();
                $midasAccountController->index();
                break;
            case 'midas-account-add':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $midasAccountController = new MidasBuyAccountController();
                $midasAccountController->add();
                break;
            case 'midas-account-store':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $midasAccountController = new MidasBuyAccountController();
                $midasAccountController->store();
                break;
            case 'midas-account-edit':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $midasAccountController = new MidasBuyAccountController();
                $midasAccountController->edit($_GET['id']);
                break;
            case 'midas-account-update':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $midasAccountController = new MidasBuyAccountController();
                $midasAccountController->update2();
                break;
            case 'midas-account-delete':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $midasAccountController = new MidasBuyAccountController();
                $midasAccountController->delete($_GET['id']);
                break;
            case 'midas-orders':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $midasOrderController = new MidasBuyOrderController();
                $midasOrderController->index();
                break;
            case 'midas-order-add':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $midasOrderController = new MidasBuyOrderController();
                $midasOrderController->add();
                break;
            case 'midas-order-store':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $midasOrderController = new MidasBuyOrderController();
                $midasOrderController->store();
                break;
            case 'midas-order-edit':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $midasOrderController = new MidasBuyOrderController();
                $midasOrderController->edit($_GET['id']);
                break;
            case 'midas-order-update':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $midasOrderController = new MidasBuyOrderController();
                $midasOrderController->updateOrder();
                break;
            case 'midas-order-delete':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $midasOrderController = new MidasBuyOrderController();
                $midasOrderController->delete($_GET['id']);
                break;
            case 'midas-order-delete-all':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $midasOrderController = new MidasBuyOrderController();
                $midasOrderController->deleteAllExceptPending();
                break;
            case 'midas-japan-orders':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $midasJapanOrderController = new MidasBuyJapanOrderController();
                $midasJapanOrderController->index();
                break;
            case 'midas-japan-order-add':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $midasJapanOrderController = new MidasBuyJapanOrderController();
                $midasJapanOrderController->add();
                break;
            case 'midas-japan-order-store':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $midasJapanOrderController = new MidasBuyJapanOrderController();
                $midasJapanOrderController->store();
                break;
            case 'midas-japan-order-edit':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $midasJapanOrderController = new MidasBuyJapanOrderController();
                $midasJapanOrderController->edit($_GET['id']);
                break;
            case 'midas-japan-order-update':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $midasJapanOrderController = new MidasBuyJapanOrderController();
                $midasJapanOrderController->updateOrder();
                break;
            case 'midas-japan-order-delete':
                if (
                    !isset($_SERVER['PHP_AUTH_USER']) ||
                    !isset($_SERVER['PHP_AUTH_PW']) ||
                    $_SERVER['PHP_AUTH_USER'] !== $username ||
                    $_SERVER['PHP_AUTH_PW'] !== $password
                ) {
                    header('WWW-Authenticate: Basic realm="Admin Area"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h1>Unauthorized Access</h1>';
                    echo '<p>You need to provide valid credentials to access this area.</p>';
                    exit;
                }
                $midasJapanOrderController = new MidasBuyJapanOrderController();
                $midasJapanOrderController->delete($_GET['id']);
                break;
            default:
                $codeController->index();
                break;
        }
    } else {
        $codeController->index();
    }

    ?>
</body>

</html>