<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhận code</title>
    <link rel="shortcut icon" href="https://muakey.com/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php
    require_once 'controllers/CodeController.php';

    $codeController = new CodeController();
    $codeController->index();

    ?>
</body>

</html>