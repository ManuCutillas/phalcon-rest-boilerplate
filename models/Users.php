<?php

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Behavior\SoftDelete;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Uniqueness;


class Users extends BaseModel {

	public function initialize() {
		$this->addBehavior(new SoftDelete([
			'field' => 'deleted_at',
			'value' => date('Y-m-d H:i:s')
		]));
	}


	public function validation() {
		$validator = new Validation;

		$validator->add('name', new PresenceOf([
			'model' => $this,
			'message' => 'Name field is required'
		]));

		$validator->add('phone', new PresenceOf([
			'model' => $this,
			'message' => 'Phone field is required'
		]));

		$validator->add('email', new PresenceOf([
			'model' => $this,
			'message' => 'Email field is required'
		]));

		$validator->add('email', new Uniqueness([
			'model'   => $this,
			'message' => 'A user with the same email address is already registered, forgot the password?'
		]));

		$validator->add('email', new Email([
			'model' => $this,
			'message' => 'Email is invalid'
		]));

		$validator->add('gender', new PresenceOf([
			'model' => $this,
			'message' => 'Gender field is required'
		]));

		$validator->add('password', new PresenceOf([
			'model' => $this,
			'message' => 'Password field is required'
		]));

		$validator->add('role', new PresenceOf([
			'model' => $this,
			'message' => 'Gender field is required'
		]));

		return $this->validate($validator);
	}


	public function beforeValidationOnCreate() {
		$this->created_at = date('Y-m-d H:i:s');
	}


	public function beforeValidationOnUpdate() {
		$this->updated_at = date('Y-m-d H:i:s');
	}

}
