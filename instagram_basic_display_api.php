<?php
	require_once('defines.php');
	
	Class instagram_basic_display_api{
		private $_appId = INSTAGRAM_APP_ID;
		private $_appSecret = INSTAGRAM_APP_SECRET;
		private $_redirectUrl = INSTAGRAM_APP_REDIRECT_URL;
		private $_getCode='';
		private $_apiBaseUrl = 'https://api.instagram.com/';
		private $_graphBaseUrl = 'https://graph.instagram.com/';
		private $_userAccessToken = '';
		private $_userAccessTokenExpires = '';
		
		
		public $authorizationUrl ='';
		public $hasUserAccessToken = false;
		
		function __construct($params){
			// save instagram code
			$this ->_getCode = $params['getCode'];
			// get an access token
			$this ->_setUserInstagramAccessToken($params);
			// get authorization url
			$this->_setAuthorizationUrl();
			
		}
		public function getUserAccessToken(){
			return $this->_userAccessToken;
		}
		
		public function getUserAccessTokenExpires(){
			return $this->_userAccessTokenExpires;
		}
		
		private function _setAuthorizationUrl(){
			$getVars = array(
				'app_id' => $this->_appId,
				'redirect_uri' => $this->_redirectUrl,
				'scope' => 'user_profile,user_media',
				'response_type' => 'code'
			);
			
			//create url
			$this -> authorizationUrl = $this->_apiBaseUrl . 'oauth/authorize?' . http_build_query($getVars);
		
		}
	
		private function _setUserInstagramAccessToken($params){
			if($params['access_token']){ //we have an access token
				$this->_userAccessToken = $params['access_token'];
				$this->hasUserAccessToken=true;
			}elseif($params['get_code']){ // try and get an access token
			$userAccessTokenResponse = $this-> _getUserAccessToken();
			$this->_userAccessToken = $userAccessTokenResponse['access_token'];
			$this-> hasUserAccessToken = true;
			
			// get long lived access token
			$longLivedAccessTokenResponse = $this->_getLongLivedAccessToken
			$this->_userAccessToken = $longLivedAccessTokenResponse['access_token'];
			$this->_userAccessTokenExpires = $longLivedAccessTokenResponse['expires_in'];
		
			}
		}
	
		private function _getUserAccessToken(){
			$params = array(
				'endpoint_url' => $this->_apiBaseUrl . 'oauth/access_token',
				'type' => 'POST'
				'url_params'=> array(
					'app_id'=> $this->app_id,
					'app_secret' => $this->_appSecret,
					'grant_type' => 'authorization_code',
					'redirect_uri' => $this->_redirectUrl,
					'code' => $this->_getCode
				)
			);
			
			$response = $this->_makeApiCall($params);
			return $response;
		}
		private function _getLongLivedAccessToken(){
			$params = array(
				'endpoint_url' => $this->_graphBaseUrl . 'access_token',
				'type' => 'GET'
				'url_params'=> array(
					'client_secret' => $this->_appSecret,
					'grant_type' => 'ig_exchange_token',
				)
			);
			
			$response = $this->_makeApiCall($params);
			return $response;
		}
		
		public function getUser() {
			$params = array(
				'endpoint_url' => $this->_graphBaseUrl . 'me',
				'type' => 'GET'
				'url_params'=> array(
					'fields' => 'id,username,media_count,acccount_type',
				)
			);
			
			$response = $this->_makeApiCall($params);
			return $response;
		}
		
		private function _makeApiCall($params){
			$ch = curl_init();
			
			$endpoint = $parms['endpoint_url];
			
			if ('POST' == $params['type']){ //Post request
				curl_setopt( $ch, CURLOPT_POSTP_POSTFIELDS, http_build_query($params['url_params]));
				curl_setopt( $ch, CURLOPT_POST, 1);
			} elseif ('GET' == $params['type]){ //get request
				$params['url_params']['access_token'] = $this->_userAccessToken;
				
				//add params to endpoint
				$endpoint .= '?' . http_build_query ( $params['url_params']);
			}
			
			// general curl options
			curl_setopt( $ch, CURLOPT_URL, $endpoint);
			
			curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
			
			$response = curl_exec( $ch);
			
			curl_close( $ch);
			
			$responseArray = json_decode ( $response, true);
			
			if ( isset($responseArray['error_type'])){
				'var_dump( $responseArray);
			} else {
				return $responseArray;
			}
			
		}
	}