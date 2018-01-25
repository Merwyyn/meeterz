<?php
    header('content-type:application/json');
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Origin: https://meeterz.waapi.fr");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
    require 'Core/configuration.php';
    $request = explode("/", $_SERVER['REQUEST_URI']);
    switch (strtolower($request[1])){
        default:
            header("HTTP/1.0 404 Not Found");
            exit();
            break;
        case "login":
            if (isAuth())
            {
                header("HTTP/1.1 401 Unauthorized");
                exit();
            }
            insert_require(ACCOUNT, true);
            $accountController=new AccountController();
            echo json_encode($accountController->login());
            break;
    }