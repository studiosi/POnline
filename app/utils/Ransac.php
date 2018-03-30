<?php
namespace TU\Utils;
    use TU\Utils\Equation;
    use TU\Utils\Ellipse;
    use TU\Utils\Ransac;
    use TU\controllers\ImageController;
    
    
/**
 * The RANSAC algorithm for finding best fit model for the ellipse 
 * @author Matti Suokas
 */
    
    class RANSAC {
    // Stores the model's error value, which is the cumulative error amount of pixel
    // distances
    private $besterr = INF;
    /*
     threshold, an unspecified parameter in the formal statement of the RANSAC paradigm, 
     is used as the basis for determining that an n subset of P has been found that implies 
     a sufficiently large consensus set to permit the algorithm to terminate.
     */
    // Used to determine the amount of needed pts, calculated in the function
    // from data point count and the set inliersRatio.
    private $threshold = 2.5;     
    private $inliersRatio = 0.5;  // To accept a model, atl least x% of points must fit 
    private $k = 500; // Amount of iterations
    private $testinliers = array(); // The testinliers the model fits the data with
    private $bestfit = array(); // Best fit of points
    private $t = 16; // How far the point can be from the ellipse border (px)
    private $d = 0;
    
    public function ransacAlg($data) {
        // We cannot fit less than five points
        if (count($data) < 5) return $data;
        $this->d = 5;
        //$bestfit = array();
        $this->threshold = count($data) * $this->inliersRatio;
        $iter = 0; // Iterator for main loop
        while ($iter < $this->k) {
            $runthreshold = 0; // Max amount of runs if the fitness doesn't get better
            $thiserr = 0;
            $alsoinliers = array();   
            // Adding 5 elements to testinliers array
            $j = 0; 
            shuffle($data);
            for ($j = 0; $j < 5; $j++) {
                $this->testinliers[$j] = $data[$j];
            } 
            
            // Fitting ellipse
            Ellipse::setfrompoints($this->testinliers); 
            $finditer = 0;
            // If the testinlier's give an array with inf param value in the
            // equation. Fit until a proper array of five points is found.
            while (Ellipse::getErr() == true) {
                shuffle($data);
                for ($j = 0; $j < 5; $j++) {
                    $this->testinliers[$j] = $data[$j];
                } 
                Ellipse::setfrompoints($this->testinliers);
                $finditer = $finditer + 1;
                // If a better model isn't found in 50 iters, just continue
                if ($finditer > 50) {
                    Ellipse::setErr();
                }
            } 
            
            // Get ellipse's center point
            $center = Ellipse::getCenter();
            // Get ellipse's axis length
            $axis = Ellipse::getAxisLength();
            
            //for every point in data not in testinliers {
            for ($i = 0; $i < count($data); $i++) {
                $p = $data[$i];
                if (!in_array($p, $this->testinliers)) {
                    // See how far the point is from the border
                    $p2 =  Ellipse::pointOnEllipse($axis[0], $axis[1], $p);
                    $v = array('dx' => $p['x'] - $p2['x'], 'dy' => $p['y'] - $p2['y']);
                    $distancefromborder = hypot($v['dx'], $v['dy']);
                    // Add to the error the distance from the border
                    $thiserr += $distancefromborder;
                    //if point fits model with distance smaller than t, add point to alsoinliers
                    if ($distancefromborder < $this->t) {
                        array_push($alsoinliers, $p);
                    }
                }
            }
            
            //if the number of elements in alsoinliers is > d {
            if (count($alsoinliers) > $this->d) {
                // this implies that we may have found a good model
                // now test how good it is
                $bettermodel = $alsoinliers;
                // If we found a new best model, save it to bestfit and reset runthreshold
                if ($thiserr < $this->besterr) {
                    $runthreshold = 0;
                    $this->bestfit = $bettermodel;
                    $this->besterr = $thiserr;
                    
                }
            }
            $iter++;
            
            $runthreshold++;
            // Return current bestfit, if over 100 runs without a new bestfit
            if (($runthreshold > 100) || (count($this->bestfit) > $this->threshold)) {
                return $this->bestfit;
            }
        }
        // If we haven't found a bestfit, set bestfit as the five testinlier points
        if (empty($this->bestfit)) $this->bestfit = $this->testinliers;
        // Return model's points which best fit the data 
        return $this->bestfit;
    }
    
}