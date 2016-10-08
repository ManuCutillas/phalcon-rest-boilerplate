<?php

use Phalcon\Mvc\Controller;

/*
** Base controller
*/

class BaseController extends Controller {

	protected $user;
	protected $request;
	protected $response;


	public function onConstruct() {
		$this->user = $this->session->has('user')? $this->session->get('user') : null;

		$this->request = $this->application->request;
		$this->response = $this->application->response;
	}


	protected function returnError($code, $type, $messages) {
		$this->response->setStatusCode($code, $type);
		$this->response->setJsonContent((object) [
			'error' => is_array($messages)? $messages : [ $messages ]
		]);

		return;
	}


	protected function returnNotice($code, $type, $messages) {
		$this->response->setStatusCode($code, $type);
		$this->response->setJsonContent((object) [
			'notice' => is_array($messages)? $messages : [ $messages ]
		]);

		return;
	}

}
