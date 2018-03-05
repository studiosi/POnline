<?php

namespace TU\DAO;

use Silex\Application;
use TU\Utils\FormatUtils;

class ImageDAO {

        // Returns the image object with least clicks
	public function getLessClickedImage(Application $app) {

		$qb = $app['db']->createQueryBuilder();
                
		$qb->select('min(n_clicks)')
		->from('images_count');
		
		$min_clicks = $app['db']->fetchColumn($qb->getSQL(), array(), 0);
		
		$qb->select('*')
		->from('images_count')
		->where(
			$qb->expr()->eq('n_clicks', $min_clicks)	
		);		
		
		$images = $app['db']->fetchAll($qb->getSQL());
		
		if(count($images) == 1) {
			return $images[0];
		}
		else {
			return $images[mt_rand(0, count($images) - 1)];
		}

	}
	
        // Inserts one given point into the db
	public function insertPoint(Application $app, $id, $newPoint, $id_player, $distance, $human) {
		
		$human ? $h = 1 : $h = 0;
		
		// Insert click
		$app['db']->insert('clicks', array(
			'id_photo' => $id,
			'x' => $newPoint['x'],
			'y' => $newPoint['y'],
			'id_player' => $id_player,
			'human_generated' => $h,
			'distance' => $distance
		));		
		
	}

        // Returns an image object with the requested image id
	public function getImageById(Application $app, $id) {
		
		$qb = $app['db']->createQueryBuilder();
		
		$qb->select('*')
		->from('images_count')
		->where(
			$qb->expr()->eq('id', $id)
		);
		
		return $app['db']->fetchAssoc($qb->getSQL());
		
	}
	
        // Returns all the clicks from and image with the requested image id
	public function getAllClicksImage(Application $app, $id) {
		
		$qb = $app['db']->createQueryBuilder();
		$ope = "OPE";
		$qb->select('c.id', 'c.id_photo', 'c.x', 'c.y', 'c.id_player', 'c.human_generated', 'c.distance', 'i.id', 'i.status')
		->from('clicks', 'c')   
                ->innerJoin('c', 'players', 'i', 'c.id_player = i.id')        
		->where(
                    
			$qb->expr()->eq('c.id_photo', $id)   
                        
		);
               
		$points = $app['db']->fetchAll($qb->getSQL());
		
		return FormatUtils::getFormattedOpePoints($points);
		
	}
        
        // Returns all the timestamps of user clicks with the requested user id
        public function getAllStampsUser(Application $app, $id_player) {
		
		$qb = $app['db']->createQueryBuilder();
		$ope = "OPE";
		$qb->select('c.id', 'c.id_photo', 'c.id_player', 'c.click_time', 'c.distance', 'i.id', 'i.status')
		->from('clicks', 'c')   
                ->innerJoin('c', 'players', 'i', 'c.id_player = i.id')        
		->where(
                    
			$qb->expr()->eq('c.id_player', $id_player)   
                        
		);
               
		$points = $app['db']->fetchAll($qb->getSQL());
		
		return FormatUtils::getFormattedOpeStamps($points);
		
	}
	
        // Returns all the image objects from database
	public function getAllImages(Application $app) {
		
		$qb = $app['db']->createQueryBuilder();
		
		$qb->select('*')
		->from('images_count');
		
		return $app['db']->fetchAll($qb->getSQL());
		
	}
        
        // Gets player inputted points for the admin user view with given player id
        public function getAllPlayerImages(Application $app, $id_player) {
                $qb = $app['db']->createQueryBuilder();
              
                $qb->select('DISTINCT i.filename', 'i.id', 'i.n_clicks')
                ->from('clicks', 'c')   
                ->innerJoin('c', 'images_count', 'i', 'c.id_photo = i.id')        
                ->where(

                    $qb->expr()->eq('c.id_player', $id_player)   
                   
		);
            
		return $app['db']->fetchAll($qb->getSQL());
		
	}
	
        // returns all valid points with the requested player id and image id 
        // for the requested image and player
	public function getClicksUserImage(Application $app, $id_photo, $id_player) {
		
		$qb = $app->createQueryBuilder();
		
		$qb->select('*')
		->from('valid_clicks')
		->where(
			$qb->expr()->andX(
				$qb->expr()->eq('id_photo', $id_photo),
				$qb->expr()->eq('id_player', $id_player)
			)	
		);
		
		$points = $app['db']->fetchAll($qb->getSQL());
		
		return FormatUtils::getFormattedPoints($points);
		
	}
	
        // Returns the total click amount of the system
	public function getTotalClicks(Application $app) {
			
		$qb = $app['db']->createQueryBuilder();
			
		$qb->select('total')
		->from('total_clicks');
			
		return $app['db']->fetchColumn($qb->getSQL(), array(), 0);
			
	}
	
        // Returns the image count the database holds
	public function getNumberImages(Application $app) {
		
		$qb = $app['db']->createQueryBuilder();
			
		$qb->select('count(*)')
		->from('images_count');
			
		return $app['db']->fetchColumn($qb->getSQL(), array(), 0);
		
	}
	
	// Returns all valid points with the requested player id and image id
        // For this we don't care if the user has been banned or not
	public function getAllClicksUserImage(Application $app, $id_player, $id_photo) {
		
		$qb = $app['db']->createQueryBuilder();
		
		$qb->select('*')
		->from('clicks')
		->where(
			$qb->expr()->andX(
				$qb->expr()->eq('id_photo', $id_photo),
				$qb->expr()->eq('id_player', $id_player)
			)
		);
		
		$points = $app['db']->fetchAll($qb->getSQL());
		
		return FormatUtils::getFormattedPoints($points);
		
	}
        
        // returns all the point objects of each click without considering 
        // if they are valid or not nor if the user is operational or banned
	public function getAllClicks(Application $app) {
		
		$qb = $app['db']->createQueryBuilder();
		
		$qb->select('*')
		->from('clicks');
		
		
		$points = $app['db']->fetchAll($qb->getSQL());
		
		return FormatUtils::getFormattedClicks($points);
		
	}
}