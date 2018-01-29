<?php
    /**
        DO NOT TOUCH THE NEXT LINES
    **/
    ini_set("session.cookie_domain", ".meeterz.waapi.fr");
    define( 'ROOT_DIR', dirname(__FILE__) );
    define("ON", true);
    define("OFF", false);
    $obligatory_files=["Core/lang/lang.php",
                       "Core/functions.php",
                       "Core/jwtToken.php",
                       "Controllers/controller.php",
                       "Model/error.class.php",
                       "Model/modele.class.php"];
    $modules=[];
    $prefix_hash="E48#Idxn";
    $jwtKey="EmpkNz87Afamp";
    /** 
        YOU CAN EDIT NOW
    **/
    $networks=["facebook","twitter","googlePlus","instagram"];
    $database_config=["host" => 'localhost', "user" => 'dr166627', "password" => 'bTrm5&45', "database" => 'meeterz_dev2'];
    $debug=ON;
    $base_web=ROOT_DIR."/../";
    $base_web_view=$base_web."../api2.meeterz.waapi.fr/";
    define("USER", 0);
    define("EVENT", 1);
    define("TALENT", 2);
    define("REGISTRATION", 3);
    define("AFFINITY", 4);
    define("TAG", 5);
    $lang_available=["fr", "en"];
    $modules[USER]=["state" => ON, // ON to put enabled the module
                       "files" => ["Model/account.class.php", "Controllers/account.controller.php"], // Which files we require to use it ?
                       "controllers" => "AccountController",
                       "modules" => []]; // Which others modules are require to use this one  
    $modules[EVENT]=["state" => ON, // ON to put enabled the module
                       "files" => ["Model/event.class.php", "Controllers/event.controller.php"], // Which files we require to use it ?
                       "controllers" => "EventController",
                       "modules" => [USER, REGISTRATION, TALENT, TAG]]; // Which others modules are require to use this one  
    $modules[TALENT]=["state" => ON, // ON to put enabled the module
                       "files" => ["Model/talent.class.php", "Controllers/talent.controller.php"], // Which files we require to use it ?
                       "controllers" => "TalentController",
                       "modules" => [AFFINITY]]; // Which others modules are require to use this one  
    $modules[REGISTRATION]=["state" => ON, // ON to put enabled the module
                       "files" => ["Model/registration.class.php", "Controllers/registration.controller.php"], // Which files we require to use it ?
                       "controllers" => "RegistrationController",
                       "modules" => []]; // Which others modules are require to use this one  
    $modules[AFFINITY]=["state" => ON, // ON to put enabled the module
                       "files" => ["Model/affinity.class.php", "Controllers/affinity.controller.php"], // Which files we require to use it ?
                       "controllers" => "AffinityController",
                       "modules" => []]; // Which others modules are require to use this one  
    $modules[TAG]=["state" => ON, // ON to put enabled the module
                       "files" => ["Model/tag.class.php"], // Which files we require to use it ?
                       "controllers" => "",
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
    foreach ($obligatory_files as $file){
        require($base_web.$file);
    }