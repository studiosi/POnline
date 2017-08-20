<?php

	// Require the composer autoloader
	require_once __DIR__ . '/../vendor/autoload.php';
	use TU\Utils\FileRequirer;
	// Create the app object
	$app = new Silex\Application();

	// Set debug mode (DELETE FOR PRODUCTION)
	$app['debug'] = true;

	// Set default time zone
	date_default_timezone_set('Europe/Helsinki');

	// Require all config/routing files
	FileRequirer::requireDirectory( __DIR__ . "/config");
	FileRequirer::requireDirectory( __DIR__ . "/routing");

	// Run the app object
	$app->run();
