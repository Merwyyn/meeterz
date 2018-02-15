<?php
    function getLatLong($address)
    {
        global $googleKey;
        $prepAddr = str_replace(' ','+',$address);
        $geocode=file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false&key='.$googleKey);
        $output= json_decode($geocode);
        if (isset($output->status) && $output->status=="ZERO_RESULTS")
        {
            return -1;
        }
        if (!isset($output->results[0])){
            return -1;
        }
        $latitude = $output->results[0]->geometry->location->lat;
        $longitude = $output->results[0]->geometry->location->lng;
        return [$latitude, $longitude];
    }
    function calculDistance($lat1, $long1, $lat2, $long2)
    {
        $R = 6371e3; // metres
        $a1 = deg2rad($lat1);
        $a2 = deg2rad($lat2);
        $b1 = deg2rad($lat2-$lat1);
        $b2 = deg2rad($long2-$long1);
        $a = sin($b1/2) * sin($b1/2) +
                cos($a1) * cos($a2) *
                sin($b2/2) * sin($b2/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R*$c;
    }
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