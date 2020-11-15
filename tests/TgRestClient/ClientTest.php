<?php declare(strict_types=1);

namespace TgRestClient;

use PHPUnit\Framework\TestCase;


/**
 * Client test cases.
 */
final class ClientTest extends TestCase {
	
    private static $TEST_URI     = 'https://gorest.co.in/public-api/users';
    private static $TEST_URI2    = 'https://jsonplaceholder.typicode.com/users';
    
    public function testHead(): void {
        $response  = Request::head(self::$TEST_URI2)
            ->execute();
        $this->assertEquals(200, $response->getHttpCode());
        $this->assertTrue(strpos($response->getContentType(), Headers::TYPE_JSON) === 0);
        $this->assertEquals('cloudflare', $response->getHeader('Server'));
    }

    public function testGet(): void {
        $response  = Request::get(self::$TEST_URI2)
            ->execute();
        $this->assertEquals(200, $response->getHttpCode());
        $this->assertTrue(strpos($response->getContentType(), Headers::TYPE_JSON) === 0);
        $this->assertEquals('cloudflare', $response->getHeader('Server'));
        
        $body = $response->getBody();
        $this->assertTrue(is_array($body));
        $this->assertEquals(10, count($body));        
    }

    /**
     * Requires environment variable GOREST_TOKEN.
     */
    public function testGetPostPatchDelete(): void {
        if (getenv('GOREST_TOKEN')) {
            Headers::setDefaultHeader('gorest.co.in', Headers::AUTHORIZATION, 'Bearer '.getenv('GOREST_TOKEN'));
            $rand = \TgUtils\Utils::generateRandomString(4);
            
            // Create a record (PUT)
            $user      = json_decode('{"name":"John Doe '.$rand.'", "email":"john.doe.'.$rand.'@example.com", "gender":"Male", "status":"Active"}');
            $response  = Request::post(self::$TEST_URI, $user)->execute();
            $this->assertEquals(200, $response->getHttpCode());
            $body = $response->getBody();
            $this->assertEquals(201, $body->code);
            $id = $body->data->id;
            //echo "\n\n ==> CREATED USER ID: $id\n\n";
            $this->assertEquals('John Doe '.$rand,                $body->data->name);
            $this->assertEquals('john.doe.'.$rand.'@example.com', $body->data->email);
            
            // Retrieve the record (GET)
            $response  = Request::get(self::$TEST_URI.'/'.$id)->execute();
            $this->assertEquals(200, $response->getHttpCode());
            $body = $response->getBody();
            $this->assertEquals(200, $body->code);
            $this->assertEquals('John Doe '.$rand,                $body->data->name);
            $this->assertEquals('john.doe.'.$rand.'@example.com', $body->data->email);
            
            // Patch the record (PATCH)
            $user      = json_decode('{"name":"Jane Doe '.$rand.'", "email":"jane.doe.'.$rand.'@example.com", "gender":"Female", "status":"Active"}');
            $response  = Request::patch(self::$TEST_URI.'/'.$id, $user)->execute();
            $this->assertEquals(200, $response->getHttpCode());
            $body = $response->getBody();
            $this->assertEquals(200, $body->code);
            $this->assertEquals('Jane Doe '.$rand,                $body->data->name);
            $this->assertEquals('jane.doe.'.$rand.'@example.com', $body->data->email);
            
            // Check the record again (GET)
            $response  = Request::get(self::$TEST_URI.'/'.$id)->execute();
            $this->assertEquals(200, $response->getHttpCode());
            $body = $response->getBody();
            $this->assertEquals(200, $body->code);
            $this->assertEquals('Jane Doe '.$rand,                $body->data->name);
            $this->assertEquals('jane.doe.'.$rand.'@example.com', $body->data->email);
            
            // Delete the record (DELETE)
            $response  = Request::delete(self::$TEST_URI.'/'.$id)->execute();
            $this->assertEquals(200, $response->getHttpCode());
            $body = $response->getBody();
            $this->assertEquals(204, $body->code);
            $this->assertNull($body->data);
            
            // Check that it is deleted (GET)
            $response  = Request::get(self::$TEST_URI.'/'.$id)->execute();
            $this->assertEquals(200, $response->getHttpCode());
            $body = $response->getBody();
            $this->assertEquals(404, $body->code);
            
        } else {
            // Prevent dangerous issue
            $this->assertTrue(true);
        }
    }

}

