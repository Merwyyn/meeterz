<?php
    class TalentController extends Controller {
        public function __construct(){ parent::__construct(); }
        protected function top(){
            $talent = new Talent();
            return $talent->getTop();
        }
        protected function subscribe(){
            $controllerAffinity=new AffinityController();
            return $controllerAffinity->subscribe();
        }
        protected function isSubscribed(){
            $this->hadToBeAuth(true);
            $idUser= getToken()->id;
            $idTalent=filter_input(INPUT_POST, "idTalent");
            $controllerAffinity=new AffinityController();
            return ["isSubcribed" => $controllerAffinity->getAffinity($idUser, $idTalent)->getSubscribed()];
        }
    }
    