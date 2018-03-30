<?php
namespace TU\Utils;
use TU\Utils\Equation;
use TU\Utils\Ellipse;
use TU\controllers\ImageController;
    
    /**
     * Provides the mathematical functions needed in calculating the ellipse's
     * parameters, e.g. all the matrix functions and eigenvalues.
     * i.e. contains 3x3 matrix helper functions
     * @author Matti Suokas
     */
    class Equation {
        // Return $B matrix determinant
	public static function determinant($B) {
            return $B[0][0] * $B[1][1] * $B[2][2]
                + $B[0][1] * $B[1][2] * $B[2][0]
                + $B[0][2] * $B[1][0] * $B[2][1]
                - $B[0][2] * $B[1][1] * $B[2][0]
                - $B[0][1] * $B[1][0] * $B[2][2]
                - $B[0][0] * $B[1][2] * $B[2][1];
	}
        
        // Return inverse matrix of $B
        public static function inverse($B) {
            // Get determinant of matrix $B
            $d = Equation::determinant($B);
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
        
        // Return the result matrix from matrix multiply $A × $B 
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
        
        // Return transpose matrix of matrix $B
	public static function transpose($B) {
            return [[$B[0][0], $B[1][0], $B[2][0]],
            [$B[0][1], $B[1][1], $B[2][1]],
            [$B[0][2], $B[1][2], $B[2][2]]];
	}
        
        // Return matrix result of matrix $A + matrix $B
        public static function add($A, $B) {
            return [[$A[0][0] + $B[0][0], $A[0][1] + $B[0][1], $A[0][2] + $B[0][2]],
            [$A[1][0] + $B[1][0], $A[1][1] + $B[1][1], $A[1][2] + $B[1][2]],
            [$A[2][0] + $B[2][0], $A[2][1] + $B[2][1], $A[2][2] + $B[2][2]]];
	}

        // Return sum of the elements on matrix $A main diagonal
	public static function trace($A) { 
            return $A[0][0] + $A[1][1] + $A[2][2];             
        }  

        // Return scaled matrix $A by a multiplier of $k
	public static function scale($A, $k) {
            return [[$k * $A[0][0], $k * $A[0][1], $k * $A[0][2]],
            [$k * $A[1][0], $k * $A[1][1], $k * $A[1][2]],
            [$k * $A[2][0], $k * $A[2][1], $k * $A[2][2]]];
	}
        
        // Return eigenvalues of matrix $A
        public static function eigenvalues($A) {
            $q = Equation::trace($A) / 3;                                 
            $K = Equation::add($A, [[-$q, 0, 0],[0, -$q, 0],[0, 0, -$q]]);
            $p = sqrt(Equation::trace(Equation::multiply($K,$K))/6);
            $d = Equation::determinant(Equation::scale($K, 1 / $p));  
            $pi = M_PI;
            $phi;                        
            if ($d <= -2) {
                    $phi = M_PI / 3;
            } else if ($d >= 2) {
                    $phi = 0;
            } else {
                    $phi = acos($d / 2) / 3;
            }
            return [$q + 2 * $p * cos($phi),
            $q + 2 * $p * cos($phi + (2 * M_PI / 3)),
            $q + 2 * $p * cos($phi + (4 * M_PI / 3))];
	}
        
        // Return the found nullspace of the matrix $G
        public static function nullspace($G) {
            $k1 = -$G[2][0]/$G[2][2];
            $k2 = -$G[2][1]/$G[2][2];

            $y = -($G[1][0]+$G[1][2]*$k1)/($G[1][1]+$G[1][2]*$k2);
            $z = $k1 + $k2*$y;
            $n = sqrt(1+$y*$y+$z*$z);

            return [1/$n, $y/$n, $z/$n];
	}
        
        
    }
