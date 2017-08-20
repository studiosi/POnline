<?php

	global $app;
        
        
        
        //
        
	$app->get('/', 'TU\Controllers\StaticController::getIndex')
		->bind('index');
	
	$app->get('/image', 'TU\Controllers\ImageController::getImage')
		->bind('image');

	$app->get('/signup', 'TU\Controllers\PlayerController::getSignup')
		->bind('signup');
	$app->post('/signup', 'TU\Controllers\PlayerController::postSignup');

	$app->get('/start', 'TU\Controllers\PlayerController::getLoginPlayer')
	->bind('start');
	$app->post('/start', 'TU\Controllers\PlayerController::postLoginPlayer');
	
	// Player exit URL
	$app->get('/exit', function () use($app) {
		$app['session']->start();
		$app['session']->invalidate();
		return $app->redirect($app['url_generator']->generate('index'));
	})->bind('exit');