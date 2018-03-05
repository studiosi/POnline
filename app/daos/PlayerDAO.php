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
		
                // Checks if the given username already exists in the db
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
		
                // Creates admin user to users table of the database
                public function createAdmin(Application $app, $username, $password) {
			
			$f = new RandomLib\Factory();
			$g = $f->getGenerator(new SecurityLib\Strength(SecurityLib\Strength::MEDIUM));			
			$salt = $g->generateString(64);			
			$encoder = new MessageDigestPasswordEncoder();
			$hash = $encoder->encodePassword($password, $salt);
			
			$data = array(
				'username' => $username,
				'password' => $hash,
				'salt' => $salt		
			);
			
			$app['db']->insert('users', $data);
			
		}
                
                // Creates player to players table of the database
		public function createPlayer(Application $app, $username, $password, $email) {
			
			$f = new RandomLib\Factory();
			$g = $f->getGenerator(new SecurityLib\Strength(SecurityLib\Strength::MEDIUM));			
			$salt = $g->generateString(64);			
			$encoder = new MessageDigestPasswordEncoder();
			$hash = $encoder->encodePassword($password, $salt);
			
			$data = array(
				'username' => $username,
                                'email' => $email,
				'password' => $hash,
				'salt' => $salt,
				'status' => PlayerDAO::$STATUS['OPERATIONAL']			
			);
			
			$app['db']->insert('players', $data);
			
		}
		
                // Checks if the players password match his hash and password
		public function checkLoginPlayer(Application $app, $username, $password) {
			
			$qb = $app['db']->createQueryBuilder();
			
			$qb->select('*')
			->from('players')
			->where(
				$qb->expr()->eq('username', '\'' . $username . '\'')
			);
			
			$player = $app['db']->fetchAssoc($qb->getSQL());
			
                        // If player is banned return false
			if(strcmp($player['status'], PlayerDAO::$STATUS['BANNED']) == 0) {
				
				return false;
				
			}
			
			$f = new RandomLib\Factory();
			$g = $f->getGenerator(new SecurityLib\Strength(SecurityLib\Strength::MEDIUM));
			$salt = $player['salt'];
			$encoder = new MessageDigestPasswordEncoder();
                        // Encode hash with the given password and queried salt
			$hash = $encoder->encodePassword($password, $salt);
			
                        // Return if the hash and the players password match or not
			return (strcmp($hash, $player['password']) == 0);
			
		}
		
                // Sets this class' session data for the player
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
		
                // Checks if the user is logged in
		public function checkIfUserLoggedIn(Application $app) {
					
			$value = $app['session']->get(PlayerDAO::$SESSION_PLAYER_ID, -1);
                        // Return false if user is logged in, true if not
			if($value == -1) {
				return false;
			}
			return true;
		}
		
                // Returns leaderboard with operational players. Leaderboard is in 
                // descending order according to amount of clicks by each player
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
		
                // Return all players’ data objects from inner join of players table and leaderboard view
		public function getAllPlayers(Application $app) {
			
			$qb = $app['db']->createQueryBuilder($app);
			
			$qb->select('*')
			->from('players', 'p')
			->join('p', 'leaderboard', 'l', 'p.id = l.id_player');
			
			return $app['db']->fetchAll($qb->getSQL());
			
		}
		
                // Return player's data object by id
		public function getPlayerById(Application $app, $id) {
			
			$qb = $app['db']->createQueryBuilder($app);
			
			$qb->select('*')
			->from('players', 'p')
			->where(
					$qb->expr()->eq('id', $id)
			);
			
			return $app['db']->fetchAssoc($qb->getSQL());
			
		}
		
                // Returns the leaderboard's leader's click amount
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
		
                // Checks how many points are left to input for the player to rise up one position in the board
		public function getNextNClicks(Application $app, $id_player, $n_clicks) {
			
			$qb = $app['db']->createQueryBuilder($app);
			
                        // Select click amount N from leaderboard view
			$qb->select('N')
			->from('leaderboard', 'l')
			->andWhere(	
                                // Set expression status to equal operational player
				$qb->expr()->eq('status', '\'' . PlayerDAO::$STATUS['OPERATIONAL'] . '\''),	
                                // Set expression click Number N to be greater than n_clicks 
				$qb->expr()->gt('N', $n_clicks),
                                // Set expression to exclude id_player
				$qb->expr()->neq('id_player', $id_player)
			)
                        // Order by N in ascending order
			->orderBy('N', 'asc')
                        // Set only one result to be query's max number of results
			->setMaxResults(1);
						
                        // Return the queried data object
			return $app['db']->fetchColumn($qb->getSQL(), array(), 0);
			
		}
		
                //Returns click count N of the queried user id 
		public function getNClicksByID(Application $app, $user_id) {
			
			$qb = $app['db']->createQueryBuilder($app);
				
			$qb->select('N')
			->from('leaderboard', 'l')
			->where(
				$qb->expr()->eq('id_player', $user_id)
			);

			return $app['db']->fetchColumn($qb->getSQL(), array(), 0);
			
		}
		
                // Update player operation status to BAN or OPE by $op to players db table
		public function userOp(Application $app, $op, $user_id) {
			
			$nVal = null;
			if ($op == 'BAN') {
				$nVal = $this::$STATUS['BANNED'];
			}
			elseif ($op == 'OPE') {
				$nVal = $this::$STATUS['OPERATIONAL'];
			}
			
			if(!is_null($nVal)) {
				// Update players status to players table of annotator database
				$app['db']->update('players', array('status' => $nVal), array('id' => $user_id));
				
			}
			
			return;
			
		}
		
	}