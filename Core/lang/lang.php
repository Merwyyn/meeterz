<?php
    $lang=filter_input(INPUT_GET, "lang");
    if ($lang && isset($lang_available[$lang]))
    {
        $_SESSION["lang"]=$lang;
    }
    elseif (!isset($_SESSION["lang"]))
    {
        $_SESSION["lang"]=DEFAULT_LANGUE;
        $lang_browser=filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE');
        if ($lang_browser && isset($lang_available[substr($lang_browser, 0, 2)]))
        {
            $_SESSION["lang"]=substr($lang_browser, 0, 2);
        }
        else
        {
            $_SESSION["lang"]=DEFAULT_LANGUE_ETRANGER;
        }
    }
    define("LANG_USER", $_SESSION["lang"]);
    require_once(LANG_USER.".php");
    foreach ($lang_available as $k)
    {
        $lang_available[$k]=constant("LANG_".mb_strtoupper($k));
    }
    function __($constant, ...$array_var)
    {
        $texte_final=$constant;
        $array_temp=$array_var;
        $key=0;
        while (preg_match("/%([a-zA-Z0-9_]+)%/", $texte_final) && $key<count($array_temp))
        {
                $texte_final=preg_replace('/%([a-zA-Z0-9_]+)%/', $array_temp[$key], $texte_final, 1);
                $key++;
        }
        return $texte_final;
    }