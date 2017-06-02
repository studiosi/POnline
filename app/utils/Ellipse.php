<?php

    namespace TU\Utils;
    use TU\Utils\Equation;
    use TU\Utils\Ellipse;
    use TU\controllers\ImageController;

    class Ellipse{
            /*    private static $x;
                private static $y;
                private static $majorAxis;
                private static $minorAxis;
                private static $angle;
                private static $f1_x;
                private static $f1_y;
                private static $f2_x;
                private static $f2_y;
            */
    


            
            // 3x3 matrix helpers
	public static function determinant($B) {
		return $B[0][0] * $B[1][1] * $B[2][2]
		    + $B[0][1] * $B[1][2] * $B[2][0]
		    + $B[0][2] * $B[1][0] * $B[2][1]
		    - $B[0][2] * $B[1][1] * $B[2][0]
		    - $B[0][1] * $B[1][0] * $B[2][2]
		    - $B[0][0] * $B[1][2] * $B[2][1];
	}
        
        public static function inverse($B) {
		$d = Ellipse::determinant($B);
		return [
                    [($B[1][1] * $B[2][2] - $B[1][2] * $B[2][1]) / $d,
			($B[0][2] * $B[2][1] - $B[0][1] * $B[2][2]) / $d,
			($B[0][1] * $B[1][2] - $B[0][2] * $B[1][1]) / $d],
			[($B[1][2] * $B[2][0] - $B[1][0] * $B[2][2]) / $d,
			($B[0][0] * $B[2][2] - $B[0][2] * $B[2][0]) / $d,
			($B[0][2] * $B[1][0] - $B[0][0] * $B[1][2]) / $d],
                        [($B[1][0] * $B[2][1] - $B[1][1] * $B[2][0]) / $d,
			($B[0][1] * $B[2][0] - $B[0][0] * $B[2][1]) / $d,
			($B[0][0] * $B[1][1] - $B[0][1] * $B[1][0]) / $d]
                        ];
	}
        
        public static function multiply($A, $B) {
		return [[$A[0][0] * $B[0][0] + $A[0][1] * $B[1][0] + $A[0][2] * $B[2][0],
		$A[0][0] * $B[0][1] + $A[0][1] * $B[1][1] + $A[0][2] * $B[2][1],
		$A[0][0] * $B[0][2] + $A[0][1] * $B[1][2] + $A[0][2] * $B[2][2]],
		[$A[1][0] * $B[0][0] + $A[1][1] * $B[1][0] + $A[1][2] * $B[2][0],
		$A[1][0] * $B[0][1] + $A[1][1] * $B[1][1] + $A[1][2] * $B[2][1],
		$A[1][0] * $B[0][2] + $A[1][1] * $B[1][2] + $A[1][2] * $B[2][2]],
		[$A[2][0] * $B[0][0] + $A[2][1] * $B[1][0] + $A[2][2] * $B[2][0],
		$A[2][0] * $B[0][1] + $A[2][1] * $B[1][1] + $A[2][2] * $B[2][1],
		$A[2][0] * $B[0][2] + $A[2][1] * $B[1][2] + $A[2][2] * $B[2][2]]];
	}

	public static function transpose($B) {
		return [[$B[0][0], $B[1][0], $B[2][0]],
		[$B[0][1], $B[1][1], $B[2][1]],
		[$B[0][2], $B[1][2], $B[2][2]]];
	}
        
        public static function add($A, $B) {
		return [[$A[0][0] + $B[0][0], $A[0][1] + $B[0][1], $A[0][2] + $B[0][2]],
		[$A[1][0] + $B[1][0], $A[1][1] + $B[1][1], $A[1][2] + $B[1][2]],
		[$A[2][0] + $B[2][0], $A[2][1] + $B[2][1], $A[2][2] + $B[2][2]]];
	}

	public static function trace($A) { return $A[0][0] + $A[1][1] + $A[2][2]; }

	public static function scale($A, $k) {
		return [[$k * $A[0][0], $k * $A[0][1], $k * $A[0][2]],
		[$k * $A[1][0], $k * $A[1][1], $k * $A[1][2]],
		[$k * $A[2][0], $k * $A[2][1], $k * $A[2][2]]];
	}
        
        public static function eigenvalues($A) {
		$q = Ellipse::trace($A) / 3;
		$K = Ellipse::add($A, [[-$q, 0, 0],[0, -$q, 0],[0, 0, -$q]]);
		$p = sqrt(Ellipse::trace(Ellipse::multiply($K,$K))/6);
		$d = Ellipse::determinant(Ellipse::scale($K, 1 / $p));  
                $pi = acos(-1);
                // pi() or acos(-1) for getting the value of pi
		$phi;                        // SO FAR WORKS
		if ($d <= -2) {
			$phi = $pi / 3;
		} else if ($d >= 2) {
			$phi = 0;
		} else {
			$phi = acos($d / 2) / 3;
		}
                // PHI IS VALUE OF NAN
		ImageController::debug_to_console("ellipse debug: " . $phi . acos(-1));
		return [$q + 2 * $p * cos($phi),
		$q + 2 * $p * cos($phi + (2 * $pi / 3)),
		$q + 2 * $p * cos($phi + (4 * $pi / 3))];
	}
        
        public static function nullspace($G) {
		$k1 = -$G[2][0]/$G[2][2];
		$k2 = -$G[2][1]/$G[2][2];

		$y = -($G[1][0]+$G[1][2]*$k1)/($G[1][1]+$G[1][2]*$k2);
		$z = $k1 + $k2*$y;
		$n = sqrt(1+$y*$y+$z*$z);
		
		return [1/$n, $y/$n, $z/$n];
	}
        
        
        }