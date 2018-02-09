<?php
    class Tag extends Modele{
        private $_id;
        private $_icon;
        private $_description;
        private $_name;
        
        const SELECT = 'SELECT * FROM tags WHERE id=?';
        const SELECT_ALL = 'SELECT * FROM tags';
        const CREATE = 'INSERT INTO tags (icon, description, name) VALUES (?, ?, ?)';
        const DELETE = 'DELETE FROM tags WHERE id=?';
        const UPDATE = 'UPDATE tags SET icon=?, description=?, name=? WHERE id=?';
        public function __construct($id=NULL, $icon=NULL, $description=NULL, $name=NULL) {
            parent::__construct();
            if (func_num_args()==1)
            {
                $this->loadFromDb($id);
            }
            else
            {
                $this->loadFromInfo($id, $icon, $description, $name);
            }
        }
        public function loadFromDb($id){
            try{
                $req=$this->execute(self::SELECT, [$id]);
                $data=$req->fetch();
                if (!isset($data["icon"]))
                {
                    $this->_errors=true;
                    return;
                }
                $this->loadFromInfo($data["id"], $data["icon"], $data["description"], $data["name"]);
            } catch(Exception $ex) {
                
            } 
        }
        public function save(){
            try{
                if ($this->_id){
                    $this->execute(self::UPDATE, [$this->_icon, $this->_description, $this->_name, $this->_id]);
                } else {
                    $this->execute(self::CREATE, [$this->_icon, $this->_description, $this->_name]);
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
        public function loadFromInfo($id, $icon, $description, $name) {
            $this->_id = $id;
            $this->_icon = $icon;
            $this->_description = $description;
            $this->_name = $name;
        }
        public function getAll(){
            return $this->query(self::SELECT_ALL)->fetchAll(PDO::FETCH_COLUMN);
        }
        public function getId() {
            return $this->_id;
        }

        public function getIcon() {
            return $this->_icon;
        }

        public function getDescription() {
            return $this->_description;
        }

        public function getName() {
            return $this->_name;
        }

        public function setId($id) {
            $this->_id = $id;
        }

        public function setIcon($icon) {
            $this->_icon = $icon;
        }

        public function setDescription($description) {
            $this->_description = $description;
        }

        public function setName($name) {
            $this->_name = $name;
        }
    }