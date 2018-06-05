<?php
namespace MiniPHP;

class Request
{
	private $get;
    private $post;
    private $files;
    private $server;
    private $cookie;
    private $method;
    private $requested_uri;
    private $body = null;

    public function __construct(array $GET = array(), array $POST = array(), array $FILES = array(), array $SERVER = array(), array $COOKIE = array()){
        $this->get = $GET;
        $this->post = $POST;
        $this->files = $FILES;
        $this->server = $SERVER;
        $this->cookie = $COOKIE;
        $this->method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $this->requested_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/' ;
    }

    public static function createFromGlobals(){
        return new Request($_GET,$_POST,$_FILES,$_SERVER,$_COOKIE);
    }

    /**
     * Get query param passed by URL
     * @param $key
     * @return value or false
     */
    public function get($key){
        return isset($this->get[$key]) ? $this->get[$key] : false;
    }

    /**
     * Get post variables
     * @param $key
     * @return value or false
     */
    public function post($key){
        return isset($this->post[$key]) ? $this->post[$key] : false;
    }

    public function server($key){
        return isset($this->server[$key]) ? $this->server[$key] : false;
    }

    public function cookie($key){
        return isset($this->cookie[$key]) ? $this->cookie[$key] : false;
    }

    /**
     * Returns the Request Body content from POST,PUT
     * For more info see: http://php.net/manual/en/wrappers.php.php
     */
    public function getBody(){
        if ($this->body == null)
            $this->body = file_get_contents('php://input');
        return $this->body;
    }

    /**
     * Get headers received
     * @param $key
     * @return value or false
     */
    public function header($key){
        return isset($_SERVER['HTTP_'.strtoupper($key)]) ? $_SERVER['HTTP_'.strtoupper($key)] : false;
    }

    public function getMethod(){
        return $this->method;
    }

    public function  getRequestedUri(){
        return $this->requested_uri;
    }
}