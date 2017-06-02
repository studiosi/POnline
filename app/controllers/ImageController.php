<?php

	namespace TU\Controllers;

	use Silex\Application;
	use TU\DAO\ImageDAO;
	use RandomLib;
	use SecurityLib;
	use Symfony\Component\HttpFoundation\Request;
	use TU\Utils\MathUtils;
        use TU\Utils\Equation;
        use TU\Utils\Ellipse;
	use TU\DAO\PlayerDAO;
	use TU\Utils\FormatUtils;
				
	class ImageController {
		
		private $TOKEN_SESSION_NAME = 'session_token_name';
		private $IMAGE_ID_SESSION_NAME = 'session_id_name';
		private $NUMBER_CLICKS_SESSION = 'session_n_clicks_image';
		
		private $MIN_CLICKS = 9; // Preinput points, the center of the algorithm + 8 random points more

		public function getImage(Application $app) {
			
			$pDAO = new PlayerDAO();
			
			if(!$pDAO->checkIfUserLoggedIn($app)) {
				$app['session']->invalidate();
				return $app->redirect($app['url_generator']->generate('index'));
			}
			
			if(empty($app['session']->get($this->NUMBER_CLICKS_SESSION))) {
				
				$app['session']->set($this->NUMBER_CLICKS_SESSION, 0);
				
			}
			
			$user = $app['session']->get(PlayerDAO::$SESSION_PLAYER_ID);
			
			$nClicks = $pDAO->getNClicksByID($app, $user['id']);
			$nextNClicks = $pDAO->getNextNClicks($app, $user['id'], $nClicks);

			$imDAO = new ImageDAO();
			
			$image = $imDAO->getLessClickedImage($app);
			
			// Generate token and put it into session.
			// This way we can assure that nobody will troll by sending points automatically.
			$f = new RandomLib\Factory();
			$g = $f->getGenerator(new SecurityLib\Strength(SecurityLib\Strength::MEDIUM));			
			$token = $g->generateString(64);			
			$app['session']->set($this->TOKEN_SESSION_NAME, $token);
			$app['session']->set($this->IMAGE_ID_SESSION_NAME, $image['id']);
			
			return $app['twig']->render('image.twig', 
				array(
					'image' => $image,
					'token' => $token,
					'user' => $user,
					'clicksLeft' => $nextNClicks - $nClicks,
					'nclicks' => $app['session']->get($this->NUMBER_CLICKS_SESSION)
				));

		}
		
		public function savePoint(Application $app, Request $req) {
			
			$pDAO = new PlayerDAO();
				
			if(!$pDAO->checkIfUserLoggedIn($app)) {
				$app['session']->invalidate();
				return $app->redirect($app['url_generator']->generate('index'));
			}
			
			$t = $req->get('t');
			$t_s = $app['session']->get($this->TOKEN_SESSION_NAME);
			
			if($t != $t_s) {
				$app->abort(400, "Invalid request");
			}
			
			$id = $app['session']->get($this->IMAGE_ID_SESSION_NAME);
			$x = $req->get('x');
			$y = $req->get('y');
			
			$user = $app['session']->get(PlayerDAO::$SESSION_PLAYER_ID);
			
			$imDAO = new ImageDAO();
			
			$pointList = $imDAO->getAllClicksImage($app, $id);
			
			if(count($pointList) >= $this->MIN_CLICKS) {
			
				$centroid = MathUtils::calculateCentroid($pointList);
				
				$newPoint = array('x' => $x, 'y' => $y);
				
				$distance = MathUtils::calculateDistance($centroid, $newPoint);
				
                                // Acceptable point algorithm commented out
				// if (MathUtils::isAcceptablePoint($pointList, $centroid, $newPoint, $distance)) {					
					
					$image = $imDAO->insertPoint($app, $id, $newPoint, $user['id'], $distance, true);
					
					$n_clicks_session = $app['session']->get($this->NUMBER_CLICKS_SESSION);
					$n_clicks_session += 1;
					$n_clicks_session = $app['session']->set($this->NUMBER_CLICKS_SESSION, $n_clicks_session);
				
                                        /*
				}
				else {
					
					return json_encode(array('msg' => 'ERR'));
					
				}
                                */
				
				return json_encode(array('msg' => ''));
				
			}
			else {
				
				$centroid = array('x' => $x, 'y' => $y);
				$newPoint = array('x' => $x, 'y' => $y);
				$distance = 0;
						
				$image = $imDAO->insertPoint($app, $id, $newPoint, $user['id'], $distance, false);
					
				$n_clicks_session = $app['session']->get($this->NUMBER_CLICKS_SESSION);
				$n_clicks_session += 1;
				$n_clicks_session = $app['session']->set($this->NUMBER_CLICKS_SESSION, $n_clicks_session);
			
				return json_encode(array('msg' => ''));
				
			}
			
			return json_encode(array('msg' => 'ERR'));
			
		}

		public function showPoints(Application $app, $id) {
			
			$imDAO = new ImageDAO();
			$Rmax = 0; 
                        $Rmin = PHP_INT_MAX;
                        
			$image = $imDAO->getImageById($app, $id);
			$points = $imDAO->getAllClicksImage($app, $id);
			
			$centroid = MathUtils::calculateCentroid($points);		
			
			if(is_null($centroid)) {
				$centroid = array();
			}
                        
			/*
                        foreach ($points as $point) {
                            $dist = MathUtils::calculateDistance($point, $centroid);
                            if ($dist > $Rmax) {
                                $Rmax = $dist;
                            }
                            
                            if ($dist < $Rmin) {
                                $Rmin = $dist;
                            }
                        }
                        */
                        //$a = MathUtils::calculateSemiMajorAxis($Rmax, $Rmin);
                        //$b = MathUtils::calculateSemiMinorAxis($Rmax, $Rmin);
                        
                        //$eccentricity = MathUtils::calculateEccentricity($a, $b);
                        Equation::setfrompoints($points);
                        
                        
			$pointList = FormatUtils::getJavascriptSerializedPoints($points);
			$cent = FormatUtils::getJavascriptSerializedPoints(array($centroid), true);
			
			return $app['twig']->render('show.twig',
			array(
					'image' => $image,
					'pointList' => $pointList,
					'centroid' => $cent
			));
			
		}

		public function showPlayerPoints(Application $app, $id_player, $id_photo) {
			
			$imDAO = new ImageDAO();
			
			$image = $imDAO->getImageById($app, $id_photo);
			
			$points = $imDAO->getAllClicksImage($app, $id_photo); // Only valid points
			$centroid = MathUtils::calculateCentroid($points);
			
			if(is_null($centroid)) {
				
				$centroid = array();
				
			}
			
			$cent = FormatUtils::getJavascriptSerializedPoints(array($centroid), true);
			
			$user_points = $imDAO->getAllClicksUserImage($app, $id_player, $id_photo);			
			
			$pointList = FormatUtils::getJavascriptSerializedPoints($user_points);
			
			
			return $app['twig']->render('show.twig',
			array(
				'image' => $image,
				'pointList' => $pointList,
				'centroid' => $cent,
				'id_player' => $id_player
			));
			
		}
                
                // Console debuffing
                function debug_to_console( $data ) {
                    $output = $data;
                    if ( is_array( $output ) )
                        $output = implode( ',', $output);

                    echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
                }
		
	}
