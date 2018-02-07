<?php
    class Registration extends Modele{
        private $_idUser;
        private $_idEvent;
        private $_validity;
        private $_date;
        private $_participation;
        private $_new;
        const SAVE = 'INSERT INTO registration VALUES (?, ?, ?, ?, ?)';
        const UPDATE = 'UPDATE registration SET validity=?, date=?, participation=? WHERE idUser=? AND idEvent=?';
        const SELECT = 'SELECT * FROM registration WHERE idUser=? AND idEvent=?';
        const SELECT_BY_USER_VALID = 'SELECT idEvent FROM registration WHERE idUser=? AND validity=1';
        const SELECT_ID_RECOMMENDED = 'SELECT idEvent, COUNT(idEvent) as c FROM registration '
                . 'WHERE idUser IN '
                    . '(SELECT idUser FROM registration '
                    . 'LEFT JOIN event ON idEvent=id '
                    . 'WHERE idEvent IN (%LISTE_ID%) AND idUser!=? AND validity=1 AND dateLimit>? AND openingDate<? '
                    . 'GROUP BY (idUser)) '
                . 'AND idEvent NOT IN (%LISTE_ID2%) GROUP BY (idEvent) '
                . 'ORDER BY c DESC LIMIT 6';
        const COUNT_REGISTERED = 'SELECT COUNT(*) FROM registration WHERE validity=1';
        const COUNT_REGISTERED_BY_ID = 'SELECT COUNT(*) FROM registration WHERE validity=1 AND idEvent=?';
        const COUNT_PARTICIPATE = 'SELECT COUNT(*) FROM registration WHERE participation=1';
        public function getCountRegisteredByEventId($idEvent){
            return $this->execute(self::COUNT_REGISTERED_BY_ID, [$idEvent])->fetchColumn();
        }
        public function getCountRegistered(){
            return $this->query(self::COUNT_REGISTERED)->fetchColumn();
        }
        public function getCountParticipate(){
            return $this->query(self::COUNT_PARTICIPATE)->fetchColumn();
        }
        public function getEventsRecommended($idUser){
            global $debug;
            try{
                $req=$this->execute(self::SELECT_BY_USER_VALID, [$idUser]);
                $idsEvent=[];
                while ($data=$req->fetch())
                {
                    $idsEvent[]=$data["idEvent"];
                }
                if (empty($idsEvent))
                {
                    return [];
                }
                $data=[];
                $req2=$this->execute(__(self::SELECT_ID_RECOMMENDED, implode(",", $idsEvent), implode(",", $idsEvent)), [$idUser, time(), time()]);
                while ($data2=$req2->fetch())
                {
                    $data[]=$data2["idEvent"];
                }
                return $data;
            } catch (Exception $ex){
                if ($debug)
                {
                    return ["error" => $ex];
                }
                return [];
            }
        }
        public function __construct($idUser=NULL, $idEvent=NULL, $validity=NULL, $date=NULL, $participation=NULL) {
            parent::__construct();
            if (func_num_args()==2)
            {
                $this->loadFromDb($idUser, $idEvent);
                $this->_new=false;
            }
            else
            {
                $this->loadFromInfo($idUser, $idEvent, $validity, $date, $participation);
                $this->_new=true;
            }
        }
        public function loadFromDb($idUser, $idEvent){
            try{
                $req=$this->execute(self::SELECT, [$idUser, $idEvent]);
                $data=$req->fetch();
                if (!isset($data["validity"]))
                {
                    $this->_errors=true;
                    return;
                }
                $this->loadFromInfo($data["idUser"], $data["idEvent"], $data["validity"], $data["date"], $data["participation"]);
            } catch(Exception $ex) {
                
            } 
        }
        public function loadFromInfo($idUser, $idEvent, $validity, $date, $participation) {
            $this->_idUser = $idUser;
            $this->_idEvent = $idEvent;
            $this->_validity = $validity;
            $this->_date = $date;
            $this->_participation = $participation;
        }
        public function save(){
            try{
                if ($this->_new)
                {
                    $this->execute(self::SAVE, [$this->_idUser, $this->_idEvent, $this->_validity, $this->_date, $this->_participation]);
                }
                else
                {
                    $this->execute(self::UPDATE, [$this->_validity, $this->_date, $this->_participation, $this->_idUser, $this->_idEvent]);
                }    
            } catch(Exception $ex) {
                
            } 
        }
        public function getIdUser() {
            return $this->_idUser;
        }

        public function getIdEvent() {
            return $this->_idEvent;
        }

        public function getValidity() {
            return $this->_validity;
        }

        public function getDate() {
            return $this->_date;
        }

        public function getParticipation() {
            return $this->_participation;
        }

        public function setIdUser($idUser) {
            $this->_idUser = $idUser;
        }

        public function setIdEvent($idEvent) {
            $this->_idEvent = $idEvent;
        }

        public function setValidity($validity) {
            $this->_validity = $validity;
        }

        public function setDate($date) {
            $this->_date = $date;
        }

        public function setParticipation($participation) {
            $this->_participation = $participation;
        }


    }