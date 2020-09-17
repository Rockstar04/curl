<?php
require_once(dirname(dirname(__FILE__)) . '/vendor/autoload.php');

class CurlTest extends PHPUnit_Framework_TestCase {
    public function testGet() {
        $http = new \dcai\curl;
        $response = $http->get('http://httpbin.org/get', array('key'=>1));
        $json = $response->json();
        $this->assertEquals($json->args->key, 1);
    }
    public function testPostArray() {
        $http = new \dcai\curl;
        $response = $http->post('http://httpbin.org/post', array('key_1'=>1, 'key_2'=>'val_2'));
        $json = $response->json();
        $this->assertEquals($json->form->key_1, 1);
        $this->assertEquals($json->form->key_2, 'val_2');
    }
    public function testPostString() {
        $http = new \dcai\curl;
        $response = $http->post('http://httpbin.org/post', 'key_1=1&key_2=val_2');
        $json = $response->json();
        $this->assertEquals($json->form->key_1, 1);
        $this->assertEquals($json->form->key_2, 'val_2');
    }
    public function testUpload() {
        $http = new \dcai\curl();
        $postData = array(
            'afile' =>
                \dcai\curl::makeUploadFile(realpath(__DIR__ . '/assets/uploadtest.txt'))
        );
        $response = $http->post('http://httpbin.org/post', $postData);
        $json = $response->json();
        $this->assertEquals(trim($json->files->afile), 'upload test');
    }
    public function testHeaders() {
        $http = new \dcai\curl();
        $http->appendRequestHeader('oauthtoken', 'supersecret');
        $response = $http->get('http://httpbin.org/headers');
        $this->assertEquals(trim($response->json()->headers->Oauthtoken), 'supersecret');
    }
    public function testPostData() {
        $http = new \dcai\curl;
        $processed = $http->makePostFields(array(
            'hello' => 'world',
            'afile' => '@' . realpath(__DIR__ . '/assets/uploadtest.txt'),
            'nestedlist' => array(
                'name2' => array(
                    1,
                    2,
                    3
                ),
            ),
            'coollist' => array(
                'xbox one',
                'ps4',
                'wii',
            ),
        ));
        $this->assertCount(8, $processed);
        $this->assertArrayHasKey('hello', $processed);
        $this->assertEquals($processed['hello'], 'world');
        $this->assertArrayHasKey('nestedlist[name2][2]', $processed);
        $this->assertArrayHasKey('coollist[0]', $processed);
        $this->assertEquals($processed['coollist[0]'], 'xbox one');
    }
}
