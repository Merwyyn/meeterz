<?php
    class Brand extends Modele{
        private $_id;
        private $_name;
        private $_url;
        private $_logo;
        
        const CREATE = 'INSERT INTO brand (name, url, logo) VALUES (?, ?, ?)';
        const UPDATE = 'UPDATE brand SET name=?, url=?, logo=? WHERE id=?';
        const DELETE = 'DELETE FROM brand WHERE id=?';
        const SELECT = 'SELECT * FROM brand WHERE id=?';
        const SELECT_ALL = 'SELECT * FROM brand';
        public function __construct($id=NULL, $name=NULL, $url=NULL, $logo=NULL) {
            parent::__construct();
            if (func_num_args()==1)
            {
                $this->loadFromDb($id);
            }
            else
            {
                $this->loadFromInfo($id, $name, $url, $logo);
            }
        }
        public function getAll(){
            return $this->query(self::SELECT_ALL)->fetchAll(PDO::FETCH_COLUMN);
        }
        public function loadFromDb($id){
            try{
                $req=$this->execute(self::SELECT, [$id]);
                $data=$req->fetch();
                if (!isset($data["name"]))
                {
                    $this->_errors=true;
                    return;
                }
                $this->loadFromInfo($data["id"], $data["name"], $data["url"], $data["logo"]);
            } catch(Exception $ex) {
                
            } 
        }
        public function loadFromInfo($id, $name, $url, $logo) {
            $this->_id = $id;
            $this->_name = $name;
            $this->_url = $url;
            $this->_logo = $logo;
        }
        public function save(){
            try{
                if ($this->_id){
                    $this->execute(self::UPDATE, [$this->_name, $this->_url, $this->_logo, $this->_id]);
                } else {
                    $this->execute(self::CREATE, [$this->_name, $this->_url, $this->_logo]);
                }
                return ["success" => true];
            } catch (Exception $ex) {
                return ["error" => WRONG_HAPPENS];
            }
        }
        public function delete(){
            try{
                $this->execute(self::DELETE, [$this->_id]);
                return ["success" => true];
            } catch (Exception $ex) {
                return ["error" => WRONG_HAPPENS];
            }
        }
        public function getId() {
            return $this->_id;
        }
        public function getName() {
            return $this->_name;
        }
        public function getUrl() {
            return $this->_url;
        }
        public function getLogo() {
            return $this->_logo;
        }
        public function setId($id) {
            $this->_id = $id;
        }
        public function setName($name) {
            $this->_name = $name;
        }
        public function setUrl($url) {
            $this->_url = $url;
        }
        public function setLogo($logo) {
            $this->_logo = $logo;
        }
    }