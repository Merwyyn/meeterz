<?php
    class TalentController extends Controller {
        public function __construct(){ parent::__construct(); }
        protected function top(){
            $talent = new Talent();
            return $talent->getTop();
        }
    }
    