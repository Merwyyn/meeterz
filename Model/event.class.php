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
        const SELECT_NEXT = 'SELECT id, city, name, dateLimit, picture, description, placesLimitMin, COUNT(id) AS registered FROM event e '
                . 'LEFT OUTER JOIN registration r ON e.id=r.idEvent '
                . 'WHERE dateLimit>? AND openingDate<? AND r.validity=1 '
                . 'GROUP BY (id) '
                . 'HAVING registered<placesLimitMin '
                . 'ORDER BY dateLimit ASC LIMIT 6';
        const SELECT_BY_ID = 'SELECT id, city, name, dateLimit, picture, description, placesLimitMin, COUNT(id) AS registered FROM event e '
                . 'LEFT OUTER JOIN registration r ON e.id=r.idEvent '
                . 'WHERE dateLimit>? AND openingDate<? AND r.validity=1 AND id IN (%LISTE_ID%) '
                . 'GROUP BY (id) '
                . 'HAVING registered<placesLimitMin';
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
        public function getMeetsById($ids){
            global $debug;
            try{
                $results=$this->execute(__(self::SELECT_BY_ID, $ids), [time(), time()])->fetchAll();
                foreach ($results as $k => $event)
                {
                    $tmp=$results[$k]["dateLimit"]-time();
                    if ($tmp>24*3600)
                    {
                        $results[$k]["remainingTime"]="J-".floor($tmp/(24*3600));
                    }
                    else if ($tmp>3600)
                    {
                        $results[$k]["remainingTime"]="H-".floor($tmp/3600);
                    }
                    else
                    {
                        $results[$k]["remainingTime"]="M-".ceil($tmp/60);
                    }
                    $results[$k]["type"]="meet";
                }
                return $results;
            } catch(Exception $ex) {
                if ($debug)
                {
                    return ["error" => $ex];
                }
                return [];
            } 
        }
        public function getNextEvent(){
            global $debug;
            try{
                $results=$this->execute(self::SELECT_NEXT, [time(), time()])->fetchAll();
                foreach ($results as $k => $event)
                {
                    $tmp=$results[$k]["dateLimit"]-time();
                    if ($tmp>24*3600)
                    {
                        $results[$k]["remainingTime"]="J-".floor($tmp/(24*3600));
                    }
                    else if ($tmp>3600)
                    {
                        $results[$k]["remainingTime"]="H-".floor($tmp/3600);
                    }
                    else
                    {
                        $results[$k]["remainingTime"]="M-".ceil($tmp/60);
                    }
                }
                return $results;
            } catch(Exception $ex) {
                if ($debug)
                {
                    return ["error" => $ex];
                }
                return [];
            } 
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

