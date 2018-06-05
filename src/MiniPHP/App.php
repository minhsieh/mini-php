<?php
namespace MiniPHP;

use MiniPHP\Route;

class App
{
	protected $db;
	protected $request;
	protected $routes = array();
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get($uri, callable $callbacak)
	{
		$this->routes['GET'][] = new Route($uri , $callback);
	}
	
	public function post($uri, callable $callback)
	{
		$this->routes['POST'][] = new Route($uri, $callback);
	}
	
	public function put($uri, callable $callback)
	{
		$this->routes['PUT'][] = new Route($uri, $callback);
	}
	
	public function delete($uri, callable $callback)
	{
		$this->routes['DELETE'][] = new Route($uri, $callback);
	}
	
	public function respond(callable $callback)
	{
		$this->routes['respond'] = new Route('', $callback);
	}
	
	public function listen()
	{
		$slugs = array();
		$run = $this->traverseRoutes($this->request->getMethod(), $this->routes, $slugs);
		
		if(!$run && (!isset($this->routes['respond']) || empty($this->routes['respond']))){
			return $this->error("Route path not found: {$this->request->getRequestUri()} with method: {$this->request->getMethod()}" , true);
		}
		else if(!$run){
			$callback = $this->routes['respond']->function();
            $callback();
		}
		return true;
	}
	
	public function getRoutes()
	{
		return $this->routes;
	}
	
	public function getRequest()
	{
		return $this->request;
	}
	
	public function Redirect($url)
	{
		echo header('Location: '.$url);
	}
	
	public function Response($filename = '', Array $vars = array(), $status = 200, Array $headers = array(), $asText = 0)
	{
		$this->setStatus($status);
		
		if(count($headers)) $this->setHeaders($headers);
		

	}
	
	public function setStatus($status = 200)
	{
		if($status != 200){
			http_response_code($status);	
		}
		return $status;
	}
	
	public function setHeaders(Array $headers)
	{
		foreach($headers as $key => $value){
			header($key.": ".$value);
		}
	}
}