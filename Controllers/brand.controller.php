<?php
    class BrandController extends Controller {
        public function __construct(){ parent::__construct(); }
        public function get(){
            $this->hadToBeAuth(true);
            $brand = new Brand();
            return $brand->getAll();
        }
        public function create(){
            try{
                $this->hadToBeAdmin();
                $upload = new Upload($_FILES, PICTURES, getToken()->id."/brand");
                $id=filter_input(INPUT_POST, "idBrand");
                $name=filter_input(INPUT_POST, "name");
                $url=filter_input(INPUT_POST, "url");
                if ($upload->haveErrors() || empty($name) || empty($url))
                {
                    throw new Exception(ERROR_CREATE_BRAND);
                }
                $logo=$upload->getListPath();
                $brand=new Brand($id, $name, $url, $logo);
                return $brand->save();
            } catch (Exception $ex) {
                return ["error" => $ex];
            }
        }
        public function delete(){
            $this->hadToBeAdmin();
            $id=filter_input(INPUT_POST, "idBrand");
            if (empty($id))
            {
                return ["error" => ERROR_CREATE_BRAND];
            }
            $brand=new Brand($id);
            return $brand->delete();
        }
    }
    