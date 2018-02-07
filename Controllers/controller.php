<?php
    class Controller{
        protected $_request;
        public function __construct(){ }
        public function buildFromRequest($r){
            $this->_request=$r;
            $action=(count($r)>2)?$r[2]:$r[1];
            switch ($action){
                default:
                    if (method_exists($this, $action))
                    {
                        return $this->$action ();
                    }
                    header("HTTP/1.0 404 Not Found");
                    break;
            }
        }
        public function hadToBeAuth($state){
            if (($state && !isAuth()) || (!$state && isAuth()))
            {
                header("HTTP/1.1 401 Unauthorized");
                exit();
            }
        }
    }