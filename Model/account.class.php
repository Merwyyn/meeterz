<?php
    class Account extends Modele {
        private $_id;
        private $_email;
        private $_password;
        private $_lastName;
        private $_firstName;
        private $_access_level;
        private $_birthDate;
        private $_nationality;    
        private $_address;
        private $_city;
        private $_postalCode;
        private $_country;
        private $_cellNumber;
        private $_facebook;
        private $_instagram;
        private $_twitter;
        private $_google;
        private $_howDoYouKnow;
        private $_occupation;
        private $_children;
        private $_picture;
        private $_registrationDate;
        private $_loginTime;
        private $_logoutTime;
        
        const SELECT = 'SELECT * FROM user WHERE id=?';
        const EXIST_LOGIN = 'SELECT id FROM user WHERE email=? AND password=?';
        const EXIST_LOGIN_NETWORK = 'SELECT id FROM user WHERE %NETWORK%=?';
        const EXIST_MAIL = 'SELECT COUNT(*) FROM user WHERE email=?';
        const INSERT = 'INSERT INTO user (email, password, registrationDate) VALUES (?, ?, ?)';
        const UPDATE_LOGIN = 'UPDATE user SET loginTime=? WHERE id=?';
        const UPDATE_LOGOUT = 'UPDATE user SET logoutTime=? WHERE id=?';
        public function __construct($idUser=NULL, $lastName=NULL, $firstName=NULL, $access_level=NULL, $birthDate=NULL, $nationality=NULL, $email=NULL, $password=NULL, $address=NULL, $city=NULL, $postalCode=NULL, $country=NULL, $cellNumber=NULL, $facebook=NULL, $instagram=NULL, $twitter=NULL, $google=NULL, $howDoYouKnow=NULL, $occupation=NULL, $children=NULL, $picture=NULL, $registrationDate = NULL, $loginTime = NULL, $logoutTime = NULL){
            parent::__construct();
            if (func_num_args()==1)
            {
                $this->loadFromDb($idUser);
            }
            else
            {
                $this->loadFromInfo($idUser, $lastName, $firstName, $access_level, $birthDate, $nationality, $email, $password, $address, $city, $postalCode, $country, $cellNumber, $facebook, $instagram, $twitter, $google, $howDoYouKnow, $occupation, $children, $picture, $registrationDate, $loginTime, $logoutTime);
            }
        }
        public function toData(){
            return ["lastName" => $this->_lastName,
                    "firstName" => $this->_firstName,
                    "access_level" => $this->_access_level,
                    "birthDate" => $this->_birthDate,
                    "nationality" => $this->_nationality,
                    "email" => $this->_email,
                    "address" => $this->_address,
                    "city" => $this->_city,
                    "postalCode" => $this->_postalCode,
                    "country" => $this->_country,
                    "cellNumber" => $this->_cellNumber,
                    "facebook" => $this->_facebook,
                    "instagram" => $this->_instagram,
                    "twitter" => $this->_twitter,
                    "google" => $this->_google,
                    "howDoYouKnow" => $this->_howDoYouKnow,
                    "occupation" => $this->_occupation,
                    "children" => $this->_children,
                    "picture" => $this->_picture,
                    "registrationDate" => $this->_registrationDate];
        }
        public function profilComplete(){
            return ($this->_lastName && $this->_firstName && $this->_address && $this->_birthDate && 
                    $this->_city && $this->_postalCode && $this->_country && $this->_cellNumber);
        }
        public function updateLogin($id){
            try{
                $this->execute(self::UPDATE_LOGIN, [time(), $id]);
            } catch (Exception $ex) {
                
            }
        }
        public function updateLogout($id){
            try{
                $this->execute(self::UPDATE_LOGOUT, [time(), $id]);
            } catch (Exception $ex) {
                
            }
        }
        public function create($email, $password){
            try{
                $this->execute(self::INSERT, [$email, cryptPassword($password), time()]);
                return $this->lastInsert();
            } catch (Exception $ex) {
                return -1;
            }
        }
        public function canLogin($email, $password){
            try{
                $req=$this->execute(self::EXIST_LOGIN, [$email, cryptPassword($password)]);
                $data=$req->fetch();
                return (isset($data["id"]))?$data["id"]:-1;
            } catch (Exception $ex) {
                return -1;
            }
        }
        public function canLoginNetwork($network, $token){
            try{
                $req=$this->execute(__(self::EXIST_LOGIN_NETWORK, $network), [$token]);
                $data=$req->fetch();
                return (isset($data["id"]))?$data["id"]:-1;
            } catch (Exception $ex) {
                return -1;
            }
        }
        public function existByMail($email){
            try{
                $req=$this->execute(self::EXIST_MAIL, [$email]);
                return ($req->fetchColumn());
            } catch (Exception $ex) {
                return 1;
            }
        }
        public function loadFromDb($idUser)
        {
            try{
                $req=$this->execute(self::SELECT, [$idUser]);
                $data=$req->fetch();
                if (!isset($data["lastName"]))
                {
                    $this->_errors=true;
                    return;
                }
                $this->loadFromInfo($idUser, $data["lastName"], $data["firstName"], $data["access_level"], $data["birthDate"], $data["nationality"], $data["email"], $data["password"], $data["address"], $data["city"], $data["postalCode"], $data["country"], $data["cellNumber"], $data["facebook"], $data["instagram"], $data["twitter"], $data["google"], $data["howDoYouKnow"], $data["occupation"], $data["children"], $data["picture"], $data["registrationDate"], $data["loginTime"], $data["logoutTime"]);
            }  catch (Exception $ex) {

            }
                
        }
        public function loadFromInfo($idUser, $lastName, $firstName, $access_level, $birthDate, $nationality, $email, $password, $address, $city, $postalCode, $country, $cellNumber, $facebook, $instagram, $twitter, $google, $howDoYouKnow, $occupation, $children, $picture, $registrationDate, $loginTime, $logoutTime) {
            $this->_id = $idUser;
            $this->_lastName = $lastName;
            $this->_firstName = $firstName;
            $this->_access_level = $access_level;
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
            $this->_google = $google;
            $this->_howDoYouKnow = $howDoYouKnow;
            $this->_occupation = $occupation;
            $this->_children = $children;
            $this->_picture = $picture;
            $this->_registrationDate = $registrationDate;
            $this->_loginTime = $loginTime;
            $this->_logoutTime = $logoutTime;
        }
        
        public function getId() {
            return $this->_id;
        }

        public function getLastName() {
            return $this->_lastName;
        }

        public function getFirstName() {
            return $this->_firstName;
        }

        public function getAccessLevel() {
            return $this->_access_level;
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

        public function getGoogle() {
            return $this->_google;
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

        public function setId($idUser) {
            $this->_id = $idUser;
        }

        public function setLastName($lastName) {
            $this->_lastName = $lastName;
        }

        public function setFirstName($firstName) {
            $this->_firstName = $firstName;
        }

        public function setAccessLevel($admin) {
            $this->_access_level = $admin;
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

        public function setGoogle($google) {
            $this->_google = $google;
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