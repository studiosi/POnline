<?php

	namespace TU\Utils;
	
	class FormatUtils {
		
		public static function getFormattedOpePoints($points) {
			
			$pointList = array();
			foreach($points as $point) {
                            if ($point['status'] == 'OPE') {
				$pointList[] = array( 'x' => $point['x'], 'y' => $point['y']);
                        }
			}
			
			return $pointList;
			
		}
                
                // For the timestamps function
                public static function getFormattedOpeStamps($points) {
			
			$pointList = array();
			foreach($points as $point) {
                            if ($point['status'] == 'OPE') {
				$pointList[] = array('pid' => $point['id_player'], 'ts' => $point['click_time']);
                        }
			}
			
			return $pointList;
			
		}
                
                public static function getFormattedPoints($points) {
			
			$pointList = array();
			foreach($points as $point) {
                            $pointList[] = array( 'x' => $point['x'], 'y' => $point['y']);
			}
			
			return $pointList;
			
		}
                
                // Data for analysing clicks of images
                public static function getFormattedClicks($points) {
			
			$pointList = array();
			foreach($points as $point) {
                            $pointList[] = array( 'id' => $point['id'], 'id_photo' => $point['id_photo'], 'id_player' => $point['id_player'] );
			}
			
			return $pointList;
			
		}
		
		public static function getJavascriptSerializedPoints($formatted_points, $centroid = false) {			
			
			if(count($formatted_points) == 0) {
				
				if($centroid) {
					return "[]";
				}
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


