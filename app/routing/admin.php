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
        
        $app->get('/admin/csv', 'TU\Controllers\AdminController::getPercentageCSV')
	->bind('admin_csv');
        
        $app->get('/admin/csv2', 'TU\Controllers\AdminController::getAllPlayersToCsv')
	->bind('admin_csv2');
        
        $app->get('/admin/csv3', 'TU\Controllers\AdminController::getUserClickAmount')
	->bind('admin_csv3');
        
        $app->get('/admin/csv4', 'TU\Controllers\AdminController::getConsistency')
	->bind('admin_csv4');
        
        $app->get('/admin/csv5', 'TU\Controllers\AdminController::getTimestampsCSV')
	->bind('admin_csv5');
        
        $app->get('/admin/csv6', 'TU\Controllers\AdminController::get5ToNAll')
	->bind('admin_csv6');
        
        $app->get('/admin/csv7', 'TU\Controllers\AdminController::getImgCountsForImprovement')
        ->bind('admin_csv7');
               
	$app->get('/admin/see/{id}', 'TU\Controllers\ImageController::showPoints')
	->bind('admin_show');
	
	$app->get('/admin/player/{id}', 'TU\Controllers\AdminController::showPlayerAdminMenu')
	->bind('player_manage');
	
	$app->get('/admin/player/control/{id_player}/{id_photo}', 'TU\Controllers\ImageController::showPlayerPoints')
	->bind('player_points');
	
        