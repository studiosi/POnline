<?php
	namespace TU\DAO;
	
	use Silex\Application;
	use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
	use RandomLib;
	use SecurityLib;
		
	class PlayerDAO {
		
		public static $STATUS = array(
			'OPERATIONAL' => 'OPE',
			'BANNED' => 'BAN'
		);
		
		public static $N_LEADERBOARD = 10;
		
		public static $SESSION_PLAYER_ID = 'SESSION_PLAYER';
		
		public function existsUsername(Application $app, $username) {
			
			$qb = $app['db']->createQueryBuilder();
			
			$qb->select('count(id)')
			->from('players')
			->where(
				$qb->expr()->eq('username', '\'' . $username . '\'')
			);
		
			$n = $app['db']->fetchColumn($qb->getSQL(), array(), 0);
			
			return $n != 0;
			
		}
		
		public function createPlayer(Application $app, $username, $password) {
			
			$f = new RandomLib\Factory();
			$g = $f->getGenerator(new SecurityLib\Strength(SecurityLib\Strength::MEDIUM));			
			$salt = $g->generateString(64);			
			$encoder = new MessageDigestPasswordEncoder();
			$hash = $encoder->encodePassword($password, $salt);
			
			$data = array(
				'username' => $username,
				'password' => $hash,
				'salt' => $salt,
				'status' => PlayerDAO::$STATUS['OPERATIONAL']			
			);
			
			$app['db']->insert('players', $data);
			
		}
		
		public function checkLoginPlayer(Application $app, $username, $password) {
			
			$qb = $app['db']->createQueryBuilder();
			
			$qb->select('*')
			->from('players')
			->where(
				$qb->expr()->eq('username', '\'' . $username . '\'')
			);
			
			$player = $app['db']->fetchAssoc($qb->getSQL());
			
			if(strcmp($player['status'], PlayerDAO::$STATUS['BANNED']) == 0) {
				
				return false;
				
			}
			
			$f = new RandomLib\Factory();
			$g = $f->getGenerator(new SecurityLib\Strength(SecurityLib\Strength::MEDIUM));
			$salt = $player['salt'];
			$encoder = new MessageDigestPasswordEncoder();
			$hash = $encoder->encodePassword($password, $salt);
			
			return (strcmp($hash, $player['password']) == 0);
			
		}
		
		public function doLoginUserSession(Application $app, $username) {
			
			$qb = $app['db']->createQueryBuilder();
			
			$qb->select('*')
			->from('players')
			->where(
				$qb->expr()->eq('username', '\'' . $username . '\'')
			);
				
			$player = $app['db']->fetchAssoc($qb->getSQL());
			
			$app['session']->set(PlayerDAO::$SESSION_PLAYER_ID, $player);
			
		}
		
		public function checkIfUserLoggedIn(Application $app) {
			
			return (!empty($app['session']->get(PlayerDAO::$SESSION_PLAYER_ID)));
			
		}
		
		public function getLeaderboard(Application $app) {
			
			$qb = $app['db']->createQueryBuilder();
			
			$qb->select('*')
			->from('leaderboard')
			->where(
				$qb->expr()->eq('status', '\'' . PlayerDAO::$STATUS['OPERATIONAL'] . '\'')
			)
			->orderBy('N', 'DESC')
			->setMaxResults(PlayerDAO::$N_LEADERBOARD);
			
			return $app['db']->fetchAll($qb->getSQL());
			
		}
		
		public function getAllPlayers(Application $app) {
			
			$qb = $app['db']->createQueryBuilder($app);
			
			$qb->select('*')
			->from('players', 'p')
			->join('p', 'leaderboard', 'l', 'p.id = l.id_player');
			
			return $app['db']->fetchAll($qb->getSQL());
			
		}
		
		public function getPlayerById(Application $app, $id) {
			
			$qb = $app['db']->createQueryBuilder($app);
			
			$qb->select('*')
			->from('players', 'p')
			->where(
					$qb->expr()->eq('id', $id)
			);
			
			return $app['db']->fetchAssoc($qb->getSQL());
			
		}
		
		public function getLeaderNClicks(Application $app) {
			
			$qb = $app['db']->createQueryBuilder($app);
			
			$qb->select('N')
			->from('leaderboard', 'l')
			->where(
				$qb->expr()->eq('status', '\'' . PlayerDAO::$STATUS['OPERATIONAL'] . '\'')
			)
			->orderBy('N', 'desc')
			->setMaxResults(1);
			
			return $app['db']->fetchColumn($qb->getSQL(), array(), 0);
			
		}
		
		public function getNClicksByID(Application $app, $user_id) {
			
			$qb = $app['db']->createQueryBuilder($app);
				
			$qb->select('N')
			->from('leaderboard', 'l')
			->where(
				$qb->expr()->eq('id_player', $user_id)
			);

			return $app['db']->fetchColumn($qb->getSQL(), array(), 0);
			
		}
		
		public function userOp(Application $app, $op, $user_id) {
			
			$nVal = null;
			if ($op == 'BAN') {
				$nVal = $this::$STATUS['BANNED'];
			}
			elseif ($op == 'OPE') {
				$nVal = $this::$STATUS['OPERATIONAL'];
			}
			
			if(!is_null($nVal)) {
				
				$app['db']->update('players', array('status' => $nVal), array('id' => $user_id));
				
			}
			
			return;
			
		}
		
	}