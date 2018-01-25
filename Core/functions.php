<?php
    function isPhoneNumber($phoneNumber){
        return preg_match("^(0|\+33)[1-9]([-. ]?[0-9]{2}){4}$", $phoneNumber);
    }
    function isEmail($email){
        return filter_input(FILTER_VALIDATE_EMAIL, $email);
    }
    function isAuth(){
        return (isset($_SESSION["id"]) && $_SESSION["id"]>0);
    }
    function isMobile(){
        if (!($ua=filter_input(INPUT_SERVER, 'HTTP_USER_AGENT')))
        {
            return false;
        }
        return (preg_match('/iphone/i',$ua) || preg_match('/android/i',$ua) || preg_match('/blackberry/i',$ua) || preg_match('/symb/i',$ua) || preg_match('/ipad/i',$ua) || preg_match('/ipod/i',$ua) || preg_match('/phone/i',$ua));
    }
    function generateRandomCharacters($length, $downcase=false)
    {
        $chaine = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        if ($downcase)
        {
            $chaine = 'abcdefghijklmnopqrstuvwxyz'.$chaine;
        }
        return substr(str_shuffle($chaine), 0, $length);
    }
    function isAjax()
    {
        return (($r=filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) && strtolower($r)==='xmlhttprequest');
    }
    function insert_require($key, $insert_controllers=false)
    {
        global $modules;
        global $base_web;
        foreach ($modules[$key]["modules"] as $module)
        {
            if ($modules[$module]["state"])
            {
                insert_require($module);
            }
        }
        foreach ($modules[$key]["files"] as $file)
        {
            require_once($base_web.$file);
        }
        if ($insert_controllers)
        {
            foreach ($modules[$key]["controllers"] as $controller)
            {
                require_once($base_web.$controller);
            }
        }
    }
    function cryptPassword($pass){
        global $prefix_hash;
        $hash=$prefix_hash.$pass;
        for ($i=0;$i<1000;$i++) { $hash=hash("sha256", $hash); }
        return $hash;
    }