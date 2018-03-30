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
        use TU\Utils\Ransac;
				
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
			
			$value = $app['session']->get($this->NUMBER_CLICKS_SESSION, -1);
			if($value == -1) {
				
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
			
                        // Lots of unecessary stuff for POnline2 as it accepts every point
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
                        // Get the image object with id
			$image = $imDAO->getImageById($app, $id);
                        // Get all point objects input into the image
			$pointsraw = $imDAO->getAllClicksImage($app, $id);
                        
                        /* for testing 
                        $pointsraw_unfiltered = $imDAO->getAllClicksImage($app, $id);
                        $filter = 1;
                        $pointsraw_tmp = array_unique($pointsraw_unfiltered,SORT_REGULAR);
                        $pointsraw = array_values($pointsraw_tmp);
                        shuffle($pointsraw);    
                        $n = count($pointsraw);
                        $chunked1 = array_slice($pointsraw, 0, $n / 2);
                        $chunked2 = array_slice($pointsraw, $n / 2);
                        $array_len = min(count($chunked1), count($chunked2));
                        var_dump(count($chunked1));
                        $len = ceil(max(5, $array_len*$filter));
                        //$len = 5;
                        //var_dump($len);
                        $tmp1 = array_slice($chunked1, 0, $len);
                        $tmp2 = array_slice($chunked2, 0, $len);
                        $pointsraw = $tmp1; */
                        /* Testing end */
                        //var_dump($pointsraw);
                        
                        $ransac = new Ransac;
                        // Fit the points with RANSAC
                        $points = $ransac->ransacAlg($pointsraw);
                        
                        // Setup variable for outlier points
                        $outlier_points = array();
                        $j = 0;
                        
                        // Input outlier points into the outlier_points array
                        for ($i = 0; $i < count($pointsraw); $i++) {
                            if (!in_array($pointsraw[$i], $points)) {
                                $outlier_points[$j] = $pointsraw[$i];
                                $j++; 
                            }
                        } 
                        
                        $centroid = array('x' => 0, 'y' => 0);
                        if (count($points) > 0) {
                            // Get ellipse params with the inlier points
                            Ellipse::setfrompoints($points);
                            
                            // Get centroid of the ellipse
                            $centroid = Ellipse::getCenter();		
			}
                        
                        $axis = [0,1];
                        if (count($points) > 0) {
                            // Get major and minor axis length of the ellipse
                            $axis = Ellipse::getAxisLength();
                        }
                        
                        $angle = 0;
                        if (count($points) > 0) {
                            // Get the angle of the ellipse 
                            $angle = Ellipse::getAngle();
                        }
                        
                        // Serialize inlier points
			$pointList = FormatUtils::getJavascriptSerializedPoints($points);
                        // Serialize outlier points
                        $outlierList = FormatUtils::getJavascriptSerializedPoints($outlier_points);
                        // Serialize centroid
			$cent = FormatUtils::getJavascriptSerializedPoints(array($centroid), true);
                        $axisa = $axis[0];
                        $axisb = $axis[1];
                        
                        // Return show.twig page with the calculated parameters
			return $app['twig']->render('show.twig',
			array(
				'image' => $image,
				'pointList' => $pointList,
                                'outlierList' => $outlierList,
				'centroid' => $cent,
                                'axisa' => $axisa,
                                'axisb' => $axisb,
                                'angle' => $angle
                        ));
			
		}

		public function showPlayerPoints(Application $app, $id_player, $id_photo) {
			
			$imDAO = new ImageDAO();
			
			$image = $imDAO->getImageById($app, $id_photo);
			$user_points_raw = $imDAO->getAllClicksUserImage($app, $id_player, $id_photo);		
			$pointList = FormatUtils::getJavascriptSerializedPoints($user_points_raw);
			    
                        $ransac = new Ransac;
                        // Fit the points with RANSAC
                        $points = $ransac->ransacAlg($user_points_raw);
                        
                        // Setup variable for outlier points
                        $outlier_points = array();
                        $j = 0;
                        // Input outlier points into the outlier_points array
                        for ($i = 0; $i < count($user_points_raw); $i++) {
                            if (!in_array($user_points_raw[$i], $points)) {
                                $outlier_points[$j] = $user_points_raw[$i];
                                $j++; 
                            }
                        } 
                        
                        $centroid = array('x' => 0, 'y' => 0);
                        if (count($points) > 0) {
                            // Get ellipse params with the inlier points
                            Ellipse::setfrompoints($points);
                            // Get centroid of the ellipse
                            $centroid = Ellipse::getCenter();		
			}
                        
                        $axis = [0,1];
                        if (count($points) > 0) {
                            // Get major and minor axis length of the ellipse
                            $axis = Ellipse::getAxisLength();
                        }
                        
                        
                        $angle = 0;
                        if (count($points) > 0) {
                            // Get the angle of the ellipse 
                            $angle = Ellipse::getAngle();
                        }
                        
                        // Serialize inlier points
			$pointList = FormatUtils::getJavascriptSerializedPoints($points);
                        // Serialize outlier points
                        $outlierList = FormatUtils::getJavascriptSerializedPoints($outlier_points);
                        // Serialize centroid
			$cent = FormatUtils::getJavascriptSerializedPoints(array($centroid), true);
                        $axisa = $axis[0];
                        $axisb = $axis[1];
			
			return $app['twig']->render('show.twig',
			array(
				'image' => $image,
				'pointList' => $pointList,
                                'outlierList' => $outlierList,
				'centroid' => $cent,
                                'axisa' => $axisa,
                                'axisb' => $axisb,
                                'angle' => $angle,
                                'id_player' => $id_player
                        ));
			
		}
		
	}
