<?php
    class Talent extends Modele{
        private $_idTalent;
        private $_lastName;
        private $_firstName;
        private $_profilePicture;
        private $_headerPicture;
        private $_birthDate;
        private $_birthCity;
        private $_job;
        private $_occupation;
        private $_facebook;
        private $_instagram;
        private $_twitter;
        private $_googlep;
        private $_manager;
        private $_category;
        
        const SELECT = 'SELECT * FROM talent WHERE id=?';
        const SELECT_TOP = 'SELECT id, firstName, lastName, profilePicture FROM talent WHERE id IN (SELECT idTalent, MAX(COUNT(*)) AS c FROM affinity GROUP BY (idTalent) ORDER BY c DESC LIMIT 12)';
        public function __construct($idTalent=NULL, $lastName=NULL, $firstName=NULL, $profilePicture=NULL, $headerPicture=NULL, $birthDate=NULL, $birthCity=NULL, $job=NULL, $occupation=NULL, $facebook=NULL, $instagram=NULL, $twitter=NULL, $googlep=NULL, $manager=NULL, $category=NULL) {
            parent::__construct();
            if (func_num_args()==1)
            {
                $this->loadFromDb($idTalent);
            }
            else
            {
                $this->loadFromInfo($idTalent, $lastName, $firstName, $profilePicture, $headerPicture, $birthDate, $birthCity, $job, $occupation, $facebook, $instagram, $twitter, $googlep, $manager, $category);
            }
        }
        public function loadFromDb($idTalent){
            try{
                $req=$this->execute(self::SELECT, [$idTalent]);
                $data=$req->fetch();
                if (!isset($data["lastName"]))
                {
                    $this->_errors=true;
                    return;
                }
                $this->loadFromInfo($data["id"], $data["lastName"], $data["firstName"], $data["profilePicture"], $data["headerPicture"], $data["birthDate"], $data["birthCity"], $data["job"], $data["occupation"], $data["facebook"], $data["instagram"], $data["twitter"], $data["google"], $data["idManager"], $data["categories"]);
            } catch(Exception $ex) {
                
            } 
        }
        public function getTop(){
            try{
                return $this->query(self::SELECT_TOP)->fetchAll(PDO::FETCH_COLUMN);
            } catch(Exception $ex) {
                return [];
            } 
        }
        public function loadFromInfo($idTalent, $lastName, $firstName, $profilePicture, $headerPicture, $birthDate, $birthCity, $job, $occupation, $facebook, $instagram, $twitter, $googlep, $manager, $category) {
            $this->_idTalent = $idTalent;
            $this->_lastName = $lastName;
            $this->_firstName = $firstName;
            $this->_profilePicture = $profilePicture;
            $this->_headerPicture = $headerPicture;
            $this->_birthDate = $birthDate;
            $this->_birthCity = $birthCity;
            $this->_job = $job;
            $this->_occupation = $occupation;
            $this->_facebook = $facebook;
            $this->_instagram = $instagram;
            $this->_twitter = $twitter;
            $this->_googlep = $googlep;
            $this->_manager = $manager;
            $this->_category = $category;
        }
    }