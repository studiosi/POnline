<?php
	namespace TU\Utils;
	
	use Symfony\Component\Security\Core\User\UserInterface;
	
	class DUser implements UserInterface  {
	
		private $username;
		private $password;
		private $salt;
		private $roles;
	
		public function __construct($username, $password, $salt, array $roles = NULL) {
	
			$this->username = $username;
			$this->password = $password;
			$this->salt = $salt;
			$this->roles = $roles;
	
		}
	
		public function getRoles() {
	
			return array('ROLE_ALL');
	
		}
	
		public function getPassword() {
	
			return $this->password;
	
		}
	
		public function getSalt() {
	
			return $this->salt;
	
		}
	
		public function getUsername() {
	
			return $this->username;
	
		}
	
		public function eraseCredentials() {
	
			return;
	
		}
	
	}