<?php

        namespace TU\Utils;

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
    

            /* 
            public static function fitEllipseRANSAC($points, $count){
                $ellipse = new Ellipse;
                $count=0;
                $index[5];
                $match=false;
                for($i=0; $i<5; $i++){
                    do {
                        $match = false;
                        $index[$i]=rand()%$points.size();
                        for ($j=0; $j<$i;$j++){
                            if($index[$i] == $index[$j]){
                                $match = true;
                            }
                        }
                    } 
                    while($match);
                }
      
                
                $aData = new Matrix([
                    $points[$index[0]].$x * $points[$index[0]].$x, 2 * $points[$index[0]].$x * $points[$index[0]].$y, $points[$index[0]].
                    $y * $points[$index[0]].$y, 2 * $points[$index[0]].$x, 2 * $points[$index[0]].$y,

                    points[index[1]].x * points[index[1]].x, 2 * points[index[1]].x * points[index[1]].y, points[index[1]].
                    y * points[index[1]].y, 2 * points[index[1]].x, 2 * points[index[1]].y,

                    points[index[2]].x * points[index[2]].x, 2 * points[index[2]].x * points[index[2]].y, points[index[2]].
                    y * points[index[2]].y, 2 * points[index[2]].x, 2 * points[index[2]].y,

                    points[index[3]].x * points[index[3]].x, 2 * points[index[3]].x * points[index[3]].y, points[index[3]].
                    y * points[index[3]].y, 2 * points[index[3]].x, 2 * points[index[3]].y,

                    points[index[4]].x * points[index[4]].x, 2 * points[index[4]].x * points[index[4]].y, points[index[4]].
                    y * points[index[4]].y, 2 * points[index[4]].x, 2 * points[index[4]].y 
                ]);
                
            } */
            
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
		$d = determinant($B);
		return [[($B[1][1] * $B[2][2] - $B[1][2] * $B[2][1]) / $d,
			($B[0][2] * $B[2][1] - $B[0][1] * $B[2][2]) / $d,
			($B[0][1] * $B[1][2] - $B[0][2] * $B[1][1]) / $d],
			[($B[1][2] * $B[2][0] - $B[1][0] * $B[2][2]) / $d,
			($B[0][0] * $B[2][2] - $B[0][2] * $B[2][0]) / $d,
			($B[0][2] * $B[1][0] - $B[0][0] * $B[1][2]) / $d],
                        [($B[1][0] * $B[2][1] - $B[1][1] * $B[2][0]) / $d,
			($B[0][1] * $B[2][0] - $B[0][0] * $B[2][1]) / $d,
			($B[0][0] * $B[1][1] - $B[0][1] * $B[1][0]) / $d]];
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
		$q = trace(A) / 3;
		$K = add($A, [[-$q, 0, 0],[0, -$q, 0],[0, 0, -$q]]);
		$p = Math.sqrt(trace(multiply($K,$K))/6);
		$d = determinant(scale($K, 1 / $p));

		$phi;
		if ($d <= -2) {
			$phi = Math.PI / 3;
		} else if ($d >= 2) {
			$phi = 0;
		} else {
			$phi = Math.acos($d / 2) / 3;
		}
		
		return [$q + 2 * $p * Math.cos($phi),
		$q + 2 * $p * Math.cos($phi + (2 * Math.PI / 3)),
		$q + 2 * $p * Math.cos($phi + (4 * Math.PI / 3))];
	}
        
        public static function nullspace($G) {
		$k1 = -$G[2][0]/$G[2][2];
		$k2 = -$G[2][1]/$G[2][2];

		$y = -($G[1][0]+$G[1][2]*$k1)/($G[1][1]+$G[1][2]*$k2);
		$z = $k1 + $k2*$y;
		$n = Math.sqrt(1+$y*$y+$z*$z);
		
		return [1/$n, $y/$n, $z/$n];
	}
        
        
        }