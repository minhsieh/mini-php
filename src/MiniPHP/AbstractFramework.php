<?php

namespace MiniPHP;

abstract class AbstractFramework
{
	protected $request;
	protected $routes = array();
	
	public function __construct()
	{
		
	}
	
	public abstract function get($uri, callable $callback);
	public abstract function post($uri, callable $callback);
	public abstract function put($uri, callable $callback);
	public abstract function delete($uri, callable $callback);
	
	public abstract function listen();
	
	public function getRequest()
	{
		return $this->request;
	}
	
	public function setStatusCode()
	{
        
    }
    
    public function 
}