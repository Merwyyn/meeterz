<?php
    class Affinity extends Modele{
        private $_idUser;
        private $_idTalent;
        private $_searched;
        private $_sharedGoogle;
        private $_sharedTwitter;
        private $_sharedFacebook;
        private $_sharedInstagram;
        private $_subscribed;
        private $_new;
        const SELECT = 'SELECT * FROM affinity WHERE idUser=? AND idTalent=?';
        const SAVE = 'INSERT INTO affinity VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        const UPDATE = 'UPDATE affinity SET searched=?, sharedGoogle=?, sharedTwitter=?, sharedFacebook=?, sharedInstagram=?, subscribed=? WHERE idUser=? AND idTalent=?';
        public function __construct($idUser=NULL, $idTalent=NULL, $searched=NULL, $sharedGoogle=NULL, $sharedTwitter=NULL, $sharedFacebook=NULL, $sharedInstagram=NULL, $subscribed=NULL) {
            parent::__construct();
            if (func_num_args()==2)
            {
                $this->loadFromDb($idUser, $idTalent);
                $this->_new=false;
            }
            else
            {
                $this->loadFromInfo($idUser, $idTalent, $searched, $sharedGoogle, $sharedTwitter, $sharedFacebook, $sharedInstagram, $subscribed);
                $this->_new=true;
            }
        }
        public function toData(){
            return ["idUser" => $this->_idUser, "idTalent" => $this->_idTalent, "searched" => $this->_searched, "sharedGoogle" => $this->_sharedGoogle, "sharedTwitter" => $this->_sharedTwitter, "sharedFacebook" => $this->_sharedFacebook, "sharedInstagram" => $this->_sharedInstagram, "subscribed" => $this->_subscribed];
        }
        public function save(){
            try{
                if ($this->_new)
                {
                    $this->execute(self::SAVE, [$this->_idUser, $this->_idTalent, $this->_searched, $this->_sharedGoogle, $this->_sharedTwitter, $this->_sharedFacebook, $this->_sharedInstagram, $this->_subscribed]);
                }
                else
                {
                    $this->execute(self::UPDATE, [$this->_searched, $this->_sharedGoogle, $this->_sharedTwitter, $this->_sharedFacebook, $this->_sharedInstagram, $this->_subscribed, $this->_idUser, $this->_idTalent]);
                }    
            } catch(Exception $ex) {
                
            } 
        }
        public function loadFromDb($idUser, $idTalent){
            try{
                $req=$this->execute(self::SELECT, [$idUser, $idTalent]);
                $data=$req->fetch();
                if (!isset($data["searched"]))
                {
                    $this->_errors=true;
                    return;
                }
                $this->loadFromInfo($data["idUser"], $data["idTalent"], $data["searched"], $data["sharedGoogle"], $data["sharedTwitter"], $data["sharedFacebook"], $data["sharedInstagram"], $data["subscribed"]);
            } catch(Exception $ex) {
                
            } 
        }
        public function loadFromInfo($idUser, $idTalent, $searched, $sharedGoogle, $sharedTwitter, $sharedFacebook, $sharedInstagram, $subscribed) {
            $this->_idUser = $idUser;
            $this->_idTalent = $idTalent;
            $this->_searched = $searched;
            $this->_sharedGoogle = $sharedGoogle;
            $this->_sharedTwitter = $sharedTwitter;
            $this->_sharedFacebook = $sharedFacebook;
            $this->_sharedInstagram = $sharedInstagram;
            $this->_subscribed = $subscribed;
        }
        public function getIdUser() {
            return $this->_idUser;
        }

        public function getIdTalent() {
            return $this->_idTalent;
        }

        public function getSearched() {
            return $this->_searched;
        }

        public function getSharedGoogle() {
            return $this->_sharedGoogle;
        }

        public function getSharedTwitter() {
            return $this->_sharedTwitter;
        }

        public function getSharedFacebook() {
            return $this->_sharedFacebook;
        }

        public function getSharedInstagram() {
            return $this->_sharedInstagram;
        }

        public function getSubscribed() {
            return $this->_subscribed;
        }

        public function setIdUser($idUser) {
            $this->_idUser = $idUser;
        }

        public function setIdTalent($idTalent) {
            $this->_idTalent = $idTalent;
        }

        public function setSearched($searched) {
            $this->_searched = $searched;
        }

        public function setSharedGoogle($sharedGoogle) {
            $this->_sharedGoogle = $sharedGoogle;
        }

        public function setSharedTwitter($sharedTwitter) {
            $this->_sharedTwitter = $sharedTwitter;
        }

        public function setSharedFacebook($sharedFacebook) {
            $this->_sharedFacebook = $sharedFacebook;
        }

        public function setSharedInstagram($sharedInstagram) {
            $this->_sharedInstagram = $sharedInstagram;
        }

        public function setSubscribed($subscribed) {
            $this->_subscribed = $subscribed;
        }
    }