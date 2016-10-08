<?php

use Phalcon\DI\Injectable;
use Phalcon\Mvc\Micro\Collection;

/*
** RouteCollection class
*/

class RouteCollection extends Injectable {

	private $controller;
	private $collection;
	private $route;


	public function __construct($di, $controller) {
		$this->setDI($di);

		$this->controller = new $controller;

		$this->collection = new Collection;
		$this->collection->setHandler($this->controller);

		$this->route = (object) [
			'url' => '',
			'method' => '',
			'action' => '',
			'roles' => [],
		];
	}


	public function add($url) {
		$this->route->url = $url;

		return $this;
	}


	public function via($method) {
		$this->route->method = $method;

		return $this;
	}


	public function to($action) {
		$this->route->action = $action;

		return $this;
	}


	public function by() {
		$roles = func_get_args();

		$this->collection->{$this->route->method}($this->route->url, $this->route->action);
		$this->acl->addResource($this->route->url, strtoupper($this->route->method), $roles);

		return $this;
	}


	public function mount() {
		$this->application->mount($this->collection);
	}

}
