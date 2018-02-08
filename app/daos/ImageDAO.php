<?php

namespace TU\DAO;

use Silex\Application;
use TU\Utils\FormatUtils;

class ImageDAO {

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

	public function getImageById(Application $app, $id) {
		
		$qb = $app['db']->createQueryBuilder();
		
		$qb->select('*')
		->from('images_count')
		->where(
			$qb->expr()->eq('id', $id)
		);
		
		return $app['db']->fetchAssoc($qb->getSQL());
		
	}
	
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
        
        // For timestamps
        public function getAllStampsUser(Application $app, $id) {
		
		$qb = $app['db']->createQueryBuilder();
		$ope = "OPE";
		$qb->select('c.id', 'c.id_photo', 'c.id_player', 'c.click_time', 'c.distance', 'i.id', 'i.status')
		->from('clicks', 'c')   
                ->innerJoin('c', 'players', 'i', 'c.id_player = i.id')        
		->where(
                    
			$qb->expr()->eq('c.id_player', $id)   
                        
		);
               
		$points = $app['db']->fetchAll($qb->getSQL());
		
		return FormatUtils::getFormattedOpeStamps($points);
		
	}
	
	public function getAllImages(Application $app) {
		
		$qb = $app['db']->createQueryBuilder();
		
		$qb->select('*')
		->from('images_count');
		
		return $app['db']->fetchAll($qb->getSQL());
		
	}
        
        // Gets player inputted points for the admin user view
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
	
	public function getTotalClicks(Application $app) {
			
		$qb = $app['db']->createQueryBuilder();
			
		$qb->select('total')
		->from('total_clicks');
			
		return $app['db']->fetchColumn($qb->getSQL(), array(), 0);
			
	}
	
	public function getNumberImages(Application $app) {
		
		$qb = $app['db']->createQueryBuilder();
			
		$qb->select('count(*)')
		->from('images_count');
			
		return $app['db']->fetchColumn($qb->getSQL(), array(), 0);
		
	}
	
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
        
        // For this we don't care if the user has been banned or not
	public function getAllClicks(Application $app) {
		
		$qb = $app['db']->createQueryBuilder();
		
		$qb->select('*')
		->from('clicks');
		
		
		$points = $app['db']->fetchAll($qb->getSQL());
		
		return FormatUtils::getFormattedClicks($points);
		
	}
}