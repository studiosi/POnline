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

    private $iter = 0;
    private $besterr = INF;
    private $threshold = 2.5;     // Used to determine if error is good enough 
    private $inliersRatio = 0.7;  // To accept a model, atl least 70% of points must fit 
    private $k = 500;
    private $testinliers = array();
    private $testmodel; //model parameters fitted to maybeinliers
    private $alsoinliers = array();
    private $bestfit = array();
    
    public static function ransacAlg($data) {

    while ($iter < $k) {

        // Adding 5 elements to testinliers array
        for ($i = 0; $i < 5; $i++) {
            $r = rand($min = 0 , $max = count($data)-$i );
            
            if (!in_array($data[$r], $this->testinliers))
                $this->testinliers[$i] = $data[$r]; 
            
        }

        // Fitting ellipse
        Equation::setfrompoints($this->testinliers);

        

        //for every point in data not in testinliers {
        for ($i = 0; $i < $data; $i++) {

            //if point fits maybemodel with an error smaller than t add point to alsoinliers
            if () {
                array_push($this->alsoinliers, $point);
            }
        }
        //if the number of elements in alsoinliers is > d {
        if (count($this->alsoinliers) > d) {
            // this implies that we may have found a good model
            // now test how good it is
            $bettermodel; // model parameters fitted to all points in maybeinliers and alsoinliers
            $thiserr; //a measure of how well model fits these points
            if ($this->thiserr < $this->besterr) {
                $this->bestfit = $bettermodel;
                $this->besterr = $this->thiserr;
            }
        }
        //increment iterations
    }
    return $this->bestfit;
    }

}