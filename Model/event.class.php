<?php
    class Event extends Modele{
        private $_id;
        private $_idTalent;
        private $_name;
        private $_description;
        private $_place; // Lieu ?
        private $_date;
        private $_dateLimit;
        private $_openingDate;
        private $_placesLimitMin;
        private $_placesLimitMax;
        private $_picture;
        private $_video;
        private $_video_picture;
        private $_rules;
        private $_city;
        private $_ticket;
        private $_tags;
        private $_brand;
        
        const SELECT = 'SELECT * FROM event WHERE id=?';
        const SELECT_NEXT = 'SELECT e.idTalent, id, city, name, dateLimit, picture, description, placesLimitMin, COUNT(id) AS registered, r.validity AS validity FROM event e '
                . 'LEFT OUTER JOIN registration r ON e.id=r.idEvent '
                . 'WHERE dateLimit>? AND openingDate<? AND r.validity=1 OR e.id NOT IN '
                . '(SELECT R2.idEvent FROM registration R2 WHERE R2.idEvent=e.id AND R2.validity=1) '
                . 'GROUP BY (id) '
                . 'HAVING registered<placesLimitMin '
                . 'ORDER BY dateLimit ASC LIMIT %COUNT%';
        const SELECT_BY_ID = 'SELECT e.idTalent, id, city, name, dateLimit, picture, description, placesLimitMin, e.date, e.country, COUNT(id) AS registered FROM event e '
                . 'LEFT OUTER JOIN registration r ON e.id=r.idEvent '
                . 'WHERE dateLimit>? AND openingDate<? AND (r.validity=1 OR e.id NOT IN '
                . '(SELECT R2.idEvent FROM registration R2 WHERE R2.idEvent=e.id AND R2.validity=1)) AND id IN (%LISTE_ID%) '
                . 'GROUP BY (id) '
                . 'HAVING registered<placesLimitMin';
        const SELECT_CITIES = 'SELECT city FROM event GROUP BY (city) ORDER BY city ASC';
        const SELECT_TALENT = 'SELECT talent.id, lastName, firstName FROM event LEFT JOIN talent ON talent.id=event.idTalent GROUP BY (talent.id) ORDER BY firstName ASC';
        const SELECT_DOMAIN = 'SELECT DISTINCT job FROM event LEFT JOIN talent ON talent.id=event.idTalent ORDER BY job ASC';
        const COUNT_MEETS_DONE = 'SELECT COUNT(*) FROM event WHERE date<?';
        const COUNT_MEETS_AVAILABLE = 'SELECT COUNT(*) FROM event WHERE dateLimit>? AND openingDate<?';
        const COUNT_NEXT_MEETS = 'SELECT COUNT(*) FROM event WHERE openingDate>?';
        const SELECT_EVENTS_BY_USER_ID = 'SELECT e.idTalent, id, city, name, dateLimit, picture, description, placesLimitMin, COUNT(id) AS registered FROM event e '
                . 'LEFT OUTER JOIN registration r ON e.id=r.idEvent '
                . 'WHERE dateLimit>? AND openingDate<? AND (r.validity=1  OR e.id NOT IN '
                . '(SELECT R2.idEvent FROM registration R2 WHERE R2.idEvent=e.id AND R2.validity=1)) '
                . ' AND e.id NOT IN (SELECT R2.idEvent FROM registration R2 WHERE R2.idUser=?) '
                . 'GROUP BY (id) '
                . 'HAVING registered<placesLimitMin '
                . 'ORDER BY dateLimit ASC LIMIT %COUNT%';
        const SELECT_NEXT_EVENTS = 'SELECT event.idTalent, id, city, name, openingDate, picture, description FROM event WHERE openingDate>? ORDER BY openingDate ASC LIMIT %COUNT%';
        const SELECT_PAST_EVENTS = 'SELECT event.idTalent, id, city, name, picture, description FROM event WHERE dateLimit<? ORDER BY dateLimit ASC LIMIT %COUNT%';
        const SELECT_ID_BY_CITY = 'SELECT id FROM event WHERE city=? AND dateLimit>? AND openingDate<?';
        const SELECT_EVENT_TODAY_BY_USER = 'SELECT e.id, CONCAT(t.firstName, t.lastName) AS meetingname, e.picture AS meetingpp, e.date AS meetingtime, e.city AS meetingplace FROM event e LEFT JOIN registration r ON e.id=r.idEvent LEFT JOIN talent t ON e.idTalent=t.id WHERE r.idUser=? AND e.date>=? AND e.date<?';
        public function __construct($id=NULL, $idTalent=NULL, $name=NULL, $description=NULL, $place=NULL, $date=NULL, $dateLimit=NULL, $openingDate=NULL, $placesLimitMin=NULL, $placesLimitMax=NULL, $picture=NULL, $video=NULL, $video_picture=NULL, $rules=NULL, $city=NULL, $ticket=NULL, $tags=NULL, $brand=NULL) {
            parent::__construct();
            if (func_num_args()==1)
            {
                $this->loadFromDb($id);
            }
            else
            {
                $this->loadFromInfo($id, $idTalent, $name, $description, $place, $date, $dateLimit, $openingDate, $placesLimitMin, $placesLimitMax, $picture, $video, $video_picture, $rules, $city, $ticket, $tags, $brand);
            }
        }
        public function getEventTodayByUser($idUser){
            $registration=new Registration();
            $today=strtotime(date("d-m-Y"));
            $results=$this->execute(self::SELECT_EVENT_TODAY_BY_USER, [$idUser, $today, $today+24*3600])->fetchAll(PDO::FETCH_COLUMN);
            for ($i=0;$i<count($results);$i++)
            {
                $results[$i]["date"]=date("H:i", $results[$i]["date"]);
                $results[$i]["meetingemail"]="lorem@lipsum.com";
                $results[$i]["goinglist"]=$registration->getListParticipateByEventId($results[$i]["id"]);
                $results[$i]["going"]=count($results[$i]["goinglist"]);
                $results[$i]["pending"]=$registration->getCountRegisteredByEventId($results[$i]["id"])-$results[$i]["going"];
            }
        }
        public function getIdByCity($city){
            global $debug;
            try{
                $results=$this->execute(self::SELECT_ID_BY_CITY, [$city, time(), time()])->fetchAll();
                return $results;
            } catch(Exception $ex) {
                if ($debug)
                {
                    return ["error" => $ex];
                }
                return [];
            }
        }
        public function toData(){
            return ["done" => $this->isDone(), "available" => $this->isAvailable(), "next" => $this->isNext(), "id" => $this->_id, "name" => $this->_name, "description" => $this->_description, "place" => $this->_place, "date" => $this->_date, "dateLimit" => $this->_dateLimit, "openingDate" => $this->_openingDate, "placesLimitMin" => $this->_placesLimitMin,  "placesLimitMax" => $this->_placesLimitMax, "picture" => $this->_picture, "video" => $this->_video, "video_picture" => $this->_video_picture, "rules" => $this->_rules, "city" => $this->_city, "ticket" => $this->_ticket, "tags" => $this->_tags, "brand" => $this->_brand];
        }
        public function getEventByUserId($idUser, $limit=4){
            global $debug;
            try{
                $results=$this->execute(__(self::SELECT_EVENTS_BY_USER_ID, $limit), [time(), time(), $idUser])->fetchAll();
                return $this->formatEventToClient($results);
            } catch(Exception $ex) {
                if ($debug)
                {
                    return ["error" => $ex];
                }
                return [];
            }
        }
        public function getNextEvents($limit=4){
            global $debug;
            try{
                $results=$this->execute(__(self::SELECT_NEXT_EVENTS, $limit), [time()])->fetchAll();
                return $this->formatEventToClient($results);
            } catch(Exception $ex) {
                if ($debug)
                {
                    return ["error" => $ex];
                }
                return [];
            }
        }
        public function getPreviousEvents($limit=4){
            global $debug;
            try{
                $results=$this->execute(__(self::SELECT_PAST_EVENTS, $limit), [time()])->fetchAll();
                return $this->formatEventToClient($results);
            } catch(Exception $ex) {
                if ($debug)
                {
                    return ["error" => $ex];
                }
                return [];
            }
        }
        public function getCountMeetsDone(){
            return $this->execute(self::COUNT_MEETS_DONE, [time()])->fetchColumn();
        }
        public function getCountMeetsAvailable(){
            return $this->execute(self::COUNT_MEETS_AVAILABLE, [time(), time()])->fetchColumn();
        }
        public function getCountNextMeets(){
            return $this->execute(self::COUNT_NEXT_MEETS, [time()])->fetchColumn();
        }
        public function getDataForm(){
            $tmp=$this->query(self::SELECT_CITIES)->fetchAll();
            $city=[];
            for ($i=0;$i<count($tmp);$i++){
                if (!$i%4){
                    $city[]=[];
                }
                $city[floor($i/4)][]=$tmp[$i];
            }
            $tmp=$this->query(self::SELECT_DOMAIN)->fetchAll();
            $domain=[];
            for ($i=0;$i<count($tmp);$i++){
                if (!$i%4){
                    $domain[]=[];
                }
                $domain[floor($i/4)][]=$tmp[$i];
            }
            $tmp=$this->query(self::SELECT_TALENT)->fetchAll();
            $talent=[];
            for ($i=0;$i<count($tmp);$i++){
                if (!$i%4){
                    $talent[]=[];
                }
                $talent[floor($i/4)][]=$tmp[$i];
            }
            return ["city" => $city, "domain" => $domain, "talent" => $talent];
        }
        public function getMeetsById($ids){
            global $debug;
            try{
                $results=$this->execute(__(self::SELECT_BY_ID, $ids), [time(), time()])->fetchAll();
                return $this->formatEventToClient($results);
            } catch(Exception $ex) {
                if ($debug)
                {
                    return ["error" => $ex];
                }
                return [];
            } 
        }
        public function getNextEvent($limit=6){
            global $debug;
            try{
                $results=$this->execute(__(self::SELECT_NEXT, $limit), [time(), time()])->fetchAll();
                return $this->formatEventToClient($results);
            } catch(Exception $ex) {
                if ($debug)
                {
                    return ["error" => $ex];
                }
                return [];
            } 
        }
        private function formatEventToClient($results){
            $controllerAffinity = new AffinityController();
            foreach ($results as $k => $event)
            {
                if (isAuth() && isset($results[$k]["idTalent"]))
                {
                    $results[$k]["affinity"]=$controllerAffinity->getAffinity(getToken()->id, $results[$k]["idTalent"])->toData();
                }
                if (array_key_exists("validity", $results[$k]) && $results[$k]["validity"]==NULL)
                {
                    $results[$k]["registered"]=0;
                }
                if (isset($results[$k]["dateLimit"]))
                {
                    $tmp=$results[$k]["dateLimit"]-time();
                }
                else if (isset($results[$k]["openingDate"]))
                {
                    $tmp=$results[$k]["openingDate"]-time();
                }
                else
                {
                    break;
                }
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
                $this->loadFromInfo($data["id"], $data["idTalent"], $data["name"], $data["description"], $data["place"], $data["date"], $data["dateLimit"], $data["openingDate"], $data["placesLimitMin"], $data["placesLimitMax"], $data["picture"], $data["video"], $data["video_picture"], $data["rules"], $data["city"], $data["ticket"], $data["tags"], $data["brand"]);
            } catch(Exception $ex) {

            } 
        }
        public function loadFromInfo($id, $idTalent, $name, $description, $place, $date, $dateLimit, $openingDate, $placesLimitMin, $placesLimitMax, $picture, $video, $video_picture, $rules, $city, $ticket, $tags, $brand) {
            $this->_id = $id;
            $this->_idTalent = $idTalent;
            $this->_name = $name;
            $this->_description = $description;
            $this->_place = $place;
            $this->_date = $date;
            $this->_dateLimit = $dateLimit;
            $this->_openingDate = $openingDate;
            $this->_placesLimitMin = $placesLimitMin;
            $this->_placesLimitMax = $placesLimitMax;
            $this->_picture = $picture;
            $this->_video = $video;
            $this->_video_picture = $video_picture;
            $this->_rules = $rules;
            $this->_city = $city;
            $this->_ticket = $ticket;
            $this->_tags = $tags;
            $this->_brand = $brand;
        }
        public function getId() {
            return $this->_id;
        }

        public function getIdTalent() {
            return $this->_idTalent;
        }

        public function getName() {
            return $this->_name;
        }

        public function getDescription() {
            return $this->_description;
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

        public function getOpeningDate() {
            return $this->_openingDate;
        }

        public function getPlacesLimitMin() {
            return $this->_placesLimitMin;
        }

        public function getPlacesLimitMax() {
            return $this->_placesLimitMax;
        }

        public function getPicture() {
            return $this->_picture;
        }

        public function getVideo() {
            return $this->_video;
        }

        public function getVideo_picture() {
            return $this->_video_picture;
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

        public function setId($id) {
            $this->_id = $id;
        }

        public function setIdTalent($idTalent) {
            $this->_idTalent = $idTalent;
        }

        public function setName($name) {
            $this->_name = $name;
        }

        public function setDescription($description) {
            $this->_description = $description;
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

        public function setOpeningDate($openingDate) {
            $this->_openingDate = $openingDate;
        }

        public function setPlacesLimitMin($placesLimitMin) {
            $this->_placesLimitMin = $placesLimitMin;
        }

        public function setPlacesLimitMax($placesLimitMax) {
            $this->_placesLimitMax = $placesLimitMax;
        }

        public function setPicture($picture) {
            $this->_picture = $picture;
        }

        public function setVideo($video) {
            $this->_video = $video;
        }

        public function setVideo_picture($video_picture) {
            $this->_video_picture = $video_picture;
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
        
        public function isDone(){
            return ($this->getDate()<time());
        }
        public function isAvailable(){
            return ($this->getDateLimit()>time() && $this->getOpeningDate()<=time());
        }
        public function isNext(){
            return ($this->getOpeningDate()>time());
        }
    }
