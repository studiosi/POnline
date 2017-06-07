<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TU\Utils;
    use TU\Utils\Equation;
    use TU\Utils\Ellipse;
    use TU\controllers\ImageController;
    //use TU\Utils\Matrix;
    //use \webd\vectors\Vector;
    
/**
 * Description of Equation
 *
 * @author Maza
 */
class Equation {
    private static $equation = array(
        'a' => 0,
        'b' => 0,
        'c' => 0,
        'd' => 0,
        'e' => 0,
        'f' => 0,
        'angle' => 0
    );
    
    
    //$my = function($arg) {
    //		this.$equation = {$a:0, $b:0, $c:0, $d:0, $e:0, $f:0, $angle:0};
	
    public static function setfromequation($a, $b, $c, $d, $e, $f) {
			$equation['a'] = $a;
			$equation['b'] = $b;
			$equation['c'] = $c;
			$equation['d'] = $d;
			$equation['e'] = $e;
			$equation['f'] = $f;
                        $equation['angle'];
                        
		}
		
    public static function setfromReducedequation($a, $c, $d, $e, $f, $angle) {
			$equation['a'] = $a;
			$equation['b'] = 0;
			$equation['c'] = $c;
			$equation['d'] = $d;
			$equation['e'] = $e;
			$equation['f'] = $f;
			$equation['angle'] = ($angle === undefined)?0:$angle;
		}
	
    public static function setfrompoints($u){
			//compute sums
			$Sxxxx = array_reduce($u,function($p, $c) { return $p + $c['x'] * $c['x'] * $c['x'] * $c['x']; }, 0);
			$Sxxxy = array_reduce($u,function($p, $c) { return $p + $c['x'] * $c['x'] * $c['x'] * $c['y']; }, 0);
			$Sxxyy = array_reduce($u,function($p, $c) { return $p + $c['x'] * $c['x'] * $c['y'] * $c['y']; }, 0);
			$Sxyyy = array_reduce($u,function($p, $c) { return $p + $c['x'] * $c['y'] * $c['y'] * $c['y']; }, 0);
			$Syyyy = array_reduce($u,function($p, $c) { return $p + $c['y'] * $c['y'] * $c['y'] * $c['y']; }, 0);
			$Sxxx  = array_reduce($u,function($p, $c) { return $p + $c['x'] * $c['x'] * $c['x'];       }, 0);
			$Sxxy  = array_reduce($u,function($p, $c) { return $p + $c['x'] * $c['x'] * $c['y'];       }, 0);
			$Sxyy  = array_reduce($u,function($p, $c) { return $p + $c['x'] * $c['y'] * $c['y'];       }, 0);
			$Syyy  = array_reduce($u,function($p, $c) { return $p + $c['y'] * $c['y'] * $c['y'];       }, 0);
			$Sxx   = array_reduce($u,function($p, $c) { return $p + $c['x'] * $c['x'];             }, 0);
			$Sxy   = array_reduce($u,function($p, $c) { return $p + $c['x'] * $c['y'];             }, 0);
			$Syy   = array_reduce($u,function($p, $c) { return $p + $c['y'] * $c['y'];             }, 0);
			$Sx    = array_reduce($u,function($p, $c) { return $p + $c['x'];                   }, 0);
			$Sy    = array_reduce($u,function($p, $c) { return $p + $c['y'];                   }, 0);
			
			
			//$construct martrices
			$S1 = [[$Sxxxx, $Sxxxy, $Sxxyy],
					  [$Sxxxy, $Sxxyy, $Sxyyy],
					  [$Sxxyy, $Sxyyy, $Syyyy]];
			$S2 = [[$Sxxx, $Sxxy, $Sxx],
					  [$Sxxy, $Sxyy, $Sxy],
 					  [$Sxyy, $Syyy, $Syy]];
			$S3 = [[$Sxx, $Sxy, $Sx],
					  [$Sxy, $Syy, $Sy],
  					  [$Sx, $Sy, count($u)]];
			$S2T =  Ellipse::transpose($S2);
			$iS3 =  Ellipse::inverse($S3);
			$iC = [[0, 0, .5],
			    [0, -1, 0],
			    [.5, 0, 0]];
                        
                        
                        //var_dump($b);
			$U = Ellipse::multiply($iS3, $S2T); 
			$U = Ellipse::scale($U, -1);
			$A = Ellipse::multiply($iC, Ellipse::add($S1, Ellipse::multiply($S2, $U)));
                        
                        Ellipse::setA($A);
			$eigVal = Ellipse::eigenvalues($A); // Gives correct values
                        //$eigVal = Lapack::eigenValues($A);
			//eigenvectors - original commented below
                        // FIX THIS $A IS NULL IN FUNCTION
                        $eigVec = array_map(function($l) {
                            $EA = Ellipse::getA();
                            $ev = Ellipse::nullspace(Ellipse::add($EA, [[-$l, 0, 0],[0, -$l, 0],[0, 0, -$l]]));
                            return array('ev' => $ev, 'cond' => 4*$ev[2]*$ev[0] - $ev[1]*$ev[1]);                           

                        }, $eigVal);
                        
                        //$te = new Ellipse();
                        
                        Ellipse::setA($A);
                       
			//condition
                        // FIX THIS
                        $a1filter = array_filter($eigVec,function($e) {
                        return $e['cond'] > 0;});
                        
			 $a1 = array_reduce($a1filter, function($p,$c) {
				return $p['cond'] < $c['cond'] ? $p : $c;   
			}, array('cond' => INF, err => true));
			//if ($a1['err'] == undefined) {
				$ev = $a1['ev'];
				self::$equation['a'] = $ev[0];
				self::$equation['b'] = $ev[1];
				self::$equation['c'] = $ev[2];
				self::$equation['d'] = $U[0][0]*$ev[0] + $U[0][1]*$ev[1] + $U[0][2]*$ev[2];
				self::$equation['e'] = $U[1][0]*$ev[0] + $U[1][1]*$ev[1] + $U[1][2]*$ev[2];
				self::$equation['f'] = $U[2][0]*$ev[0] + $U[2][1]*$ev[1] + $U[2][2]*$ev[2];
                                
                                $equationstring = Equation::printEquation();
                                ImageController::debug_to_console($equationstring);
                                equation::getAxisLength();
                                //var_dump(self::$equation);
			/* } else {
                                $a1len = count($a1);
                                ImageController::debug_to_console("Pb with eigenvectors, length = " . count($a1));
                                ImageController::debug_to_console($eigVec);
                                var_dump($eigVec);
                       
                                
			} */
                                
                        $center = Equation::getCenter();
                        ImageController::debug_to_console($center);
		}
                
    public static function printCoeff($x) {
			return ($x<0?"-":"+") + abs(round($x*1000)/1000);
		}
                
    public static function printEquation() {
			return  Equation::printCoeff(self::$equation['a']) . "x^2 "
				 . Equation::printCoeff(self::$equation['b']) . "xy "
				 . Equation::printCoeff(self::$equation['c']) . "y^2 "
				 . Equation::printCoeff(self::$equation['d']) . "x "
				 . Equation::printCoeff(self::$equation['e']) . "y "
				 . Equation::printCoeff(self::$equation['f']) . " = 0";
                        
		}
		
    public static function convertToReducedEquation() {
			$eq = self::$equation;
			$t = atan(self::$equation['b'] / (self::$equation['c'] - self::$equation['a']))/2;
			$s = sin(t);
			$c = cos(t);
			$old_a = self::$equation['a'];
                        $old_b = self::$equation['b'];
			$old_c = self::$equation['c'];
			$old_d = self::$equation['d'];
			$old_e = self::$equation['e'];
			self::$equation['a'] = $old_a*$c*$c - $old_b*$c*$s + $old_c*$s*$s;
			self::$equation['c'] = $old_a*$s*$s + $old_b*$c*$s + $old_c*$c*$c;
			self::$equation['d'] = $old_d*$c - $old_e*$s;
			self::$equation['e'] = $old_d*$s + $old_e*$c;
			self::$equation['angle'] = $t;
			self::$equation['b'] = 0;
		}
		
    public static function getAxisLength() {
        $a = self::$equation['a'];  // A20   x^2 
        $b = self::$equation['b'];  // A10   xy
        $c = self::$equation['c'];  // A11   y^2
        $d = self::$equation['d'];  // A01   x
        $e = self::$equation['e'];  // A02   y
        $f = self::$equation['f'];  // A00   0
        //$eq = self::$equation;
                        
                        ImageController::debug_to_console("Test: " . $a . " " . $b . " " . $c . " " . $d . " " . $e . " " . $f);
			//var eq = this.equation;
			//if (abs(self::$equation['b']) > 1e-9) Equation::convertToReducedEquation();
			$num = -4*$f*$a*$c + $c*$d*$d 
                                + $a*$e*$e;
			$axis = [sqrt(num/(4*$a*$c*$c)),
					sqrt($num/(4*$a*$a*$c))];
                        var_dump($axis); 
        	
			//if (abs($b) > 1e-9) self::convertToReducedEquation();
                          /*  
			$num = -4 * $f * $a * $c + $c * $d * $d + $a * $e * $e;
			$Ra = sqrt($num / (4 * $a * $c * $c));
                        $Rb = sqrt($num /(4 * $a * $a * $c));
                        
                        ImageController::debug_to_console($Ra);
                        ImageController::debug_to_console($Rb);
                        */
                        return $axis;
			
		
        /*
        $numerator = sqrt(2 * ($a * ($e * $e) + $c * ($d * $d) + $f * (($b/2) * ($b/2)) - 2 * ($b/2) * $d * $e - $a * $c * $f));
        $denominator = sqrt(($a - $c) * ($a - $c)) + 4 * ($b * ($b/2)) * ($a + $c);
        
        $axis = $numerator / $denominator;          
        */
        //coefficient normalizing factor 
        /*
        $q = 64 * (($f * (4 * $a * $c - ($b * $b)) - $a * ($c * $c) + $b * $d * $c - $c * ($d * $d)) / ((4 * $a * $c - ($b * $b)) * (4 * $a * $c - ($b * $b))));
        $Rmax = 1/8 * (sqrt(2 * $q * sqrt(($b * $b) + ($a - $c) * ($a - $c)) - 2 * $q * ($a + $c))); */
        
        
        //ImageController::debug_to_console($q);
        
        //return $axis;
    }
                
    public static function getAngle() {
        $a = self::$equation['a'];
        $b = self::$equation['b'];
        $c = self::$equation['c'];
        
        $angle = atan(($c - $a - sqrt(($a - $c) * ($a - $c) + (2*$b) * (2*$b)))/(2*$b));
        self::$equation['angle'] = $angle;
        return $angle;
    }
		
    public static function getCenter() {
            
			//var eq = this.equation;
			$denom = self::$equation['b']*self::$equation['b'] - 4*self::$equation['a']*self::$equation['c'];
			return array('x' => (2*self::$equation['c']*self::$equation['d'] - self::$equation['b']*self::$equation['e'])/$denom,
					'y' => ((2*self::$equation['a']*self::$equation['e'] - self::$equation['d']*self::$equation['b'])/$denom));
		}
                
                         
		
}