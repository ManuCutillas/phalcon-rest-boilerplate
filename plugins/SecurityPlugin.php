<?php

/*
** SecurityPlugin
*/

use Phalcon\Acl;
use Phalcon\Acl\Adapter\Memory as AclList;
use Phalcon\Acl\Resource;
use Phalcon\Acl\Role;
use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\User\Plugin;

class SecurityPlugin extends Plugin {

	private $roles = ['guest', 'user', 'admin'];

	private $resources = [];


	public function addResource($url, $method, $roles) {
		if (!isset($this->resources[$url]))
			$this->resources[$url] = [];

		$this->resources[$url][$method] = $roles;
	}


	public function getAcl() {
		if ($this->config->application->environment != 'development' && isset($this->persistent->acl))
			return $this->persistent->acl;

		$acl = new AclList;
		$acl->setDefaultAction(Acl::DENY);

		foreach ($this->roles as $role)
			$acl->addRole($role);

		foreach ($this->resources as $url => $methods) {
			$acl->addResource(new Resource($url), array_keys($methods));

			foreach ($methods as $method => $roles) {
				foreach ($roles as $role)
					$acl->allow($role, $url, $method);
			}
		}

		return $this->persistent->acl = $acl;
	}


	public function beforeExecuteRoute(Event $event, Micro $app) {
		$role = 'guest';

		$route = $app->getRouter()->getMatchedRoute();

		if ($user = $this->session->get('user'))
			$role = $user->role;

		$acl = $this->getAcl();

		if ($acl->isAllowed($role, $route->getPattern(), $route->getHttpMethods()) != Acl::ALLOW) {
			if ($role == 'user')
				$this->refuseForbidden();

			$this->refuseUnauthorized();
		}
	}


	public function refuseForbidden($message = 'You do not have enough privileges to access this resource') {
		$this->response->setStatusCode(403, 'Forbidden');
		$this->response->setJsonContent((object) [
			'error' => [ $message ]
		])->send();

		exit;
	}


	public function refuseUnauthorized($message = 'You are not allowed to access a restricted resource') {
		$this->response->setStatusCode(401, 'Unauthorized');
		$this->response->setJsonContent((object) [
			'error' => [ $message ]
		])->send();

		exit;
	}

}
