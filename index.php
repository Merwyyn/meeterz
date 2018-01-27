<?php
    header('content-type:application/json');
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Origin: https://meeterz.waapi.fr");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
    if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'OPTIONS')
    {
        exit();
    }
    require 'Core/configuration.php';
    $request = explode("/", $_SERVER['REQUEST_URI']);
    $key_module=constant(strtoupper($request[1]));
    if (!isset($modules[$key_module]))
    {
        header("HTTP/1.0 404 Not Found");
        exit();
    }
    insert_require($key_module);
    $controller=new $modules[$key_module]["controllers"] ();
    echo json_encode($controller->buildFromRequest($request));