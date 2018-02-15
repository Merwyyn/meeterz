<?php
    class EventController extends Controller {
        public function __construct(){ parent::__construct(); }
        protected function last(){
            $event = new Event();
            return $event->getNextEvent();
        }
        protected function register(){
            $this->hadToBeAuth(true);
            $id = filter_input(INPUT_POST, "id");
            $event = new Event($id);
            $user = new Account(getToken()->id);
            if ($event->haveErrors())
            {
                return ["error" => EVENT_NOT_EXIST];
            }
            $user_valid=$user->profilComplete();
            $registration=new Registration($user->getId(), $id, $user_valid, time(), false);
            $registration->save();
            return ["valid" => $user_valid];
        }
        protected function getByCity(){
            $this->hadToBeAuth(true);
            $city = filter_input(INPUT_POST, "city");
            $idPost = filter_input(INPUT_POST, "id");
            if (!$city)
            {
                return [];
            }
            $event = new Event();
            $ids=$event->getIdByCity($city);
            $results=[];
            foreach ($ids as $id)
            {
                if ($idPost && $idPost==$id["id"])
                {
                    continue;
                }
                $results[]=$this->get($id["id"]);
            }
            return $results;
        }
        protected function get($idForced=NULL){
            $this->hadToBeAuth(true);
            $id = ($idForced)?$idForced:filter_input(INPUT_POST, "id");
            $event = new Event($id);
            if ($event->haveErrors())
            {
                return ["error" => EVENT_NOT_EXIST];
            }
            $registration=new Registration();
            $result=$event->toData();
            $result["registered"]=$registration->getCountRegisteredByEventId($id);
            $tmp=explode(",", $result["video"]);
            $result["video"]=["webm" => $tmp[0], "mp4" => $tmp[1]];
            $tmp=explode(",", $result["tags"]);
            $result["tags"]=[];
            foreach ($tmp as $idTag){
                $tag=new Tag($idTag);
                if ($tag->haveErrors())
                {
                    continue;
                }
                $result["tags"][]=["description" => $tag->getDescription(), "icon" => $tag->getIcon()];
            }
            $result["date_string"]=date("d", $result["date"])." ".getMonthString(date("m", $result["date"]))." ".date("Y", $result["date"])." à ".date("H:i", $result["date"]);
            $result["openingDate_string"]=date("d", $result["openingDate"])." ".getMonthString(date("m", $result["openingDate"]))." ".date("Y", $result["openingDate"])." à ".date("H:i", $result["openingDate"]);
            $registration=new Registration(getToken()->id, $result["id"]);
            $result["user"]=["register" => !$registration->haveErrors(), "validity" => (!$registration->haveErrors() && $registration->getValidity()), "participate" => (!$registration->haveErrors() && $registration->getParticipation())];
            return $result;
        }
        protected function search(){
            $affinityController = new AffinityController();
            $this->hadToBeAuth(true);
            $search = filter_input(INPUT_POST, "search");
            $city_form = filter_input(INPUT_POST, "city", FILTER_DEFAULT , FILTER_REQUIRE_ARRAY);
            $domain_form = filter_input(INPUT_POST, "domain", FILTER_DEFAULT , FILTER_REQUIRE_ARRAY);
            $talent_form = filter_input(INPUT_POST, "talent", FILTER_DEFAULT , FILTER_REQUIRE_ARRAY);
            $range_form = filter_input(INPUT_POST, "range");
            $lat = filter_input(INPUT_POST, "lat");
            $long = filter_input(INPUT_POST, "long");
            $date_form = filter_input(INPUT_POST, "date");
            if (!empty($date_form)){
                switch ($date_form){
                    case 1:
                    case 3:
                    case 6:
                    case 12:
                        $m=date("m")+$date_form;
                        $Y=date("Y");
                        if ($m>12){ $m-=12; $Y++; }
                        $time_form=strtotime("01-".$m."-".$Y)-1;
                        break;
                    default:break;
                }
            }
            if (!empty($talent_form)){
                foreach ($talent_form as $talent_id){
                    $affinityController->searched($talent_id);
                }
            }
            $event = new Event();
            $token= getToken();
            $registration = new Registration();
            $data=$registration->getEventsRecommended($token->id);
            $results=[];
            if (!empty($data))
            {
                $results_tmp=$event->getMeetsById(implode(",",$data));
                foreach ($results_tmp as $k => $v)
                {
                    $talent = new Talent($results_tmp[$k]["id"]);
                    if (!empty($city_form) && !in_array($results_tmp[$k]["city"], $city_form))
                    {
                        continue;
                    }
                    if (!empty($domain_form) && !in_array($talent->getOccupation(), $domain_form))
                    {
                        continue;
                    }
                    if (!empty($talent_form) && !in_array($results_tmp[$k]["id"], $talent_form))
                    {
                        continue;
                    }
                    if (isset($time_form) && $time_form<$results_tmp[$k]["date"])
                    {
                        continue;
                    }
                    if (!empty($range_form) && !empty($lat) && !empty($long))
                    {
                        if (!is_array($LatLong=getLatLong($results_tmp[$k]["city"]." ".$results_tmp[$k]["country"])) ||
                        calculDistance($LatLong[0], $LatLong[1], $lat, $long)>$range_form*1000)
                        {
                            continue;
                        }
                    }
                    $results_tmp[$k]["type"]="meet";
                    if (!empty($search) && (strstr($results_tmp[$k]["city"], $search) || strstr($results_tmp[$k]["name"], $search)))
                    {
                        $results[]=$results_tmp[$k];
                    }
                    else if (!empty($search))
                    {
                        if (strstr($talent->getLastName(), $search) || strstr($talent->getFirstName(), $search))
                        {
                            $results[]=$results_tmp[$k];
                        }
                    }
                    else if (empty($search))
                    {
                        $results[]=$results_tmp[$k];
                    }
                }
            }
            if (count($data)<6)
            {
                array_unshift($results, ["type" => "whatsapp"]);
                while (count($results)<6)
                {
                    $results[]=["type" => "pub"];
                }
            }
            /**
             * Algo avec le talent...
             * Algo avec la ville...
             * Algo avec les tags... (pour les handicapés par exemple)
             */
            return $results;
        }
        protected function dataForm(){
            $this->hadToBeAuth(true);
            $event = new Event();
            return $event->getDataForm();
        }
        protected function headerEvent(){
            $this->hadToBeAuth(true);
            $event = new Event();
            $registration = new Registration();
            return ["meetDone" => $event->getCountMeetsDone(), 
                "meetsAvailable" => $event->getCountMeetsAvailable(),
                "nextMeets" => $event->getCountNextMeets(),
                "register" => $registration->getCountRegistered(),
                "participate" => $registration->getCountParticipate()];
        }
        protected function selectForm(){
            $this->hadToBeAuth(true);
            $event = new Event();
            $data=$event->getDataForm();
            $result=[];
            foreach ($data["city"] as $city)
            {
                $result[]=["value" => $city["city"], "text" => $city["city"]];
            }
            foreach ($data["talent"] as $talent)
            {
                $result[]=["value" => $talent["id"], "text" => $talent["firstName"]." ".$talent["lastName"]];
            }
            return $result;
        }
        protected function programmation(){
            $this->hadToBeAuth(true);
            $event = new Event();
            $token = getToken();
            return $event->getEventByUserId($token->id);
        }
        protected function getNext(){
            $this->hadToBeAuth(true);
            $event = new Event();
            return $event->getNextEvents();
        }
        protected function getPrevious(){
            $this->hadToBeAuth(true);
            $event = new Event();
            return $event->getPreviousEvents();
        }
    }
    