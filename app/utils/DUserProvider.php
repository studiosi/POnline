<?php
	namespace TU\Utils;
	
	use Symfony\Component\Security\Core\User\UserProviderInterface;
	use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
	use Symfony\Component\Security\Core\User\UserInterface;
	use Doctrine\DBAL\Connection;
	
	use TU\Utils\DUser;
	
	class DUserProvider implements UserProviderInterface  {
	
		// We have no access to the DAOs here
		private $conn;
	
		public function __construct(Connection $conn) {
	
			$this->conn = $conn;
	
		}
	
		public function loadUserByUsername($username) {
	
			$qb = $this->conn->createQueryBuilder();
	
			$qb->select('*')->from('users');
	
			$qb->where(
	
					$qb->expr()->like('UPPER(username)', '\'' . strtoupper($username) . '\'')
	
					);
	
			$sql = $qb->getSQL();
			$userData = $this->conn->fetchAssoc($sql);
	
			if(!$userData) {
	
				throw new UsernameNotFoundException(
						sprintf('Username "%s" does not exist', $username)
						);
	
			}
	
			return new DUser($userData['username'], $userData['password'], $userData['salt']);
	
		}
	
		public function refreshUser(UserInterface $user) {
	
			if(!$user instanceof DUser) {
	
				throw new UsernameNotFoundException(
	
						sprintf('Instances of "%s" are not supported.', get_class($user))
	
						);
	
			}
	
			return $this->loadUserByUsername($user->getUsername());
	
		}
	
		public function supportsClass($class) {
	
			return $class === 'TU\Utils\DUser';
	
		}
	
	}