<?php

namespace MiniPHP;

use MiniPHP\Request;

abstract class AbstractFramework
{
	protected $request;
	protected $routes = array();
	
	public function __construct()
	{
		$this->request = Request::createFromGlobals();
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
	
	public function setStatusCode($status = 200)
	{
		if ($status != 200){
			if (!function_exists('http_response_code'))//PHP < 5.4
			{//send header
				header('X-PHP-Response-Code: '.$status, true, $status);
			}
			else http_response_code($status);
		}
		return $status;
	}

	public function traverseRoutes($method = 'GET', array $routes, array &$slugs){
		if (isset($routes[$method])){
			foreach($routes[$method] as $route)
				if($func = $this->processUri($route, $slugs)){
					//call callback function with params in slugs
					call_user_func_array($func, $slugs);
					return true;
				}
		}
		return false;
	}

	protected  function getSegment($segment_number){
		$uri = $this->request->getRequestedUri();
		$uri_segments = preg_split('/[\/]+/',$uri,null,PREG_SPLIT_NO_EMPTY);

		return isset($uri_segments[$segment_number]) ? $uri_segments[$segment_number] : false;
	}

	private function processUri($route, &$slugs = array()){
		$url =$this->request->getRequestedUri();
		$uri = parse_url($url, PHP_URL_PATH);
		$func = $this->matchUriWithRoute($uri, $route, $slugs);
		return $func ? $func : false;
	}

	static function matchUriWithRoute($uri, $route, &$slugs){
		$uri_segments = preg_split('/[\/]+/', $uri, null, PREG_SPLIT_NO_EMPTY);

		$route_segments = preg_split('/[\/]+/', $route->route, null, PREG_SPLIT_NO_EMPTY);

		if (AbstractFramework::compareSegments($uri_segments, $route_segments, $slugs)){
			//route matched
			return $route->function; //Object route
		}
		return false;
	}

	/**  Match 2 uris
	 * @param $uri_segments
	 * @param $route_segments
	 * @return bool
	 */
	static function CompareSegments($uri_segments, $route_segments, &$slugs){

		if (count($uri_segments) != count($route_segments)) return false;

		foreach($uri_segments as $segment_index => $segment){
			$segment_route = $route_segments[$segment_index];
			//different segments must be an {slug} | :slug
			$is_slug = preg_match('/^{[^\/]*}$/', $segment_route) || preg_match('/^:[^\/]*/', $segment_route,$matches);

			if ($is_slug){//Note php does not support named parameters
				if (strlen(trim($segment)) === 0){
					return false;
				}
				$slugs[ str_ireplace(array(':', '{', '}'), '', $segment_route) ] = $segment;//save slug key => value
				}
			else if($segment_route !== $segment && $is_slug !== 1)
				return false;
		}
		//match with every segment
		return true;
	}
}