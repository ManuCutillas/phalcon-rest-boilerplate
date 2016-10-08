<?php

/*
** Users routes
*/

$routes = new RouteCollection($di, 'UsersController');

$routes->add('/users')
	->via('get')->to('find')->by('admin')
	->via('post')->to('create')->by('guest', 'admin');

$routes->add('/users/{id:[0-9]+}')
	->via('get')->to('get')->by('user', 'admin')
	->via('put')->to('update')->by('user', 'admin')
	->via('delete')->to('delete')->by('user', 'admin');

$routes->add('/users/login')
	->via('post')->to('login')->by('guest');

$routes->add('/users/logout')
	->via('post')->to('logout')->by('user', 'admin');

$routes->mount();
