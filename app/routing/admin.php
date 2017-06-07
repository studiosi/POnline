<?php

	global $app;
	
	// Create Admin
        
        $app->get('/create', 'TU\Controllers\AdminController::getSignup')
		->bind('create');
	$app->post('/create', 'TU\Controllers\AdminController::postSignup');
        
        
	// Login URLs
	
	$app->get('/login', 'TU\Controllers\StaticController::getLogin')
	->bind('login');
	
	$app->post('/admin/login_check', function(){})
	->bind('login_check');
	
	// Admin URLs
	
	$app->get('/admin/menu', 'TU\Controllers\AdminController::showMainMenu')
	->bind('admin_menu');
	
	$app->get('/admin/see/{id}', 'TU\Controllers\ImageController::showPoints')
	->bind('admin_show');
	
	$app->get('/admin/player/{id}', 'TU\Controllers\AdminController::showPlayerAdminMenu')
	->bind('player_manage');
	
	$app->get('/admin/player/control/{id_player}/{id_photo}', 'TU\Controllers\ImageController::showPlayerPoints')
	->bind('player_points');
	