<?php
    class EventController extends Controller {
        public function __construct(){ parent::__construct(); }
        protected function last(){
            $event = new Event();
            return $event->getNextEvent();
        }
        protected function search(){
            $this->hadToBeAuth(true);
            $event = new Event();
            $token = getToken();
            $registration = new Registration();
            $data=$registration->getEventsRecommended($token->id);
            $results=[];
            if (!empty($data))
            {
                $results=$event->getMeetsById(implode(",",$data));
            }
            if (count($data)<4)
            {
                array_unshift($results, ["type" => "whatsapp"]);
                while (count($results)<6)
                {
                    $results[]=["type" => "pub"];
                }
            }
            /**
             * Si un talent estl ié, on peut faire un algo avec ça
             * Algo avec la ville
             * Algo avec les tags... (pour les handicapés par exemple)
             */
            return $results;
        }
    }
    