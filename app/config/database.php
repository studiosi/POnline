<?php

	// DATABASE
	
	global $app;
	
	$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
	    'db.options' => array (
            'driver'    => 'pdo_mysql',
            'host'      => 'localhost',
            'dbname'    => 'annotator',
            'user'      => 'root',
            'password'  => '',
            'charset'   => 'utf8'
	    ),
	));
