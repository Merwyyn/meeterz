<?php
    class EventController extends Controller {
        public function __construct(){ parent::__construct(); }
        protected function last(){
            $event = new Event();
            return $event->getNextEvent();
        }
    }
    