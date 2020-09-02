<?php
require 'vendor/autoload.php';
use GuzzleHttp\Client;


class IntegrationAPIException extends Exception
{
  
    public function __construct($message='Some exception', $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}


class IntegrationAPI
{
	private $client=NULL;
	private $token='';
	
	
	
	function __construct(string $base_uri,float $timeout=10.0, $handler=NULL)
	{
		
		$this->client = new Client([
		'base_uri'=>$base_uri,
		'timeout'=>$timeout,
		'handler'=>$handler
		]);	
	}
	
	function getToken()
	{
		return $this->token;
	}
	
	function setToken($t)
	{
		$this->token=$t;
	}
	
	function authorize(string $login,string $password)
	{
		if (empty($login))
			throw new InvalidArgumentException('login empty');
		
		$res = $this->client->request('GET', '/auth', [
			'query' => ['login'=>$login, 'pass'=>$password]]);
		

		
		$json=$res->getBody();
		
		$data=json_decode($json,TRUE);
		
		$status=$data['status'];
		
		if ($status=='OK')
		{
			if (!isset($data['token']))
				throw new IntegrationAPIException('authorize token error');
				
			$token=$data['token'];
			
			if (!is_string($token) || empty($token))
				throw new IntegrationAPIException('authorize token error');
			
			
			$this->token=$token;
			
			return $data;
			
		} else 
			throw new IntegrationAPIException('authorize Status '.$status);
		
		
		
		
		
	}
	
	function getuser(string $username)
	{
		if (empty($username))
			throw new InvalidArgumentException('username empty');
		
		$urlencoded_username=rawurldecode($username);
		$res = $this->client->request('GET', '/get-user/'.$urlencoded_username, [
			'query' => ['token'=>$this->token]]);


		
		$json=$res->getBody();
		
		$data=json_decode($json,TRUE);
		
		$status=$data['status'];
		
		if ($status=='OK')
		{
			
			return $data;
			
		} else 
			throw new IntegrationAPIException('getuser Status '.$status);
		
		
	}
	
	function update(string $username,$userdata)
	{
		$urlencoded_username=rawurldecode($username);
		$res = $this->client->request('POST', '/user/'.$urlencoded_username.'/update', [
			'query' => ['token'=>$this->token], 'json'=>$userdata ]);

		
		$json=$res->getBody();
		
		$data=json_decode($json,TRUE);
		
		$status=$data['status'];
		
		if ($status=='OK')
		{
			
			return $data;
			
		} else
			throw new IntegrationAPIException('update Status '.$status);
		
		
	}
	

}





