<?php 
require 'vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Middleware;

require 'api.php';
echo "unit tests\r\n";
echo "zend.assertions=".ini_get('zend.assertions')."\r\n";
ini_set('zend.assertions',1);

function assert_failure($file, $line, $assertion, $message)
{
    echo "Проверка $assertion в $file на строке $line провалена: $message"."\r\n";
}



// настройки проверки
assert_options(ASSERT_ACTIVE,   true);
assert_options(ASSERT_BAIL,     false);
assert_options(ASSERT_WARNING,  false);
assert_options(ASSERT_CALLBACK, 'assert_failure');




{// authorize OK test
	echo "authorize OK test\r\n";
	
	$container = [];
	$history = Middleware::history($container);
	
	$mock = new MockHandler([
		new Response(200, [],
		'{
			"status": "OK",
			"token": "dsfd79843r32d1d3dx23d32d"
		}
		')]);
	
	
	$handlerStack = HandlerStack::create($mock);
	$handlerStack->push($history);
	try 
	{
		$api=new IntegrationAPI('http://testapi.ru', 10.0,$handlerStack);

		$auth_result=$api->authorize('test','12345');
		$sample=["status"=> "OK",
			"token"=> "dsfd79843r32d1d3dx23d32d"];
			
		
		
		
		$json_result=json_encode($auth_result);
		$json_sample=json_encode($sample);
		assert($json_result==$json_sample);
		
		assert($api->getToken()=="dsfd79843r32d1d3dx23d32d");

			
		assert(count($container)==1);
		$t=$container[0];
		assert($t["request"]->getMethod()=="GET");
	
		assert($t["request"]->getUri()=="http://testapi.ru/auth?login=test&pass=12345");
	}

	catch (Exception $e)
	{
		assert(FALSE);

	}
}


{ //authorize   incorrect response (empty token) test
	echo "authorize   incorrect response (empty token) test\r\n";
	$mock = new MockHandler([
		new Response(200, [],
		'{
			"status": "OK",
			"token": ""
		}
		')]);
	$handlerStack = HandlerStack::create($mock);		
	try 
	{
		$api=new IntegrationAPI('http://testapi.ru',10.0, $handlerStack);

		$auth_result=$api->authorize('test','12345');
		assert(FALSE);
			
			
	}
	catch (IntegrationAPIException $a)
	{
		assert(TRUE);
	}	
	catch (Exception $e)
	{
		assert(FALSE);
	}
}




	

{//authorize   incorrect response (no token) test
	echo "authorize   incorrect response (no token) test\r\n";
	$mock = new MockHandler([	
		new Response(200, [],
		'{
			"status": "OK"
		}
		')]);
	$handlerStack = HandlerStack::create($mock);		
	try 
	{
		$api=new IntegrationAPI('http://testapi.ru', 10.0,$handlerStack);

		$auth_result=$api->authorize('test','12345');
		assert(FALSE);
			
			
	}
	catch (IntegrationAPIException $a)
	{
		assert(TRUE);
	}	
	catch (Exception $e)
	{
		assert(FALSE);
	}
}



	



	

	
	

{ //authorize status:Error test
	echo "authorize status:Error test\r\n";
	$mock = new MockHandler([	
		new Response(200, [],
		'{
			"status": "Error",		
		}
		')]);
	$handlerStack = HandlerStack::create($mock);		
	try 
	{
		$api=new IntegrationAPI('http://testapi.ru', 10.0,$handlerStack);

		$auth_result=$api->authorize('test','12345');
		assert(FALSE);
			
			
	}
	catch (IntegrationAPIException $a)
	{
		assert(TRUE);
	}	
	catch (Exception $e)
	{
		assert(FALSE);
	}
}
	
	


{  //authorize   404 test
	echo "authorize   404 test\r\n";
	$mock = new MockHandler([	
		new Response(404, [],'Error 404')]);
	$handlerStack = HandlerStack::create($mock);		
	try 
	{
		$api=new IntegrationAPI('http://testapi.ru', 10.0,$handlerStack);

		$auth_result=$api->authorize('test','12345');
		assert(FALSE);
			
			
	}
	catch (IntegrationAPIException $a)
	{
		assert(FALSE);
	}	
	catch (Exception $e)
	{
		assert(TRUE);
	}
}
	
	


{  //authorize   500 test
	echo "authorize   500 test\r\n";
	$mock = new MockHandler([	
		new Response(404, [],'Error 500')]);


	$handlerStack = HandlerStack::create($mock);		
	try 
	{
		$api=new IntegrationAPI('http://testapi.ru', 10.0,$handlerStack);

		$auth_result=$api->authorize('test','12345');
		assert(FALSE);
			
			
	}
	catch (IntegrationAPIException $a)
	{
		assert(FALSE);
	}	
	catch (Exception $e)
	{
		assert(TRUE);
	}
}







{//get userdata  OK test
	echo "get userdata OK test\r\n";
	$mock = new MockHandler([
		new Response(200, [],
		'{
			"status": "OK",
			"active": "1",
			"blocked": false,
			"created_at": 1587457590,
			"id": 23,
			"name": "Ivanov Ivan",
			"permissions": [
				{
					"id": 1,
					"permission": "comment"
				},
				{
					"id": 2,
					"permission": "upload photo"
				},
				{
					"id": 3,
					"permission": "add event"
				}
			]
		}

		')]);
		
	$container = [];
	$history = Middleware::history($container);
		
	$handlerStack = HandlerStack::create($mock);
	$handlerStack->push($history);
	
	try 
	{
		$api=new IntegrationAPI('http://testapi.ru', 10.0,$handlerStack);

		$mytoken="bce7t8dg3d";
		
		$api->setToken($mytoken);
		
		$result=$api->getuser('ivanov');
		

		$sample=[
			'status' => 'OK',
			'active' => '1',
			'blocked' => FALSE,
			'created_at' => 1587457590,
			'id' => 23,
			'name' => 'Ivanov Ivan',
			'permissions' => [
				
					
						[
							'id' => 1,
							'permission' => 'comment'
						],

						[
							'id' => 2,
							'permission' => 'upload photo'
						],

					
						[
							'id' => 3,
							'permission' => 'add event'
						]

				]
		];
		
		$json_result=json_encode($result);
		$json_sample=json_encode($sample);


		
		assert($json_result==$json_sample);
		
		assert(count($container)==1);
		$t=$container[0];
		assert($t["request"]->getMethod()=="GET");
		assert($t["request"]->getUri()=="http://testapi.ru/get-user/ivanov?token=".$mytoken);
			
	}

	catch (Exception $e)
	{
		assert(FALSE);

	}
}





{  //get userdata user not found  test
	echo "get userdata   user not found  test\r\n";
	$mock = new MockHandler([
		new Response(200, [],
		'{
			"status": "Not found"
		}

		')]);
	$handlerStack = HandlerStack::create($mock);		
	try 
	{
		$api=new IntegrationAPI('http://testapi.ru', 10.0,$handlerStack);

		$auth_result=$api->getuser('ivanov2');
		
		assert(FALSE);
			
			
	}
	catch (IntegrationAPIException $a)
	{
		assert(TRUE);
	}	
	catch (Exception $e)
	{
		assert(FALSE);
	}
}



{ //get userdata Error test
	echo "get userdata Error test\r\n";
	$mock = new MockHandler([	
		new Response(200, [],
		'{
			"status": "Error",		
		}
		')]);
	$handlerStack = HandlerStack::create($mock);		
	try 
	{
		$api=new IntegrationAPI('http://testapi.ru', 10.0,$handlerStack);

		$auth_result=$api->getuser('ivanov3');
		assert(FALSE);
			
			
	}
	catch (IntegrationAPIException $a)
	{
		assert(TRUE);
	}	
	catch (Exception $e)
	{
		assert(FALSE);
	}
}
	

{  //get userdata   404 test
	echo "get userdata   404 test\r\n";
	$mock = new MockHandler([	
		new Response(404, [],'Error 404')]);
	$handlerStack = HandlerStack::create($mock);		
	try 
	{
		$api=new IntegrationAPI('http://testapi.ru', 10.0,$handlerStack);

		$auth_result=$api->getuser('ivanov4');
		assert(FALSE);
			
			
	}
	catch (IntegrationAPIException $a)
	{
		assert(FALSE);
	}	
	catch (Exception $e)
	{
		assert(TRUE);
	}
}
	
	
	
	


{//update user OK test
	echo "update user OK test\r\n";
	$mock = new MockHandler([
		new Response(200, [],
		'{
			"status": "OK"

		}

		')]);
	$container = [];
	$history = Middleware::history($container);
		
	$handlerStack = HandlerStack::create($mock);
	$handlerStack->push($history);
	try 
	{
		$api=new IntegrationAPI('http://testapi.ru', 10.0,$handlerStack);
		
		$mytoken="hd3d89gHGDswoi";
		
		$api->setToken($mytoken);

		$new_userdata= [

		"active"=> "1",
		"blocked"=> true,
		"name"=> "Petr Petrovich",
		"permissions"=> [
			[
				"id"=> 1,
				"permission"=> "comment"
			],
		 ]

		];		
		$result=$api->update('ivanov',$new_userdata);
		

		$sample=[
			'status' => 'OK'	
		];
		
		$json_result=json_encode($result);
		$json_sample=json_encode($sample);		
		assert($json_result==$json_sample);

			
		assert(count($container)==1);
		$t=$container[0];
		assert($t["request"]->getMethod()=="POST");
		assert($t["request"]->getUri()=="http://testapi.ru/user/ivanov/update?token=".$mytoken);
		$body=$t["request"]->getBody()->__toString();
		
		$body_sample='{"active":"1","blocked":true,"name":"Petr Petrovich","permissions":[{"id":1,"permission":"comment"}]}';
		assert($body==$body_sample);
		
	}

	catch (Exception $e)
	{
		assert(FALSE);

	}
}




{  //update user not found  test
	echo "update user not found  test\r\n";
	$mock = new MockHandler([
		new Response(200, [],
		'{
			"status": "Not found"
		}

		')]);
	$handlerStack = HandlerStack::create($mock);		
	try 
	{
		$api=new IntegrationAPI('http://testapi.ru', 10.0,$handlerStack);



		$new_userdata= [

		"active"=> "1",
		"blocked"=> true,
		"name"=> "Petr Petrovich",
		"permissions"=> [
			[
				"id"=> 1,
				"permission"=> "comment"
			],
		 ]

		];					
		$auth_result=$api->update('ivanov2', $new_userdata);
		
		assert(FALSE);
			
			
	}
	catch (IntegrationAPIException $a)
	{
		assert(TRUE);
	}	
	catch (Exception $e)
	{
		assert(FALSE);
	}
}



{  //update user Error  test
	echo "update user Error  test\r\n";
	$mock = new MockHandler([
		new Response(200, [],
		'{
			"status": "Error"
		}

		')]);
	$handlerStack = HandlerStack::create($mock);		
	try 
	{
		$api=new IntegrationAPI('http://testapi.ru', 10.0,$handlerStack);


		$new_userdata= [

		"active"=> "1",
		"blocked"=> true,
		"name"=> "Petr Petrovich",
		"permissions"=> [
			[
				"id"=> 1,
				"permission"=> "comment"
			],
		 ]

		];				
		$auth_result=$api->update('ivanov2', $new_userdata);
		
		assert(FALSE);
			
			
	}
	catch (IntegrationAPIException $a)
	{
		assert(TRUE);
	}	
	catch (Exception $e)
	{
		assert(FALSE);
	}
}


{  //update user  404 test
	echo "update user  404 test\r\n";
	$mock = new MockHandler([	
		new Response(404, [],'Error 404')]);
	$handlerStack = HandlerStack::create($mock);		
	try 
	{
		$api=new IntegrationAPI('http://testapi.ru', 10.0,$handlerStack);

		$new_userdata= [

		"active"=> "1",
		"blocked"=> true,
		"name"=> "Petr Petrovich",
		"permissions"=> [
			[
				"id"=> 1,
				"permission"=> "comment"
			],
		 ]

		];				
		$result=$api->update('ivanov4', $new_userdata);
		assert(FALSE);
			
			
	}
	catch (IntegrationAPIException $a)
	{
		assert(FALSE);
	}	
	catch (Exception $e)
	{
		assert(TRUE);
	}
}
