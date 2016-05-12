<?php

	// TWIG TEMPLATE SERVICE PROVIDER CONFIGURATION
	
	global $app;

	$app->register(new Silex\Provider\TwigServiceProvider(), array(	
		'twig.path' => __DIR__.'/../views',
		'twig.options' => array (
			'cache' => __DIR__ . '/../cache'
		)	
	));
