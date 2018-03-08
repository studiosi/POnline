<?php

	namespace TU\Utils;
	
	class FormatUtils {
		
                // Get point list from operational users only
                // Ignores points that are input by banned users
		public static function getFormattedOpePoints($points) {
			
			$pointList = array();
			foreach($points as $point) {
                            if ($point['status'] == 'OPE') {
				$pointList[] = array( 'x' => $point['x'], 'y' => $point['y']);
                        }
			}
			
			return $pointList;
			
		}
                
                // Returns player ids and timestamps of clicks as an array from operational users
                public static function getFormattedOpeStamps($points) {
			
			$pointList = array();
			foreach($points as $point) {
                            if ($point['status'] == 'OPE') {
				$pointList[] = array('pid' => $point['id_player'], 'ts' => $point['click_time']);
                        }
			}
			
			return $pointList;
			
		}
                
                // Format $points array to have only x and y keys
                public static function getFormattedPoints($points) {
			
			$pointList = array();
			foreach($points as $point) {
                            $pointList[] = array( 'x' => $point['x'], 'y' => $point['y']);
			}
			
			return $pointList;
			
		}
                
                // Data for analysing clicks of images
                // Format $points to have only click id, image id and player id
                public static function getFormattedClicks($points) {
			
			$pointList = array();
			foreach($points as $point) {
                            $pointList[] = array( 'id' => $point['id'], 'id_photo' => $point['id_photo'], 'id_player' => $point['id_player'] );
			}
			
			return $pointList;
			
		}
		
                // Formats input for twigs' JavaScript
		public static function getJavascriptSerializedPoints($formatted_points, $centroid = false) {			
			
			if(count($formatted_points) == 0) {
				
				if($centroid) {
                                        // If empty points and centroid, return an empty array
					return "[]";
				}
                                // If empty points and not centroid, return empty two dimensional array
				else {
					return "[[]]";
				}
				
			}
			
			if(count($formatted_points) >= 1 && !$centroid) {
				$pointList = '[';
			}
			else {
				$pointList = '';
			}
			
			foreach ($formatted_points as $point) {
				
				$pointList .= '[' . $point['x'] . ',' . $point['y'] . ']';
				
				if(count($formatted_points) > 1) {
					$pointList .= ',';
				}
				
			}
			
			if(count($formatted_points) >= 1 && !$centroid) {
				$pointList .= ']';
			}
			
			return $pointList;
			
		}
		
	}


