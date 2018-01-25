<?php
    class Account extends Modele {
        private $_idUser;
        private $_lastName;
        private $_firstName;
        private $_admin;
        private $_birthDate;
        private $_nationality;
        private $_email;
        private $_password;
        private $_address;
        private $_city;
        private $_postalCode;
        private $_country;
        private $_cellNumber;
        private $_facebook;
        private $_instagram;
        private $_twitter;
        private $_googlep;
        private $_howDoYouKnow;
        private $_occupation;
        private $_children;
        private $_picture;
        private $_registrationDate;
        
        const SELECT = 'SELECT * FROM user WHERE idUser=?';
        const EXIST_LOGIN = 'SELECT idUser FROM user WHERE email=? AND password=?';
        const EXIST_LOGIN_NETWORK = 'SELECT idUser FROM user WHERE %NETWORK%=?';
        public function __construct($idUser=NULL, $lastName=NULL, $firstName=NULL, $admin=NULL, $birthDate=NULL, $nationality=NULL, $email=NULL, $password=NULL, $address=NULL, $city=NULL, $postalCode=NULL, $country=NULL, $cellNumber=NULL, $facebook=NULL, $instagram=NULL, $twitter=NULL, $googlep=NULL, $howDoYouKnow=NULL, $occupation=NULL, $children=NULL, $picture=NULL, $registrationDate = NULL){
            parent::__construct();
            if (func_num_args()==1)
            {
                $this->loadFromDb($idUser);
            }
            else
            {
                $this->loadFromInfo($idUser, $lastName, $firstName, $admin, $birthDate, $nationality, $email, $password, $address, $city, $postalCode, $country, $cellNumber, $facebook, $instagram, $twitter, $googlep, $howDoYouKnow, $occupation, $children, $picture, $registrationDate);
            }
        }
        public function canLogin($email, $password){
            $req=$this->execute(self::EXIST_LOGIN, [$email, cryptPassword($password)]);
            $data=$req->fetch();
            return (isset($data["idUser"]))?$data["idUser"]:-1;
        }
        public function canLoginNetwork($network, $token){
            $req=$this->execute(__(self::EXIST_LOGIN_NETWORK, $network), [$token]);
            $data=$req->fetch();
            return (isset($data["idUser"]))?$data["idUser"]:-1;
        }
        public function loadFromDb($idUser)
        {
            $req=$this->execute(self::SELECT, [$idUser]);
            $data=$req->fetch();
            if (!isset($data["lastName"]))
            {
                $this->_errors=true;
                return;
            }
            $this->loadFromInfo($idUser, $data["lastName"], $data["firstName"], $data["admin"], $data["birthDate"], $data["nationality"], $data["email"], $data["password"], $data["address"], $data["city"], $data["postalCode"], $data["country"], $data["cellNumber"], $data["facebook"], $data["instagram"], $data["twitter"], $data["googlep"], $data["howDoYouKnow"], $data["occupation"], $data["children"], $data["picture"], $data["registrationDate"]);
        }
        public function loadFromInfo($idUser, $lastName, $firstName, $admin, $birthDate, $nationality, $email, $password, $address, $city, $postalCode, $country, $cellNumber, $facebook, $instagram, $twitter, $googlep, $howDoYouKnow, $occupation, $children, $picture, $registrationDate) {
            $this->_idUser = $idUser;
            $this->_lastName = $lastName;
            $this->_firstName = $firstName;
            $this->_admin = $admin;
            $this->_birthDate = $birthDate;
            $this->_nationality = $nationality;
            $this->_email = $email;
            $this->_password = $password;
            $this->_address = $address;
            $this->_city = $city;
            $this->_postalCode = $postalCode;
            $this->_country = $country;
            $this->_cellNumber = $cellNumber;
            $this->_facebook = $facebook;
            $this->_instagram = $instagram;
            $this->_twitter = $twitter;
            $this->_googlep = $googlep;
            $this->_howDoYouKnow = $howDoYouKnow;
            $this->_occupation = $occupation;
            $this->_children = $children;
            $this->_picture = $picture;
            $this->_registrationDate = $registrationDate;
        }
        
        public function getIdUser() {
            return $this->_idUser;
        }

        public function getLastName() {
            return $this->_lastName;
        }

        public function getFirstName() {
            return $this->_firstName;
        }

        public function getAdmin() {
            return $this->_admin;
        }

        public function getBirthDate() {
            return $this->_birthDate;
        }

        public function getNationality() {
            return $this->_nationality;
        }

        public function getEmail() {
            return $this->_email;
        }

        public function getAddress() {
            return $this->_address;
        }

        public function getCity() {
            return $this->_city;
        }

        public function getPostalCode() {
            return $this->_postalCode;
        }

        public function getCountry() {
            return $this->_country;
        }

        public function getCellNumber() {
            return $this->_cellNumber;
        }

        public function getFacebook() {
            return $this->_facebook;
        }

        public function getInstagram() {
            return $this->_instagram;
        }

        public function getTwitter() {
            return $this->_twitter;
        }

        public function getGooglep() {
            return $this->_googlep;
        }

        public function getHowDoYouKnow() {
            return $this->_howDoYouKnow;
        }

        public function getOccupation() {
            return $this->_occupation;
        }

        public function getChildren() {
            return $this->_children;
        }

        public function getPicture() {
            return $this->_picture;
        }

        public function getRegistrationDate() {
            return $this->_registrationDate;
        }

        public function setIdUser($idUser) {
            $this->_idUser = $idUser;
        }

        public function setLastName($lastName) {
            $this->_lastName = $lastName;
        }

        public function setFirstName($firstName) {
            $this->_firstName = $firstName;
        }

        public function setAdmin($admin) {
            $this->_admin = $admin;
        }

        public function setBirthDate($birthDate) {
            $this->_birthDate = $birthDate;
        }

        public function setNationality($nationality) {
            $this->_nationality = $nationality;
        }

        public function setEmail($email) {
            $this->_email = $email;
        }

        public function setAddress($address) {
            $this->_address = $address;
        }

        public function setCity($city) {
            $this->_city = $city;
        }

        public function setPostalCode($postalCode) {
            $this->_postalCode = $postalCode;
        }

        public function setCountry($country) {
            $this->_country = $country;
        }

        public function setCellNumber($cellNumber) {
            $this->_cellNumber = $cellNumber;
        }

        public function setFacebook($facebook) {
            $this->_facebook = $facebook;
        }

        public function setInstagram($instagram) {
            $this->_instagram = $instagram;
        }

        public function setTwitter($twitter) {
            $this->_twitter = $twitter;
        }

        public function setGooglep($googlep) {
            $this->_googlep = $googlep;
        }

        public function setHowDoYouKnow($howDoYouKnow) {
            $this->_howDoYouKnow = $howDoYouKnow;
        }

        public function setOccupation($occupation) {
            $this->_occupation = $occupation;
        }

        public function setChildren($children) {
            $this->_children = $children;
        }

        public function setPicture($picture) {
            $this->_picture = $picture;
        }

        public function setRegistrationDate($registrationDate) {
            $this->_registrationDate = $registrationDate;
        }   
        
        public function getPassword() {
            return $this->_password;
        }

        public function setPassword($password) {
            $this->_password = $password;
        }


    }