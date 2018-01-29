<?php
    class AffinityController extends Controller {
        public function __construct(){ parent::__construct(); }
        public function getAffinity($idUser, $idTalent){
            $affinity=new Affinity($idUser, $idTalent);
            if ($affinity->haveErrors())
            {
                $affinity = new Affinity($idUser, $idTalent, 0, false, false, false, false, false);
            }
            return $affinity;
        }
        public function subscribe(){
            $this->hadToBeAuth(true);
            $idTalent=filter_input(INPUT_POST, "talent");
            if (!$idTalent)
            {
                return [];
            }
            $token = getToken();
            $affinity=$this->getAffinity($token->id, $idTalent);
            $affinity->setSubscribed(!$affinity->getSubscribed());
            $affinity->save();
            return [];
        }
        public function searched(){
            $this->hadToBeAuth(true);
            $idTalent=filter_input(INPUT_POST, "talent");
            if (!$idTalent)
            {
                return [];
            }
            $token = getToken();
            $affinity=$this->getAffinity($token->id, $idTalent);
            $affinity->setSearched($affinity->getSearched()+1);
            $affinity->save();
            return [];
        }
    }
    