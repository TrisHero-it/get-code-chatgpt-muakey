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
    $codeController = new CodeController();

    if (isset($_GET['act'])) {
        $accountController = new AccountController();
        $ip = $codeController->getClientIP();
        if ($ip == '1.54.23.12') {
            switch ($_GET['act']) {
                case 'add':
                    $accountController->add();
                    break;
                case 'store':
                    $accountController->store();
                    break;
                case 'delete':
                    $accountController->delete($_GET['id']);
                    break;
                default:
                    $accountController->index();
                    break;
            }
        } else {
            $codeController->index();
        }
    } else {
        $codeController->index();
    }

    ?>
</body>

</html>