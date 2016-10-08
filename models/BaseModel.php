<?php

use Phalcon\Mvc\Model;

class BaseModel extends Model {

	public function getResponseMessages() {
		$messages = [];

		foreach ($this->getMessages() as $message)
			$messages[] = $message->getMessage();

		return $messages;
	}

}
