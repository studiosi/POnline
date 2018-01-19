<?php

	namespace TU\Controllers;

	use Silex\Application;
	use TU\DAO\ImageDAO;
	use TU\DAO\PlayerDAO;
	use RandomLib;
	use SecurityLib;
	use Symfony\Component\HttpFoundation\Request;
        use TU\Utils\MathUtils;
        use TU\Utils\Equation;
        use TU\Utils\Ellipse;
	use TU\Utils\FormatUtils;
        use TU\Utils\Ransac;
	
	class AdminController {
		
		private $TOKEN_BAN_UNBAN = 'token_ban_unban';
                
                public function getSignup(Application $app) {
			
			return $app['twig']->render('signup.twig');
			
		}
		
		public function postSignup(Application $app, Request $req) {
			
			$username = $req->get('username');
			$pwd = $req->get('pwd');
			$pwd_repeat = $req->get('pwd_repeat');
			
			$pdao = new PlayerDAO();
			
			$errors = array();			
			
			// Username
			if(strlen($username) == 0) {
			
				$errors[] = "Username is empty";
			
			}			
			elseif (preg_match("/^[0-9A-Za-z_]+$/", $username) == 0) {
			
				$errors[] = "Username can only contain ASCII letters a-z (smalls and caps), numbers and the underscore";
			
			}			
			elseif($pdao->existsUsername($app, $username)) {
				
				$errors[] = "Username already exists";
				
			}
			
			// Password
			if(strlen($pwd) == 0) {
				
				$errors[] = "Password is empty";
				
			}			
			if(strcmp($pwd, $pwd_repeat) != 0) {
				
				$errors[] = "Passwords do not match";
				
			}
			
			// User creation / error display
			if(count($errors) > 0) {
				
				$data = array(
					'username' => $username
				);
				
				return $app['twig']->render('signup.twig', array('errors' => $errors, 'data' => $data));
				
			}
			else {
				
				$pdao->createAdmin($app, $username, $pwd);
				return $app->redirect($app['url_generator']->generate('index'));
				
			}
			
		}
		
		public function showMainMenu(Application $app) {
				
			$imDAO = new ImageDAO();
			$images = $imDAO->getAllImages($app);
                        $pointsraw = $imDAO->getAllClicks($app);
                        
                        //$x = 0;
                        //$info = array();
                        /*
                        $header = array('id', 'name', 'clicks');
                        // Output CSV file with the image params
                        $fp = fopen('/params.csv','w');
                        //add BOM to fix UTF-8 in Excel
                        //fputs($csv, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
                        
                        fputcsv($fp, $header); 
			foreach($images as $image) {
                            $i = 0;
                            while ($i < 5) {
                                // REMOVE BANNED PTS
                                    $x = $x+1;

                                $i = $i + 1;


                                $pointsraw = $imDAO->getAllClicksImage($app, $image['id']);
                                if (count($pointsraw) > 0) {
                                //var_dump($pointsraw);
                                $ransac = new Ransac;
                                $points = $ransac->ransacAlg($pointsraw);

                                $centroid = array('x' => 0, 'y' => 0);

                                    Equation::setfrompoints($points);

                                $centroid = Equation::getCenter();		


                                $ellipse_params = Equation::getEllipseParams();


                                $axis = Equation::getAxisLength();





                                $angle = Equation::getAngle();

                                $imcent = array_merge($image,$centroid);
                                $imcent = array_merge($imcent,$ellipse_params);
                                fputcsv($fp, $imcent);
                                }
                            }
                        }
                        fclose($fp);*/ 
                        /*
                        $header = array('id', 'name', 'clicks');
                        // Output CSV file with the image click params
                        $fp = fopen('/clicks.csv','w');
                        //add BOM to fix UTF-8 in Excel
                        //fputs($csv, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
                        
                        fputcsv($fp, $header); 
			


                        $pointsraw = $imDAO->getAllClicks($app);
                        foreach($pointsraw as $point) {
                            fputcsv($fp, $point);
                        }
                                
                        
                        fclose($fp); */
                        //var_dump($info);
                        //var_dump($clicks); 
			$pDAO = new PlayerDAO();
			$players = $pDAO->getAllPlayers($app);
			
			return $app['twig']->render('list.twig', array(
				'images' => $images,
				'players' => $players
			));
                        
				
		}
		
		public function showPlayerAdminMenu(Application $app, $id) {
			
			$imDAO = new ImageDAO();
			$images = $imDAO->getAllPlayerImages($app, $id);
			
			$pDAO = new PlayerDAO();
			$player = $pDAO->getPlayerById($app, $id);
			
			$f = new RandomLib\Factory();
			$g = $f->getGenerator(new SecurityLib\Strength(SecurityLib\Strength::MEDIUM));
			$token = $g->generateString(64);
			$app['session']->set($this->TOKEN_BAN_UNBAN, $token);
			
			return $app['twig']->render('admin_player.twig', array(
				'player' => $player,
				'images' => $images,
				'token' => $token
			));
			
		}
		
		public function userOp(Application $app, Request $req) {
			
			$t = $req->get('t');
			$t_s = $app['session']->get($this->TOKEN_BAN_UNBAN);
			
			if($t != $t_s) {
				$app->abort(400, "Invalid request");
			}
			
			var_dump($t);
			var_dump($t_s);
			
			$pDAO = new PlayerDAO();
			
			$op = $req->get('op');
			$user_id = $req->get('id');
			
			$pDAO->userOp($app, $op, $user_id);
			
			return "";
			
			
		}
		
	}
