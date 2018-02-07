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
            $google_id=NULL;
            $client = new Google_Client();
            $client->setApplicationName("Backend_Meeterz");
            $client->setDeveloperKey("SERVER_KEY");
            $client->authenticate(filter_input(INPUT_POST, "code"));
            $access_token = $client->getAccessToken();
            $ticket = $client->verifyIdToken($access_token);
            if ($ticket) {
              $data = $ticket->getAttributes();
              $google_id=$data['payload']['sub']; // user ID
            }
            return $this->loginNetworks("google", $google_id);
        }
        protected function loginNetworks($network, $code){
            $this->hadToBeAuth(false);
            global $jwtKey;
            try{
                $account=new Account();
                $result=$account->canLoginNetwork($network, $code);
                if ($result<0)
                {
                    $account->createFrom($network, $code);
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
            $account = new Account(getToken()->id);
            return $account->toData();
        }
    }
    