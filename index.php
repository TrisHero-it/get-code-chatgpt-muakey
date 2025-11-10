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
    require_once 'vendor/autoload.php';
    $codeController = new CodeController();
    $steamController = new SteamController();
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