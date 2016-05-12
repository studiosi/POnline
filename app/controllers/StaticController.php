<?php

	namespace TU\Controllers;

	use Silex\Application;
	use Symfony\Component\HttpFoundation\Request;
	use TU\DAO\ImageDAO;
	use TU\DAO\PlayerDAO;
	use TU\Utils\MathUtils;
				
	class StaticController {
		
		public function getIndex(Application $app) {
			
			$imDAO = new ImageDAO();
			$pDAO = new PlayerDAO();
			
			$n_images = $imDAO->getNumberImages($app);
			$total = $imDAO->getTotalClicks($app);
			
			$percentage = MathUtils::calculateCurrentPercentage($total, $n_images);
				
			$leaderboard = $pDAO->getLeaderboard($app);
			
			return $app['twig']->render('index.twig', array(
				'percentage' => $percentage,
				'leaderboard' => $leaderboard
			));

		}
		
		public function getLogin(Application $app, Request $req) {
			
			$error = $app['security.last_error']($req);
			
			if(!empty($error)) {
				
				$app['session']->invalidate();
				
			}
			
			return $app['twig']->render('login.twig', array('error' => $error));
			
		}

	}
