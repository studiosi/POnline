<?php

namespace TU\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use TU\DAO\PlayerDAO;
	
	
	class PlayerController {
		
		public function getSignup(Application $app) {
			
			return $app['twig']->render('signup.twig');
			
		}
		
		public function postSignup(Application $app, Request $req) {
			
			$username = $req->get('username');
			$pwd = $req->get('pwd');
			$pwd_repeat = $req->get('pwd_repeat');
			$email = $req->get('email');
                        
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
                        
                        if (strlen($email) == 0) {
                            $email = "";
                        }
			
			// User creation / error display
			if(count($errors) > 0) {
				
				$data = array(
					'username' => $username
				);
				
				return $app['twig']->render('signup.twig', array('errors' => $errors, 'data' => $data));
				
			}
			else {
				
				$pdao->createPlayer($app, $username, $pwd, $email);
				return $app->redirect($app['url_generator']->generate('index'));
				
			}
			
		}

		public function getLoginPlayer(Application $app) {
			
			return $app['twig']->render('login_player.twig');
			
		}
		
		public function postLoginPlayer(Application $app, Request $req) {
			
			$username = $req->get('username');
			$pwd = $req->get('pwd');
			
			$pdao = new PlayerDAO();
			
			
			if(empty($username) || empty($pwd)) {
				
				return $app['twig']->render('login_player.twig', array('error' => 'Empty username or password.'));
				
			}
			elseif ($pdao->checkLoginPlayer($app, $username, $pwd)) {
				
				$pdao->doLoginUserSession($app, $username);
				return $app->redirect($app['url_generator']->generate('image'));
				
			}
			else {
				
				return $app['twig']->render('login_player.twig', array('error' => 'Bad credentials or banned user.'));
				
			}
			
			
		}
		
	}