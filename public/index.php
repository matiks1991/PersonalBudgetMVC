<?php
	//Front controller
	
	//Set lifetime cookie of session
	ini_set('session.cookie_lifetime', '864000'); //ten days in seconds

	//Composer
	require_once '../vendor/autoload.php';


	//Error and Exception handling
	error_reporting(E_ALL);
	set_error_handler('Core\Error::errorHandler');
	set_exception_handler('Core\Error::exceptionHandler');


	//Session
	session_start();

	//Routing
	$router = new Core\Router();


	//Add the routes
	$router->add('', ['controller' => 'Home', 'action' => 'index']);
	$router->add('login', ['controller' => 'Login', 'action' => 'new']);
	$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);
	$router->add('password/reset/{token:[\da-f]+}', ['controller' => 'Password', 'action' => 'reset']);
	$router->add('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);
	$router->add('expense', ['controller' => 'Expense', 'action' => 'index']);
	$router->add('income', ['controller' => 'Income', 'action' => 'index']);
	$router->add('balance', ['controller' => 'Balance', 'action' => 'index']);
	$router->add('poprzedni-miesiac', ['controller' => 'Balance', 'action' => 'previousMonth']);
	$router->add('biezacy-rok', ['controller' => 'Balance', 'action' => 'currentYear']);
	$router->add('niestandardowy-okres', ['controller' => 'Balance', 'action' => 'customPeriod']);
	$router->add('settings', ['controller' => 'Settings', 'action' => 'index']);

	$router->add('{controller}/{action}');

	// Display the routing table
	$router->dispatch($_SERVER['QUERY_STRING']);