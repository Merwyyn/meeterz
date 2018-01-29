<?php
    function getMonthString($i){
        return constant("MONTH_".$i);
    }
    function isPhoneNumber($phoneNumber){
        return preg_match("^(0|\+33)[1-9]([-. ]?[0-9]{2}){4}$", $phoneNumber);
    }
    function isEmail($email){
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    function isAuth(){
        return (getToken()!=NULL);
    }
    function getToken(){
        $token=NULL;
        $headers = apache_request_headers();
        if(isset($headers['Authorization'])){
            $matches = explode(" ", $headers['Authorization']);
            if(count($matches)==2 && $matches[0]=="Bearer"){
                $token = $matches[1];
            }
        }
        return decodeToken($token);
    }
    function decodeToken($token){
        global $jwtKey;
        try{
            return JWT::decode($token, $jwtKey);
        } catch (Exception $ex) {
            return NULL;
        }
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
    function insert_require($key)
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
    }
    function cryptPassword($pass){
        global $prefix_hash;
        $hash=$prefix_hash.$pass;
        for ($i=0;$i<1000;$i++) { $hash=hash("sha256", $hash); }
        return $hash;
    }