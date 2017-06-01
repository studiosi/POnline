<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TU\Utils;
    use TU\Utils\Equation;
    use TU\controllers\ImageController;

/**
 * Description of Equation
 *
 * @author Maza
 */
class Equation {
    private static $a = 0; 
    private static $b = 0; 
    private static $c = 0; 
    private static $d = 0; 
    private static $e = 0;
    private static $f = 0; 
    private static $angle = 0;
    
    
    //$my = function($arg) {
    //		this.$equation = {$a:0, $b:0, $c:0, $d:0, $e:0, $f:0, $angle:0};
	
    public static function setfromequation($a, $b, $c, $d, $e, $f) {
			$this->a = $a;
			$this->b = $b;
			$this->c = $c;
			$this->d = $d;
			$this->e = $e;
			$this->f = $f;
			$this->angle = 0;
		}
		
    public static function setfromReducedequation($a, $c, $d, $e, $f, $angle) {
			$this->a = $a;
			$this->b = 0;
			$this->c = $c;
			$this->d = $d;
			$this->e = $e;
			$this->f = $f;
			$this->angle = ($angle === undefined)?0:$angle;
		}
	
    public static function setfrompoints($u){
			//compute sums
			$Sxxxx = $u.array_reduce(function($p, $c) { return $p + $c.$x * $c.$x * $c.$x * $c.$x; }, 0);
			$Sxxxy = $u.array_reduce(function($p, $c) { return $p + $c.$x * $c.$x * $c.$x * $c.$y; }, 0);
			$Sxxyy = $u.array_reduce(function($p, $c) { return $p + $c.$x * $c.$x * $c.$y * $c.$y; }, 0);
			$Sxyyy = $u.array_reduce(function($p, $c) { return $p + $c.$x * $c.$y * $c.$y * $c.$y; }, 0);
			$Syyyy = $u.array_reduce(function($p, $c) { return $p + $c.$y * $c.$y * $c.$y * $c.$y; }, 0);
			$Sxxx  = $u.array_reduce(function($p, $c) { return $p + $c.$x * $c.$x * $c.$x;       }, 0);
			$Sxxy  = $u.array_reduce(function($p, $c) { return $p + $c.$x * $c.$x * $c.$y;       }, 0);
			$Sxyy  = $u.array_reduce(function($p, $c) { return $p + $c.$x * $c.$y * $c.$y;       }, 0);
			$Syyy  = $u.array_reduce(function($p, $c) { return $p + $c.$y * $c.$y * $c.$y;       }, 0);
			$Sxx   = $u.array_reduce(function($p, $c) { return $p + $c.$x * $c.$x;             }, 0);
			$Sxy   = $u.array_reduce(function($p, $c) { return $p + $c.$x * $c.$y;             }, 0);
			$Syy   = $u.array_reduce(function($p, $c) { return $p + $c.$y * $c.$y;             }, 0);
			$Sx    = $u.array_reduce(function($p, $c) { return $p + $c.$x;                   }, 0);
			$Sy    = $u.array_reduce(function($p, $c) { return $p + $c.$y;                   }, 0);
			
			
			//$construct martrices
			$S1 = [[$Sxxxx, $Sxxxx, $Sxxyy],
					  [$Sxxxx, $Sxxyy, $Sxyyy],
					  [$Sxxyy, $Sxyyy, $Syyyy]];
			$S2 = [[$Sxxx, $Sxxy, $Sxx],
					  [$Sxxy, $Sxyy, $Sxy],
 					  [$Sxyy, $Syyy, $Syy]];
			$S3 = [[$Sxx, $Sxy, $Sx],
					  [$Sxy, $Syy, $Sy],
  					  [$Sx, $Sy, $u.length]];
			$S2T =  Ellipse::transpose($S2);
			$iS3 =  Ellipse::inverse($S3);
			$ic = [[0, 0, .5],
					  [0, -1, 0],
				 	  [.5, 0, 0]];

			$u = Ellipse::multiply($iS3, $S2T);
			$u = Ellipse::scale($u, -1);
			$a = Ellipse::multiply($ic, Ellipse::add($S1, Ellipse::multiply($S2, $u)));
			
			$eigVal = Ellipse::eigenvalues($a);
                        
			$eigfunc = array(Equation::eigenvectors($S1,$a));
			//eigenvectors - original commented below
                        $eigVec = $eigVal.array_map($S1, $eigfunc);
			/*$eigVec = $eigVal.array_map(function($Sl) {
				$ev = Ellipse::nullspace(Ellipse::add($a, [[-$l, 0, 0],[0, -$l, 0],[0, 0, -$l]]));
				//return {$ev: $ev, $cond: 4*$ev[2]*$ev[0] - $ev[1]*$ev[1]};
                                $cond = 4*$ev[2]*$ev[0] - $ev[1]*$ev[1];
                                return array($ev, $cond);
                                
                         }); */
			
			//condition
			 $a1 = $eigVec.array_filter(function($e) {
				return $e.cond > 0;
			}).array_reduce(function($p,$c) {
				return $p.cond < $c.cond ? $p : $c;
			}, array($cond = inf, $err = true));

			if ($a1.$err == undefined) {
				 $ev = $a1.$ev;
				$this->a = $ev[0];
				$this->b = $ev[1];
				$this->c = $ev[2];
				$this->d = $u[0][0]*$ev[0] + $u[0][1]*$ev[1] + $u[0][2]*$ev[2];
				$this->e = $u[1][0]*$ev[0] + $u[1][1]*$ev[1] + $u[1][2]*$ev[2];
				$this->f = $u[2][0]*$ev[0] + $u[2][1]*$ev[1] + $u[2][2]*$ev[2];
			} else {
                                $a1len = $a1.length;
                                ImageController::debug_to_console($p . $b . " with" . $eigenvectors . $length . "=" . $a1len);
				//console.warn("$p$b with $eigenvectors, $length = " + $a1.length);
				ImageController::debug_to_console($eigVec);
			}
		}

    public static function printEquation() {
			return printCoeff($this->a) + "x^2 "
				 + printCoeff($this->b) + "xy "
				 + printCoeff($this->c) + "y^2 "
				 + printCoeff($this->d) + "x "
				 + printCoeff($this->e) + "y "
				 + printCoeff($this->f) + " = 0";
		}
		
    public static function convertToReducedEquation() {
			$eq = this.equation;
			$t = Math.atan($this->b / ($this->c - $this->a))/2;
			$s = Math.sin(t);
			$c = Math.cos(t);
			$old_a = $this->a;
                        $old_b = $this->b;
			$old_c = $this->c;
			$old_d = $this->d;
			$old_e = $this->e;
			$this->a = $old_a*$c*$c - $old_b*$c*$s + $old_c*$s*$s;
			$this->c = $old_a*$s*$s + $old_b*$c*$s + $old_c*$c*$c;
			$this->d = $old_d*$c - $old_e*$s;
			$this->e = $old_d*$s + $old_e*$c;
			$this->angle = $t;
			$this->b = 0;
		}
		
    public static function getAxisLength() {
			//var eq = this.equation;
			if (Math.abs($this->b) > 1e-9) Equation::convertToReducedEquation();
			$num = -4*$this->f*$this->a*$this->c + $this->c*$this->d*$this->d + $this->a*$this->e*$this->e;
			return [Math.sqrt(num/(4*$this->a*$this->c*$this->c)),
					Math.sqrt($num/(4*$this->a*$this->a*$this->c))];
		}
		
    public static function getCenter() {
			//var eq = this.equation;
			$denom = $this->b*$this->b - 4*$this->a*$this->c;
			return [(2*$this->c*$this->d - $this->b*$this->e)/$denom,
					(2*$this->a*$this->e - $this->d*$this->b)/$denom];
		}
                
    public static function eigenvectors($S1, $a) {
                        $ev = Ellipse::nullspace(Ellipse::add($a, [[-1, 0, 0],[0, -1, 0],[0, 0, -1]]));
				//return {$ev: $ev, $cond: 4*$ev[2]*$ev[0] - $ev[1]*$ev[1]};
                        $cond = 4*$ev[2]*$ev[0] - $ev[1]*$ev[1];
                        
                        
                        return array($ev, $cond);
                            
    }
                         
			/*$eigVec = $eigVal.array_map(function($Sl) {
				$ev = Ellipse::nullspace(Ellipse::add($a, [[-$l, 0, 0],[0, -$l, 0],[0, 0, -$l]]));
				//return {$ev: $ev, $cond: 4*$ev[2]*$ev[0] - $ev[1]*$ev[1]};
                                $cond = 4*$ev[2]*$ev[0] - $ev[1]*$ev[1];
                                
                                
                         }); */
	}
	//return $my;

