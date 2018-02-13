<?php
    class AccountController extends Controller {
        public function __construct(){ parent::__construct(); }
        protected function login(){
            $this->hadToBeAuth(false);
            global $jwtKey;
            try{
                $email=filter_input(INPUT_POST, "email");
                $password=filter_input(INPUT_POST, "password");
                $stayLog=filter_input(INPUT_POST, "stayConnected");
                $account=new Account();
                $result=$account->canLogin($email, $password);
                if ($result<0)
                {
                    throw new Exception(LOGIN_FAILED);
                }
                $account->updateLogin($result);
                $time=time()+3600;
                if ($stayLog==true)
                {
                    $time+=30*24*3600;
                }
                $token=JWT::encode([
                    'id' => $result,
                    'exp' => $time], $jwtKey);
                return ["token" => $token];
            } catch (Exception $ex) {
                return ["error" => $ex->getMessage()];
            }
        }
        protected function google(){
            global $base_web;
            $postdata = file_get_contents("php://input");
            $request = json_decode($postdata);
            if (!isset($request->code))
            {
                return ["error" => WRONG_HAPPENS];
            }
            $client = new Google_Client();
            $client->setAuthConfigFile($base_web.'Core/google/client_secrets.json');
            $client->setIncludeGrantedScopes(true);
            $client->setAccessToken($client->fetchAccessTokenWithAuthCode($request->code));
            if (!$client->getAccessToken())
            {
                return ["error" => WRONG_HAPPENS];
            }
            if (!($ticket = $client->verifyIdToken()))
            {
                return ["error" => WRONG_HAPPENS];
            }
            $email=$ticket["email"];
            $last_name=$ticket["family_name"];
            $first_name=$ticket["given_name"];
            $google_id=$ticket["sub"];
            return $this->loginNetworks("google", $google_id, ["email" => $email, "lastName" => $last_name, "firstName" => $first_name]);
        }
        protected function loginNetworks($network, $code, $data=[]){
            $this->hadToBeAuth(false);
            global $jwtKey;
            try{
                $account=new Account();
                $result=$account->canLoginNetwork($network, $code);
                if ($result<0)
                {
                    $result=$account->createFrom($network, $code);
                    if ($result<0)
                    {
                        throw new Exception($result);
                    }
                    $account=new Account($result);
                    $account->updateFromData($data);
                    if (!empty($data)){ 
                        $account->save();
                    }
                    return $this->loginNetworks($network, $code);
                }
                $account->updateLogin($result);
                $time=time()+3600;
                $token=JWT::encode([
                    'id' => $result,
                    'exp' => $time], $jwtKey);
                return ["token" => $token];
            } catch (Exception $ex) {
                return ["error" => $ex->getMessage()];
            }
        }
        protected function register(){
            $this->hadToBeAuth(false);
            try{
                $email=trim(filter_input(INPUT_POST, "email"));
                $password=filter_input(INPUT_POST, "password");
                $password2=filter_input(INPUT_POST, "passwordConfirm");
                $account=new Account();
                if (!isEmail($email))
                {
                    throw new Exception(NOT_AN_EMAIL);
                }
                if (strlen($password)<6)
                {
                    throw new Exception(PASSWORD_LENGTH);
                }
                if ($password!=$password2)
                {
                    throw new Exception(PASSWORD_NOT_MATCH);
                }
                if (strlen($email)>255)
                {
                    throw new Exception(EMAIL_LENGTH);
                }
                if ($account->existByMail($email))
                {
                    throw new Exception(EMAIL_EXIST);
                }
                $result=$account->create($email, $password);
                if ($result<0)
                {
                    throw new Exception(REGISTER_FAILED);
                }
                return $this->login();
            } catch (Exception $ex) {
                return ["error" => $ex->getMessage()];
            }
        }
        protected function logout(){
            $this->hadToBeAuth(true);
            $token = getToken();
            $account = new Account();
            $account->updateLogout($token->id);
            return [];
        }
        protected function get(){
            $this->hadToBeAuth(true);
            $account = new Account(getToken()->id);
            return $account->toData();
        }
        protected function meetHistory(){
            $this->hadToBeAuth(true);
            $token = getToken();
            $registration = new Registration();
            $event = new Event();
            $results=[];
            $results["nbmeets"]=$registration->getCountParticipateMonthByUser($token->id);
            $results["nbmeetsRegistered"]=$registration->getCountRegisteredByUser($token->id);
            $results["nbmeetsDone"]=$registration->getCountDoneByUser($token->id);
            $results["meets"]=$event->getEventTodayByUser($token->id);
            $results["meetstoday"]=count($results["meets"]);
            return $results;
        }
        protected function update(){
            $this->hadToBeAuth(true);
            $token = getToken();
            $upload = new Upload($_FILES, PICTURES, getToken()->id."/user");
            $user=new Account($token->id);
            foreach ($_POST as $key => $value)
            {
                $method="set".ucfirst($key);
                if (method_exists($user, $method) && !empty($value))
                {
                    $user->$method ($value);
                }
            }
            if ($upload->getCount()>0){
                $user->setPicture($upload->getPath());
            }
            return $user->save();
        }
    }
    