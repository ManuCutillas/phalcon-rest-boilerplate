<?php

/*
** Users controller
*/

class UsersController extends BaseController {

	// Find users
	public function find() {
		try {
			$query = $this->modelsManager->createBuilder()
				->columns([
					'Users.id',
					'Users.created_at',
					'Users.updated_at',
					'Users.deleted_at',
					'Users.name',
					'Users.phone',
					'Users.email',
					'Users.gender',
					'Users.role',
					'Users.status'])
				->from('Users')
				->where('Users.deleted_at is null');

			if ($this->request->has('created_at_after')) {
				$query->andWhere('Users.created_at >= :created_at_after:', [
					'created_at_after' => $this->request->get('created_at_after', 'striptags')
				]);
			}

			if ($this->request->has('created_at_before')) {
				$query->andWhere('Users.created_at <= :created_at_before:', [
					'created_at_before' => $this->request->get('created_at_before', 'striptags')
				]);
			}

			if ($this->request->has('updated_at_after')) {
				$query->andWhere('Users.updated_at >= :updated_at_after:', [
					'updated_at_after' => $this->request->get('updated_at_after', 'striptags')
				]);
			}

			if ($this->request->has('updated_at_before')) {
				$query->andWhere('Users.updated_at <= :updated_at_before:', [
					'updated_at_before' => $this->request->get('updated_at_before', 'striptags')
				]);
			}

			if ($this->request->has('deleted_at_after')) {
				$query->andWhere('Users.deleted_at >= :deleted_at_after:', [
					'deleted_at_after' => $this->request->get('deleted_at_after', 'striptags')
				]);
			}

			if ($this->request->has('deleted_at_before')) {
				$query->andWhere('Users.deleted_at <= :deleted_at_before:', [
					'deleted_at_before' => $this->request->get('deleted_at_before', 'striptags')
				]);
			}

			if ($this->request->has('name')) {
				$query->andWhere('Users.name like :name:', [
					'name' => '%'. $this->request->get('name', 'striptags') .'%'
				]);
			}

			if ($this->request->has('phone')) {
				$query->andWhere('Users.phone like :phone:', [
					'phone' => '%'. $this->request->get('phone', 'striptags') .'%'
				]);
			}

			$query->orderBy('Users.created_at');

			if ($this->request->has('order') && in_array($this->request->get('order'), ['Users.created_at', 'Users.name', 'Users.email', 'Users.role', 'Users.status']))
				$query->orderBy($this->request->get('order'));

			if ($this->request->has('mode') && in_array($this->request->get('mode'), ['asc', 'desc']))
				$query->orderBy($query->getOrderBy() .' '. $this->request->get('mode'));

			else
				$query->orderBy($query->getOrderBy() .' desc');

			$query->limit(20, $this->request->get('offset', 'int', 0));

			$users = $query->getQuery()->execute();

			$total_users = $query
				->columns(['count' => 'count(*)'])
				->limit(null)
				->getQuery()->getSingleResult()->count;

		} catch (Exception $e) {
			if ($this->config->application->environment == 'development')
				return $this->returnError(400, 'Bad Request', $e->getMessage());

			return $this->returnError(400, 'Bad Request', 'Bad request');
		}

		if ($users->count() == 0)
			return $this->returnNotice(204, 'No Content', 'No users found');

		$this->response->setJsonContent((object) [
			'users' => $users->toArray(),
			'total_users' => $total_users,
		]);
	}


	// Create user
	public function create() {
		$data = $this->request->getJsonRawBody(true);
		$data['password'] = $this->security->hash($data['password']);

		if ($this->user === null)
			$data['role'] = 'user';

		$user = new Users;

		if ($user->create($data) == false)
			return $this->returnError(400, 'Bad Request', $user->getResponseMessages());

		$this->response->setStatusCode(201, 'Created');

		if ($this->user === null) {
			$user = (object) [
				'id' => $user->id,
				'name' => $user->name,
				'email' => $user->email,
				'role' => $user->role
			];

			$this->session->set('user', $user);

			return $this->response->setJsonContent([
				'user' => $user,
				'success' => [ 'Your account has been created successfully' ]
			]);
		}

		$this->response->setJsonContent([
			'id' => $user->id,
			'success' => [ 'User created successfully' ],
		]);
	}


	// Get user
	public function get($id) {
		try {
			$query = $this->modelsManager->createBuilder()
				->columns([
					'Users.id',
					'Users.name',
					'Users.gender',
				]);

			if ($this->user->id == $id) {
				$query->columns(array_merge($query->getColumns(), [
					'Users.phone',
					'Users.email',
					'Users.status',
				]));
			}

			if ($this->user->role == 'admin') {
				$query->columns(array_merge($query->getColumns(), [
					'Users.created_at',
					'Users.updated_at',
					'Users.deleted_at',
					'Users.role',
					'Users.comments',
				]));
			}

			$user = $query
				->from('Users')
				->where('Users.id = :id:', ['id' => $id])
				->getQuery()->getSingleResult();

			if ($user == false)
				return $this->returnError(404, 'Not Found', 'User not found');

		} catch (Exception $e) {
			if ($this->config->application->environment == 'development')
				return $this->returnError(400, 'Bad Request', $e->getMessage());

			return $this->returnError(400, 'Bad Request', 'Bad request');
		}

		$this->response->setJsonContent((object) [
			'user' => $user,
		]);
	}


	// Update user
	public function update($id) {
		$user = Users::findFirst($id);

		if ($user == false)
			return $this->returnError(404, 'Not Found', 'User not found');

		if ($this->user->role != 'admin' && $user->id != $this->user->id)
			return $this->returnError(403, 'Forbidden', 'You do not have privileges to update other users');

		$data = $this->request->getJsonRawBody(true);

		if ($this->user->role != 'admin') {
			if (isset($data['role']))
				return $this->returnError(403, 'Forbidden', 'You do not have privileges to manage roles');
		}

		if (isset($data['password']))
			$data['password'] = $this->security->hash($data['password']);

		if ($user->update($data) == false)
			return $this->returnError(400, 'Bad Request', $user->getResponseMessages());

		$message = 'User updated successfully';

		if ($this->user->role == 'user')
			$message = 'Your data has been updated successfully';

		$this->response->setJsonContent((object) [
			'success' => [ $message ]
		]);
	}


	// Delete user
	public function delete($id) {
		$user = Users::findFirst($id);

		if ($user == false)
			return $this->returnError(404, 'Not Found', 'User not found');

		if ($this->user->role != 'admin' && $user->id != $this->user->id)
			return $this->returnError(403, 'Forbidden', 'You do not have privileges to delete other users');

		if ($user->delete() == false)
			return $this->returnError(400, 'Bad Request', $user->getResponseMessages());

		$message = 'User deleted successfully';

		if ($this->user->role != 'admin') {
			$message = 'Your account has been deleted successfully';
			$this->session->remove('user');
		}

		$this->response->setJsonContent((object) [
			'success' => [ $message ]
		]);
	}


	// User login
	public function login() {
		$user = $this->user;

		if ($user === null) {
			$data = $this->request->getJsonRawBody();

			if (!isset($data->email) || !isset($data->password))
				return $this->returnError(400, 'Bad Request', 'Bad request');

			try {
				$user = $this->modelsManager->createBuilder()
					->columns([
						'Users.id',
						'Users.name',
						'Users.email',
						'Users.password',
						'Users.role'])
					->from('Users')
					->where('Users.deleted_at is null')
					->andWhere('Users.email = :email:', ['email' => $data->email])
					->getQuery()->getSingleResult();

			} catch (Exception $e) {
				if ($this->config->application->environment == 'development')
					return $this->returnError(400, 'Bad Request', $e->getMessage());

				return $this->returnError(400, 'Bad Request', 'Bad request');
			}

			if ($user == false)
				return $this->returnError(404, 'Not Found', 'User not found');

			if ($this->security->checkHash($data->password, $user->password) == false)
				return $this->returnError(401, 'Unauthorized', 'Wrong email or password');

			$user = $user->toArray();

			unset($user['password']);

			$user = (object) $user;

			$this->session->set('user', $user);
		}

		$this->response->setJsonContent((object) [
			'user' => (array) $user,
			'success' => [
				'Login successfull, welcome '. $user->name
			]
		]);
	}


	// User logout
	public function logout() {
		$this->session->remove('user');

		$this->response->setJsonContent((object) [
			'success' => [
				'Goodbye '. $this->user->name .'!'
			]
		]);
	}

}
