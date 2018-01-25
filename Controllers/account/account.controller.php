<?php
    class AccountController {
        public function __construct(){ }
        public function login(){
            global $jwtKey;
            global $networks;
            try{
                $email=filter_input(INPUT_POST, "email");
                $password=filter_input(INPUT_POST, "password");
                $account=new Account();
                if ($email)
                {
                    $result=$account->canLogin($email, $password);
                }
                else
                {
                    $network_use=null;
                    foreach ($networks as $network)
                    {
                        $network_use=(filter_input(INPUT_POST, $network))?$network:$network_use;
                    }
                    if (!$network_use)
                    {
                        return ["error" => LOGIN_FAILED];
                    }
                    $result=$account->canLoginNetwork($network_use, filter_input(INPUT_POST, $network_use));
                }
                if ($result<0)
                {
                    return ["error" => LOGIN_FAILED];
                }
                return JWT::encode([
                    'id' => 1,
                    'role' => 'User',
                    'exp' => time() + 3600], $jwtKey);
                //JWT::decode($_POST['token'], $jwtKey);
            } catch (Exception $ex) {
                return ["error" => $ex->getMessage()];
            }
        }
    }
    