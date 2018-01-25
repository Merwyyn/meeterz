<?php
    class AccountController extends Controller {
        public function __construct(){ parent::__construct(); }
        private function login(){
            hadToBeAuth(false);
            global $jwtKey;
            try{
                $email=filter_input(INPUT_POST, "email");
                $password=filter_input(INPUT_POST, "password");
                $account=new Account();
                $result=($email)?$account->canLogin($email, $password):$this->loginByNetworks($account);
                if ($result<0)
                {
                    throw new Exception(LOGIN_FAILED);
                }
                return ["token" => JWT::encode([
                    'id' => $result,
                    'role' => 'User',
                    'exp' => time() + 3600], $jwtKey)];
            } catch (Exception $ex) {
                return ["error" => $ex->getMessage()];
            }
        }
        private function loginByNetworks($account){
            $this->hadToBeAuth(false);
            global $networks;
            $network_use=null;
            foreach ($networks as $network)
            {
                $network_use=(filter_input(INPUT_POST, $network))?$network:$network_use;
            }
            if (!$network_use)
            {
                throw new Exception(LOGIN_FAILED);
            }
            return $account->canLoginNetwork($network_use, filter_input(INPUT_POST, $network_use));
        }
        private function register(){
            hadToBeAuth(false);
            try{
                $email=trim(filter_input(INPUT_POST, "email"));
                $password=filter_input(INPUT_POST, "password");
                $account=new Account();
                if (!isEmail($email))
                {
                    throw new Exception(NOT_AN_EMAIL);
                }
                if (strlen($password)<6)
                {
                    throw new Exception(PASSWORD_LENGTH);
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
                return login();
            } catch (Exception $ex) {
                return ["error" => $ex->getMessage()];
            }
        }
    }
    