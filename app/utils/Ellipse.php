<?php
namespace TU\Utils;
use TU\Utils\Equation;
use TU\Utils\Ellipse;
use TU\controllers\ImageController;
    
/**
 * Functions for calculating the ellipses parameters according to given points
 * in an array of x,y coordinates. 
 * @author Matti Suokas
 */
class Ellipse {
    private static $equation = array(
        'a' => 0,
        'b' => 0,
        'c' => 0,
        'd' => 0,
        'e' => 0,
        'f' => 0,
        'angle' => 0,
        'err' => false
    );
    private static $center = array();
    private static $axisa = 0;
    private static $axisb = 0;
    
    public static function setfromequation($a, $b, $c, $d, $e, $f) {
        $equation['a'] = $a;
        $equation['b'] = $b;
        $equation['c'] = $c;
        $equation['d'] = $d;
        $equation['e'] = $e;
        $equation['f'] = $f;
        $equation['angle'];
        $equation['err'] = false;

    }
		
    public static function setfromReducedEquation($a, $c, $d, $e, $f, $angle) {
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


        //construct martrices
        $S1 = [[$Sxxxx, $Sxxxy, $Sxxyy],
                [$Sxxxy, $Sxxyy, $Sxyyy],
                [$Sxxyy, $Sxyyy, $Syyyy]];
        $S2 = [[$Sxxx, $Sxxy, $Sxx],
                [$Sxxy, $Sxyy, $Sxy],
                [$Sxyy, $Syyy, $Syy]];
        $S3 = [[$Sxx, $Sxy, $Sx],
                [$Sxy, $Syy, $Sy],
                [$Sx, $Sy, count($u)]];
        $S2T =  Equation::transpose($S2);
        $iS3 =  Equation::inverse($S3);
        $iC = [[0, 0, .5],
                [0, -1, 0],
                [.5, 0, 0]];


        $U = Equation::multiply($iS3, $S2T); 
        $U = Equation::scale($U, -1);
        $A = Equation::multiply($iC, Equation::add($S1, Equation::multiply($S2, $U)));
        
        // Get eigenvalues
        $eigVal = Equation::eigenvalues($A); 
        
        // Get eigenvector
        $eigVec = array_map(function($l) use ($A) {
            $ev = Equation::nullspace(Equation::add($A, [[-$l, 0, 0],[0, -$l, 0],[0, 0, -$l]]));
            return array('ev' => $ev, 'cond' => 4*$ev[2]*$ev[0] - $ev[1]*$ev[1]);                           

        }, $eigVal);

        //condition
        $a1filter = array_filter($eigVec,function($e) {
            return $e['cond'] > 0;
        });

        $a1 = array_reduce($a1filter, function($p,$c) {
            return $p['cond'] < $c['cond'] ? $p : $c;   
        }, array('cond' => INF, 'err' => true));

        // If the array doesn't have error value (inf value)
        if (array_key_exists('err', $a1) == false) {
                $ev = $a1['ev'];
                self::$equation['a'] = $ev[0];
                self::$equation['b'] = $ev[1];
                self::$equation['c'] = $ev[2];
                self::$equation['d'] = $U[0][0]*$ev[0] + $U[0][1]*$ev[1] + $U[0][2]*$ev[2];
                self::$equation['e'] = $U[1][0]*$ev[0] + $U[1][1]*$ev[1] + $U[1][2]*$ev[2];
                self::$equation['f'] = $U[2][0]*$ev[0] + $U[2][1]*$ev[1] + $U[2][2]*$ev[2];
                self::$equation['err'] = false;
        } 
        // TO DO - HANDLE THE ERROR CASES
        else {
            // Inf value in the array, change error to true
            self::$equation['err'] = true;
        }
    }
                
    // Return ellipse’s equation as a string based on the class' parameters
    public static function printCoeff($x) {
        return ($x<0?"-":"+") + abs(round($x*1000)/1000);
    }
                
    // Convert the class equation into reduced equation form
    public static function printEquation() {
        return  Ellipse::printCoeff(self::$equation['a']) . "x^2 "
                . Ellipse::printCoeff(self::$equation['b']) . "xy "
                . Ellipse::printCoeff(self::$equation['c']) . "y^2 "
                . Ellipse::printCoeff(self::$equation['d']) . "x "
                . Ellipse::printCoeff(self::$equation['e']) . "y "
                . Ellipse::printCoeff(self::$equation['f']) . " = 0";
                        
		}
		
    public static function convertToReducedEquation() {
        $eq = self::$equation;
        $t = atan(self::$equation['b'] / (self::$equation['c'] - self::$equation['a']))/2;
        $s = sin($t);
        $c = cos($t);
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
		
    // Return the length of the axis a and b as an array
    public static function getAxisLength() {
        $a = self::$equation['a'];  // A20   x^2 
        $b = self::$equation['b'];  // A10   xy
        $c = self::$equation['c'];  // A11   y^2
        $d = self::$equation['d'];  // A01   x
        $e = self::$equation['e'];  // A02   y
        $f = self::$equation['f'];  // A00   0
        $eq = self::$equation;
        
        // IMPLEMENT THIS WHEN b ANGLE is 0º or 90º!
        //if (abs($b) > 1e-9) {
        //    self::convertToReducedEquation(); 
        //} 
        
        $numeratora = ($a * ($e * $e) + $c * ($d *$d) - $b * $d * $e + (($b * $b) - 4 * $a * $c) * $f) * ($a + $c + sqrt(($a - $c) * ($a - $c) + $b * $b));  
        $numeratorb = ($a * ($e * $e) + $c * ($d *$d) - $b * $d * $e + (($b * $b) - 4 * $a * $c) * $f) * ($a + $c - sqrt(($a - $c) * ($a - $c) + $b * $b));  
        $denominator = $b * $b - 4 * $a * $c;
        
        $axisa = (- sqrt(2 * $numeratora)) / $denominator;
        $axisb = (- sqrt(2 * $numeratorb)) / $denominator;   
        $axis = [$axisa, $axisb];
        
        self::$axisa = $axisa;
        self::$axisb = $axisb;
        
        return $axis;
    }
                
    // Return the angle of the ellipse
    public static function getAngle() {
        $a = self::$equation['a'];
        $b = self::$equation['b'];
        $c = self::$equation['c'];
        
        // Calculate ellipse's angle if b is not 0
        if (self::$equation['b'] != 0)
            self::$equation['angle'] = atan(($c - $a - sqrt(($a - $c) * ($a - $c) + (2*$b) * (2*$b)))/(2*$b));
        
        else 
            // Else set angle to 0
            // Is this always true?
            self::$equation['angle'] = 0;
        
        
        return self::$equation['angle'];
    }
    
    // Calculate ellipse's center point of an array of x and y coordinate and return the result
    public static function getCenter() {
        $a = self::$equation['a'];  // A20   x^2 
        $b = self::$equation['b'];  // A10   xy
        $c = self::$equation['c'];  // A11   y^2
        $d = self::$equation['d'];  // A01   x
        $e = self::$equation['e'];  // A02   y
        $f = self::$equation['f'];  // A00   0
        
        
        //Calculate denominator for the ellipse’s center equation        
	$denom = self::$equation['b']*self::$equation['b'] - 4*self::$equation['a']*self::$equation['c'];
	// Calculate center coordinate array with the equation
        $center = array('x' => (2*self::$equation['c']*self::$equation['d'] - self::$equation['b']*self::$equation['e'])/$denom,
            'y' => ((2*self::$equation['a']*self::$equation['e'] - self::$equation['d']*self::$equation['b'])/$denom));
        
        self::$center = $center;
        return $center;
        
	}
    /*    
    public static function getFoci() {
        $temp_c;
        $axis = Ellipse::getAxisLength();
        $a = $axis[0];
        $b = $axis[1];
        
        if($a > $b)
          $temp_c = sqrt($a* $a - $b * $b);
        else
          $temp_c = sqrt($b * $b - $a * $a);
        
        $ellipse = Ellipse::getCenter();
        
        $f1_x = ellipse['x'] - $temp_c * cos(self::$equation['angle'] * M_PI / 180);
        $f1_y = ellipse.['y'] - $temp_c * sin(self::$equation['angle'] * M_PI /180);
        $f2_x = ellipse.['x'] + $temp_c * cos(self::$equation['angle'] * M_PI /180);
        $f2_y = ellipse.['y'] + $temp_c * sin(self::$equation['angle'] * M_PI / 180); 
        
        self::$f1_x = $f1_x;
        self::$f1_y = $f1_y;
        self::$f2_x = $f2_x;
        self::$f2_y = $f1_y;
                
        } */
    
  

    // From http://wwwf.imperial.ac.uk/~rn/distance2ellipse.pdf .
    // Calculates the distance of a point from the border of ellipse
    public static function pointOnEllipse($a, $b, $p) {
        $center = Ellipse::getCenter(); // Gets center
        $maxIterations = 10;
        $eps = 0.1/max($a, $b);

        $p1 = array('x' => $p['x'] - $center['x'], 'y' => $p['y'] - $center['y']);

        // Intersection of straight line from origin to p with ellipse
        // as the first approximation:
        $phi = atan2($a * $p1['y'], $b * $p1['x']);

        // Newton iteration to find solution of
        // f(θ) := (a^2 − b^2) cos(phi) sin(phi) − x a sin(phi) + y b cos(phi) = 0:
        for ($i= 0; $i < $maxIterations; $i++) {
            // function value and derivative at phi:
            $c = cos($phi);
            $s = sin($phi);
            $f = ($a*$a - $b*$b)*$c*$s - $p1['x']*$a*$s + $p1['y']*$b*$c;
            $f1 = ($a*$a - $b*$b)*($c*$c - $s*$s) - $p1['x']*$a*$c - $p1['y']*$b*$s;

            $delta = $f/$f1;
            $phi = $phi - $delta;
            if (abs($delta) < $eps)  { break; }
        }
        
        // Return best approximation from the iterations
        return array('x' => $center['x'] + $a * cos($phi), 'y' => $center['y'] + $b * sin($phi));
    }
    
    
    public static function getErr() {
        return self::$equation['err'];
    }
    
    public static function setErr() {
        $equation['err'] = false;
    }
        
    public static function getEllipseParams() {
        return self::$equation;
    }

}