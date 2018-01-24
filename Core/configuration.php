<?php
    /**
        DO NOT TOUCH THE NEXT LINES
    **/
    ini_set("session.cookie_domain", ".meeterz.waapi.fr");
    define( 'ROOT_DIR', dirname(__FILE__) );
    define("ON", true);
    define("OFF", false);
    $obligatory_files=["Core/langues/langues.php",
                       "Core/functions.php",
                       "Model/error.class.php",
                       "Model/modele.class.php"];
    $modules=[];
    $prefix_hash="A7k2NiZ";
    /** 
        YOU CAN EDIT NOW
    **/
    $database_config=["host" => 'localhost', "user" => 'dr166627', "password" => 'bTrm5&45', "database" => 'meeterz_dev2'];
    $debug=ON;
    $base_web=ROOT_DIR."/../";
    $base_web_view=$base_web."../api2.meeterz.waapi.fr/";
    define("ACCOUNT", 0);
    define("ACTIVITIES", 1);
    define("INSTALLATIONS", 2);
    define("RESERVATIONS", 3);
    define("INCIDENTS", 3);
    define("MESSAGES", 4);
    $lang_available=["fr", "en"];
    $modules[ACCOUNT]=["state" => ON, // ON to put enabled the module
                       "files" => ["Model/users.class.php", // Which files we require to use it ?
                                   "Model/profileUsers.class.php",
                                   "Model/campingUsers.class.php",
                                   "Model/accountUsers.class.php"],
                       "controllers" => ["Controller/account/accountError.controller.php"], // Which files we require to verify the data
                       "modules" => []]; // Which others modules are require to use this one   
    /**
        DO NOT TOUCH THE NEXT LINES
    **/
    define("DEFAULT_LANGUE", $lang_available[0]);
    define("DEFAULT_LANGUE_ETRANGER", $lang_available[max(0, count($lang_available)-1)]);						
    if ($debug){
        ini_set('display_errors','on');
        error_reporting(E_ALL);
    }