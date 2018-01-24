<?php
    /**
    * Basic Modele class
    *
    * Parent class for every modele
    * That includes all functions for database
    *
    * @param PDO $_db
    * @return self
    */
    class Modele extends ErrorType {
        private $_db;
        
        /**
        * 
        * 
        * @param String $sql Request SQL with dynamic params
        * @return PDOStatement
        */
        public function prepare($sql){
            $a=$this->getDb()->prepare($sql);
            return $a;
        }
        /**
        *
        * @param String $sql Request SQL with constant/no params
        * @return PDOStatement
        */
        public function query($sql){
            $a=$this->getDb()->query($sql);
            return $a;
        }
        /**
        *
        * @param String $sql Request SQL with dynamic params
        * @param Array $params Array of value to replace the dynamics params
        * @return PDOStatement
        */
        public function execute($sql, $params) {
            $a=$this->getDb()->prepare($sql);
            $a->execute($params);
            return $a;
        }
        
        /**
        *
        * @return int
        */
        public function lastInsert(){
            return $this->db->lastInsertId;
        }
        public function getDb() {
            global $database_config;
            if ($this->_db == null){
                $this->_db = new PDO('mysql:host='.$database_config['host'].';dbname='.$database_config['database'].';charset=utf8',
                $database_config['user'], $database_config['password'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            }
            return $this->_db;
        }
    }