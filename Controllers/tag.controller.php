<?php
    class TagController extends Controller {
        public function __construct(){ parent::__construct(); }
        public function get(){
            $this->hadToBeAuth(true);
            $tag = new Tag();
            return $tag->getAll();
        }
        public function create(){
            try{
                $this->hadToBeAdmin();
                $upload = new Upload($_FILES, PICTURES, getToken()->id."/tag");
                $id=filter_input(INPUT_POST, "idTag");
                $name=filter_input(INPUT_POST, "name");
                $description=filter_input(INPUT_POST, "description");
                if ($upload->haveErrors() || empty($name) || empty($description))
                {
                    throw new Exception(ERROR_CREATE_TAG);
                }
                $icon=$upload->getListPath();
                $tag=new Tag($id, $icon, $description, $name);
                return $tag->save();
            } catch (Exception $ex) {
                return ["error" => $ex];
            }
        }
        public function delete(){
            $this->hadToBeAdmin();
            $id=filter_input(INPUT_POST, "idTag");
            if (empty($id))
            {
                return ["error" => WRONG_HAPPENS];
            }
            $tag=new Tag($id);
            return $tag->delete();
        }
    }
    