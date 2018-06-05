<?php
namespace MiniPHP;

class Route
{
	public $route;
	public $function;

	/**
	 * @param string $routeKey like /books/{id}/edit
	 * @param callable $func Function
	 */
	public function __construct($routeKey = '', callable $func){
		$this->route = $routeKey;
		$this->function = $func;
	}
}