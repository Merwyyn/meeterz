<?php
    class Event extends Modele{
        private $_idEvent;
        private $_name;
        private $_description;
        private $_place;
        private $_date;
        private $_dateLimit;
        private $_openingDate;
        private $_placesMax;
        private $_placesMin;
        private $_picture;
        private $_rules;
        private $_city;
        private $_ticket;
        private $_tags;
        private $_brand;
        
        const SELECT = 'SELECT * FROM event WHERE idEvent=?';
        const SELECT_NEXT = 'SELECT idEvent, name, dateLimit, picture, rules, place, placesMin FROM event WHERE dateLimit>? ORDER BY dateLimit ASC LIMIT 6';
        public function __construct($idEvent=NULL, $name=NULL, $description=NULL, $place=NULL, $date=NULL, $dateLimit=NULL, $openingDate=NULL, $placesMax=NULL, $placesMin=NULL, $picture=NULL, $rules=NULL, $city=NULL, $ticket=NULL, $tags=NULL, $brand=NULL) {
            parent::__construct();
            if (func_num_args()==1)
            {
                $this->loadFromDb($idEvent);
            }
            else
            {
                $this->loadFromInfo($idEvent, $name, $description, $place, $date, $dateLimit, $openingDate, $placesMax, $placesMin, $picture, $rules, $city, $ticket, $tags, $brand);
            }
        }
        public function getNextEvent(){
            try{
                $req=$this->execute(self::SELECT_NEXT, [time()]);
                $result=$req->fetchAll(PDO::FETCH_COLUMN);
            } catch(Exception $ex) {
                $result=[];
            } 
            return $result;
        }
        public function loadFromDb($idEvent){
            try{
                $req=$this->execute(self::SELECT, [$idEvent]);
                $data=$req->fetch();
                if (!isset($data["name"]))
                {
                    $this->_errors=true;
                    return;
                }
                $this->loadFromInfo($data["idEvent"], $data["name"], $data["description"], $data["place"], $data["date"], $data["dateLimit"], $data["openingDate"], $data["placesMax"], $data["placesMin"], $data["picture"], $data["rules"], $data["city"], $data["ticket"], $data["tags"], $data["brand"]);
            } catch(Exception $ex) {

            } 
        }
        public function loadFromInfo($idEvent, $name, $description, $place, $date, $dateLimit, $openingDate, $placesMax, $placesMin, $picture, $rules, $city, $ticket, $tags, $brand) {
            $this->_idEvent = $idEvent;
            $this->_name = $name;
            $this->_description = $description;
            $this->_place = $place;
            $this->_date = $date;
            $this->_dateLimit = $dateLimit;
            $this->_openingDate = $openingDate;
            $this->_placesMax = $placesMax;
            $this->_placesMin = $placesMin;
            $this->_picture = $picture;
            $this->_rules = $rules;
            $this->_city = $city;
            $this->_ticket = $ticket;
            $this->_tags = $tags;
            $this->_brand = $brand;
        }
        
        public function getIdEvent() {
            return $this->_idEvent;
        }

        public function getName() {
            return $this->_name;
        }

        public function getPlace() {
            return $this->_place;
        }

        public function getDate() {
            return $this->_date;
        }

        public function getDateLimit() {
            return $this->_dateLimit;
        }

        public function getPlacesMax() {
            return $this->_placesMax;
        }

        public function getPlacesMin() {
            return $this->_placesMin;
        }

        public function getPicture() {
            return $this->_picture;
        }

        public function getRules() {
            return $this->_rules;
        }

        public function getCity() {
            return $this->_city;
        }

        public function getTicket() {
            return $this->_ticket;
        }

        public function getTags() {
            return $this->_tags;
        }

        public function getBrand() {
            return $this->_brand;
        }

        public function setIdEvent($idEvent) {
            $this->_idEvent = $idEvent;
        }

        public function setName($name) {
            $this->_name = $name;
        }

        public function setPlace($place) {
            $this->_place = $place;
        }

        public function setDate($date) {
            $this->_date = $date;
        }

        public function setDateLimit($dateLimit) {
            $this->_dateLimit = $dateLimit;
        }

        public function setPlacesMax($placesMax) {
            $this->_placesMax = $placesMax;
        }

        public function setPlacesMin($placesMin) {
            $this->_placesMin = $placesMin;
        }

        public function setPicture($picture) {
            $this->_picture = $picture;
        }

        public function setRules($rules) {
            $this->_rules = $rules;
        }

        public function setCity($city) {
            $this->_city = $city;
        }

        public function setTicket($ticket) {
            $this->_ticket = $ticket;
        }

        public function setTags($tags) {
            $this->_tags = $tags;
        }

        public function setBrand($brand) {
            $this->_brand = $brand;
        }

        public function getDescription() {
            return $this->_description;
        }

        public function getOpeningDate() {
            return $this->_openingDate;
        }

        public function setDescription($description) {
            $this->_description = $description;
        }

        public function setOpeningDate($openingDate) {
            $this->_openingDate = $openingDate;
        }


    }

