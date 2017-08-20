<?php

	// SECURITY SERVICE PROVIDER CONFIGURATION	
	
	global $app;
	
	$app->register(new Silex\Provider\SecurityServiceProvider(), array(
	    'security.firewalls' => array(	    
	        'admin' => array(	            
	            'pattern' => '^/admin/',
	            'form' => array(	
	            	'login_path' => '/login', 
	            	'check_path' => '/admin/login_check',
	            	'always_use_default_target_path' => true,
	            	'default_target_path' => '/admin/menu'
	            ),	            
	            'logout' => array(	            
	            	'logout_path' => '/admin/logout'	            
	            ),	            
	            'users' => $app->share(function () use ($app) {
    				return new TU\Utils\DUserProvider($app['db']);
				}),
	        ),	    
	    )
	));

