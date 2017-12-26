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
                
                public static function getFormattedPoints($points) {
			
			$pointList = array();
			foreach($points as $point) {
                            $pointList[] = array( 'x' => $point['x'], 'y' => $point['y']);
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


