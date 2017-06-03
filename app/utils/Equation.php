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
			$equation['angle'] = 0;
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
			$ic = [[0, 0, 0.5],
			    [0, -1, 0],
			    [0.5, 0, 0]];
                       
                        
                        
			$U = Ellipse::multiply($iS3, $S2T); 
			$U = Ellipse::scale($U, -1);
			$A = Ellipse::multiply($ic, Ellipse::add($S1, Ellipse::multiply($S2, $U)));
                        
                        var_dump($U);
                        var_dump($A);
			$eigVal = Ellipse::eigenvalues($A);
                        //$eigVal = Lapack::eigenValues($A);
                        var_dump($eigVal);
			//eigenvectors - original commented below
                        
                        // FIX THIS
                        $eigVec = array_map(function($l) {
                            $ev = Ellipse::nullspace(Ellipse::add($A, [[-$l, 0, 0],[0, -$l, 0],[0, 0, -$l]]));
                            return array('ev' => $ev, 'cond' => 4*$ev[2]*$ev[0] - $ev[1]*$ev[1]);                           

                        }, $eigVal);
                        var_dump($eigVec);
                        
                       
			//condition
                        // FIX THIS
                        $a1filter = array_filter($eigVec,function($e) {
                        return $e['cond'] > 0;});
                        
			 $a1 = array_reduce($a1filter, function($p,$c) {
				return $p['cond'] < $c['cond'] ? $p : $c;   
			}, array(array($cond => INF), array(err => true)));
                        var_dump($a1);
			//if ($a1['err'] == undefined) {
				$ev = $a1['ev'];
				$equation['a'] = $ev[0];
				$equation['b'] = $ev[1];
				$equation['c'] = $ev[2];
				$equation['d'] = $u[0][0]*$ev[0] + $u[0][1]*$ev[1] + $u[0][2]*$ev[2];
				$equation['e'] = $u[1][0]*$ev[0] + $u[1][1]*$ev[1] + $u[1][2]*$ev[2];
				$equation['f'] = $u[2][0]*$ev[0] + $u[2][1]*$ev[1] + $u[2][2]*$ev[2];
                                
                                $equationstring = Equation::printEquation();
                                ImageController::debug_to_console($equationstring);
                                var_dump($equation);
			/* } else {
                                $a1len = count($a1);
                                ImageController::debug_to_console("Pb with eigenvectors, length = " . count($a1));
                                ImageController::debug_to_console($eigVec);
                                var_dump($eigVec);
                                
			} */
		}
                
    public static function printCoeff($x) {
			return ($x<0?"-":"+") + abs(round($x*1000)/1000);
		}
                
    public static function printEquation() {
			return Equation::printCoeff($equation['a']) . "x^2 "
				 . Equation::printCoeff($equation['b']) . "xy "
				 . Equation::printCoeff($equation['c']) . "y^2 "
				 . Equation::printCoeff($equation['d']) . "x "
				 . Equation::printCoeff($equation['e']) . "y "
				 . Equation::printCoeff($equation['f']) . " = 0";
		}
		
    public static function convertToReducedEquation() {
			$eq = $this->equation;
			$t = atan($equation['b'] / ($equation['c'] - $equation['a']))/2;
			$s = sin(t);
			$c = cos(t);
			$old_a = $equation['a'];
                        $old_b = $equation['b'];
			$old_c = $equation['c'];
			$old_d = $equation['d'];
			$old_e = $equation['e'];
			$equation['a'] = $old_a*$c*$c - $old_b*$c*$s + $old_c*$s*$s;
			$equation['c'] = $old_a*$s*$s + $old_b*$c*$s + $old_c*$c*$c;
			$equation['d'] = $old_d*$c - $old_e*$s;
			$equation['e'] = $old_d*$s + $old_e*$c;
			$equation['angle'] = $t;
			$equation['b'] = 0;
		}
		
    public static function getAxisLength() {
			//var eq = this.equation;
			if (abs($equation['b']) > 1e-9) Equation::convertToReducedEquation();
			$num = -4*$equation['f']*$equation['a']*$equation['c'] + $equation['c']*$equation['d']*$equation['d'] + $equation['a']*$equation['e']*$equation['e'];
			return [sqrt(num/(4*$equation['a']*$equation['c']*$equation['c'])),
					sqrt($num/(4*$$equation['a']*$equation['a']*$equation['c']))];
		}
		
    public static function getCenter() {
			//var eq = this.equation;
			$denom = $equation['b']*$equation['b'] - 4*$equation['a']*$equation['c'];
			return [(2*$equation['c']*$equation['d'] - $equation['b']*$equation['e'])/$denom,
					(2*$$equation['a']*$equation['e'] - $equation['d']*$equation['b'])/$denom];
		}
                
    public static function eigenvectors($l, $a) {
                        $ev = Ellipse::nullspace(Ellipse::add($a, [[-$l, 0, 0],[0, -$l, 0],[0, 0, -$l]]));
				//return {$ev: $ev, $cond: 4*$ev[2]*$ev[0] - $ev[1]*$ev[1]};
                        $cond = 4*$ev[2]*$ev[0] - $ev[1]*$ev[1];
                        
                        
                        return array($ev, $cond);
                            
    }
                         
		
}
	//return $my;

