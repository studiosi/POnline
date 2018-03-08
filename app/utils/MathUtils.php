<?php

	namespace TU\Utils;
	
	class MathUtils {
		
		private static $POINTS_PER_IMAGE = 110;
		private static $MAX_ACCEPTABLE_STD_DEVS = 2;
		
                // Calculate current progress of points / total needed points
		public static function calculateCurrentPercentage($clicks, $n_images) {
			
			return round(($clicks / MathUtils::calculateTotal($n_images)) * 100, 2);
			
		}
		
                // Return if the point is close enough the centroid
		public static function isAcceptablePoint($pointList, $centroid, $newPoint, $distance) {
			
			$stdDev = MathUtils::calculatePointsStdDev($pointList, $centroid);
			
			return $distance <= (MathUtils::$MAX_ACCEPTABLE_STD_DEVS * $stdDev);

		}
		
                // Return standard deviation of points distance from centroid
		public static function calculatePointsStdDev($points, $centroid) {
			
			$distances = array();
			foreach ($points as $point) {
				$distances[] = MathUtils::calculateDistance($point, $centroid);
			}
			
			return MathUtils::calculateStdDev($distances);
			
		}
		
                // Return total needed points according to points in the database and the set
                // $POINTS_PER_IMAGE class attribute
		public static function calculateTotal($n_images) {
			
			return $n_images * MathUtils::$POINTS_PER_IMAGE;
			
		}
		
                // Return standard deviation of $values
		public static function calculateStdDev($values) {
			
			$valuesMean = MathUtils::calculateMean($values);
			$devs = MathUtils::calculateSquaredDevs($values, $valuesMean);
			$stdDev = MathUtils::calculateMean($devs);
			
			return $stdDev;
			
		}
		
                // Return squared deviations of $values
		public static function calculateSquaredDevs($values, $mean) {
			
			if(count($values) == 0 || is_nan($mean)) {
			
				return null;
			
			}

			$devs = array();
			
			foreach ($values as $value) {
				
				$devs[] = pow($value - $mean, 2);
				
			}
		
			return $devs;
			
		}
		
                // Return mean of $values
		public static function calculateMean($values) {
			
			if(count($values) == 0) {
				
				return null;
				
			}
			
			$count = 0;
			$total = 0;
			
			foreach ($values as $value) {
				
				$count += 1;
				$total += $value;
				
			}
				
			return $total / $count;
			
		}
		
		// Distance is always positive
                // Return euclidean distance between $p1 and $p2
		public static function calculateDistance($p1, $p2) {
			
			return sqrt(pow($p2['x'] - $p1['x'], 2) + pow($p2['y'] - $p1['y'], 2));
			
		}
		
		// Mean of all coordinates
		public static function calculateCentroid($pointList) {
			
			$count = count($pointList);
			$xSum = 0;
			$ySum = 0;
			
			if(count($pointList) == 0) {
				
				return null;
				
			}
			
			foreach($pointList as $point) {
				
				$xSum += $point['x'];
				$ySum += $point['y'];
				
			}
			
			return array( 'x' => round(($xSum / $count), 0), 'y' => round(($ySum / $count),0));
			
		}
		
	}