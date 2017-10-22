<?php
namespace TU\Utils;
    use TU\Utils\Equation;
    use TU\Utils\Ellipse;
    use TU\controllers\ImageController;
    
    
/*
Given:
    data – a set of observed data points
    model – a model that can be fitted to data points
    n – the minimum number of data values required to fit the model
    k – the maximum number of iterations allowed in the algorithm
    t – a threshold value for determining when a data point fits a model
    d – the number of close data values required to assert that a model fits well to data

Return:
    bestfit – model parameters which best fit the data (or nul if no good model is found)
*/
    
    class RANSAC {

    private $iter = 0; // Iterator for main loop
    private $besterr = INF;
    private $threshold = 2.5;     // Used to determine if error is good enough 2.5
    private $inliersRatio = 0.5;  // To accept a model, atl least 70% of points must fit 
    private $k = 500; // Amount of iterations
    private $testinliers = array();
    private $bestfit = array();
    private $t = 16; // How far the point can be from the ellipse border (px)
    private $d = 0;
    
    public function ransacAlg($data) {
        $this->d = 5;
        //$bestfit = array();
        $this->threshold = count($data) * $this->inliersRatio;
        $iter = 0;
        while ($iter < $this->k) {
            $runthreshold = 0; // Max amount of runs if the fitness doesn't get better
            $thiserr = 0;
            $alsoinliers = array();   
            // Adding 5 elements to testinliers array
            $j = 0;
            while ($j < 5) {
                $r = rand(0 , count($data)-1);
                if (!in_array($data[$r], $this->testinliers)) {
                    $this->testinliers[$j] = $data[$r]; 
                    $j = $j + 1;
                }
            }
            
            
            // Fitting ellipse
            Equation::setfrompoints($this->testinliers);
            
            while (Equation::getErr() == true) {
                $j = 0;
                while ($j < 5) {
                $r = rand(0 , count($data)-1);
                    if (!in_array($data[$r], $this->testinliers)) {
                        $this->testinliers[$j] = $data[$r]; 
                        $j = $j + 1;
                    }
                }
                Equation::setfrompoints($this->testinliers);
            }
            
            $center = Equation::getCenter();
            $axis = Equation::getAxisLength();
            
            //for every point in data not in testinliers {
            for ($i = 0; $i < count($data); $i++) {
                $p = $data[$i];
                if (!in_array($p, $this->testinliers)) {
                    $p2 =  Equation::pointOnEllipse($axis[0], $axis[1], $p);
                    $v = array('dx' => $p['x'] - $p2['x'], 'dy' => $p['y'] - $p2['y']);
                    $distancefromborder = hypot($v['dx'], $v['dy']);
                    $thiserr += $distancefromborder;
                    //if point fits maybemodel with an error smaller than t add point to alsoinliers
                    if ($distancefromborder < $this->t) {
                        array_push($alsoinliers, $p);
                    }
                }
            }
            

            
            //$this->threshold = 0;
            //if the number of elements in alsoinliers is > d {
            if (count($alsoinliers) > $this->d) {
                // this implies that we may have found a good model
                // now test how good it is
                //$bettermodel = Equation::setfrompoints($this->alsoinliers); // model parameters fitted to all points in maybeinliers and alsoinliers
                $bettermodel = $alsoinliers;
                //$thiserr = count($alsoinliers); //a measure of how well model fits these points
                if ($thiserr < $this->besterr) {
                    $runthreshold = 0;
                //if ($thiserr > $this->besterr) {
                    $this->bestfit = $bettermodel;
                    $this->besterr = $thiserr;
                    
                }
            }
            $iter++;
            $runthreshold++;
            if (($runthreshold > 100) || count($this->bestfit) > $this->threshold) {
                return $this->bestfit;
            }
        }
        if (empty($this->bestfit)) $this->bestfit = $this->testinliers;
        //if (Equation::getErr() == true)
            return $this->bestfit;
       // else
          //  return $this->testinliers;
    }

}