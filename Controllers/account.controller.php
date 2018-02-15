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
                if (empty($password)){
                    throw new Exception(LOGIN_FAILED);
                }
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
        protected function twitter(){
            $postdata = file_get_contents("php://input");
            $request = json_decode($postdata);
            $twitteroauth = new Abraham\TwitterOAuth\TwitterOAuth("Nd3omgs4Wptkxk22nkSwciuNa", "CAYrhhchrPPXxTPWhSAzJXjunjKg9HWty5qN5RgWhL8WaXJO6Z");
            if (!isset($request->oauth_verifier))
            {
                $request_token = $twitteroauth->oauth(
                    'oauth/request_token', []
                );
                if($twitteroauth->getLastHttpCode() != 200) {
                    return ["error" => WRONG_HAPPENS];
                }
                return ["oauth_token" => $request_token['oauth_token']];
            }
            $access_token = $twitteroauth->oauth("oauth/access_token", ["oauth_verifier" => $request->oauth_verifier, 'oauth_token'=> $request->oauth_token]);
            $connection = new Abraham\TwitterOAuth\TwitterOAuth(
                "Nd3omgs4Wptkxk22nkSwciuNa",
                "CAYrhhchrPPXxTPWhSAzJXjunjKg9HWty5qN5RgWhL8WaXJO6Z",
                $access_token['oauth_token'],
                $access_token['oauth_token_secret']
            );
            $response=$connection->get("account/verify_credentials");
            if (empty($response))
            {
                return ["error" => WRONG_HAPPENS];
            }
            $tmp=explode(" ", $response->name);
            return $this->loginNetworks("twitter", $response->id, ["picture" => $response->profile_image_url_https, "lastName" => $tmp[count($tmp)-1], "firstName" => $tmp[0]]);
        }
        protected function instagram(){
            $postdata = file_get_contents("php://input");
            $request = json_decode($postdata);
            if (!isset($request->code))
            {
                return ["error" => WRONG_HAPPENS];
            }
            $params = http_build_query([
                'code' => $request->code,
                'grant_type' => "authorization_code",
                'client_id' => "3518b868b8744687bc53917977d3f5ad",
                'redirect_uri' => $request->redirectUri,
                'client_secret' => "169bf216ac474073a8df49b4d95b3ee9"
            ]);
            $opts = array('http' =>
                array(
                    'method'  => 'POST',
                    'header'  => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $params
                )
            );
            $context  = stream_context_create($opts);
            $output = json_decode(file_get_contents('https://api.instagram.com/oauth/access_token', false, $context));
            if (!isset($output->access_token))
            {
                return ["error" => WRONG_HAPPENS];
            }
            $user=$output->user;
            $full_name=$user->full_name;
            $tmp=explode(" ", $full_name);
            return $this->loginNetworks("instagram", $user->id, ["picture" => $user->profile_picture, "lastName" => $tmp[count($tmp)-1], "firstName" => $tmp[0]]);
        }
        protected function facebook(){
            $postdata = file_get_contents("php://input");
            $request = json_decode($postdata);
            if (!isset($request->code))
            {
                return ["error" => WRONG_HAPPENS];
            }
            $params = [
                'code' => $request->code,
                'client_id' => $request->clientId,
                'redirect_uri' => $request->redirectUri,
                'client_secret' => "e927ad362c20c63b8b3b41c553f88bc5"
            ];
            $url="https://graph.facebook.com/v2.5/oauth/access_token?";
            foreach ($params as $k=>$v){
                $url.=$k."=".$v."&";
            }
            $output=json_decode(file_get_contents($url));
            if (!isset($output->access_token))
            {
                return ["error" => WRONG_HAPPENS];
            }
            $fb = new Facebook\Facebook([
                'app_id' => '173145989991407',
                'app_secret' => 'e927ad362c20c63b8b3b41c553f88bc5',
                'default_graph_version' => 'v2.2'
                ]);
            try {
                $response = $fb->get('/me?fields=first_name,last_name,email', $output->access_token);
            } catch(\Facebook\Exceptions\FacebookResponseException $e) {
                return ["error" => $e->getMessage()];
            } catch(\Facebook\Exceptions\FacebookSDKException $e) {
                return ["error" => $e->getMessage()];
            }
            $me = $response->getGraphUser();
            return $this->loginNetworks("facebook", $me->getId(), ["email" => $me["email"], "lastName" => $me["last_name"], "firstName" => $me["first_name"]]);
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
                $time=time()+3600*24*30;
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
                if (method_exists($user, $method) && (!empty($value) || $value==0) && $key!="picture")
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
    