<?php

	namespace TU\Controllers;

	use Silex\Application;
	use TU\DAO\ImageDAO;
	use TU\DAO\PlayerDAO;
	use RandomLib;
	use SecurityLib;
	use Symfony\Component\HttpFoundation\Request;
	
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
			
			$pDAO = new PlayerDAO();
			$players = $pDAO->getAllPlayers($app);
			
			return $app['twig']->render('list.twig', array(
				'images' => $images,
				'players' => $players
			));
				
		}
		
		public function showPlayerAdminMenu(Application $app, $id) {
			
			$imDAO = new ImageDAO();
			$images = $imDAO->getAllImages($app);
			
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
