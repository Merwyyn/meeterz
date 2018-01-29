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
    }
    