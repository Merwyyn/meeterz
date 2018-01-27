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
        const SELECT_TOP_ID = 'SELECT id, count(*) as occ FROM talent t LEFT OUTER JOIN affinity a ON a.idTalent=t.id GROUP BY id ORDER BY occ DESC LIMIT 12';
        const SELECT_TOP = 'SELECT id, firstName, lastName, profilePicture FROM talent WHERE id IN (%ID_TOP%)';
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
            global $debug;
            try{
                $ids="";
                foreach ($this->query(self::SELECT_TOP_ID)->fetchAll(PDO::FETCH_COLUMN) as $id)
                {
                    if ($ids!="")
                    {
                        $ids.=",";
                    }
                    $ids.=$id;
                }
                $results=$this->query(__(self::SELECT_TOP, $ids))->fetchAll();
                return $results;
            } catch(Exception $ex) {
                if ($debug)
                {
                    return ["error" => $ex];
                }
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