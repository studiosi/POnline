<?php

	global $app;
	
	$app->post('/image', 'TU\Controllers\ImageController::savePoint');
	
	$app->post('/admin/userop', 'TU\Controllers\AdminController::userOp')
	->bind('userop');
	
	