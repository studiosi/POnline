<?php

    namespace TU\Controllers;

    use Silex\Application;
    use TU\DAO\ImageDAO;
    use TU\DAO\PlayerDAO;
    use RandomLib;
    use SecurityLib;
    use Symfony\Component\HttpFoundation\Request;
    use TU\Utils\MathUtils;
    use TU\Utils\Equation;
    use TU\Utils\Ellipse;
    use TU\Utils\FormatUtils;
    use TU\Utils\Ransac;

    class AdminController {

        private $TOKEN_BAN_UNBAN = 'token_ban_unban';
        
        // Renders signup.twig for sign up page
        public function getSignup(Application $app) {

            return $app['twig']->render('signup.twig');

        }
        
        // For admin user creation
        public function postSignup(Application $app, Request $req) {

            $username = $req->get('username');
            $pwd = $req->get('pwd');
            $pwd_repeat = $req->get('pwd_repeat');

            $pdao = new PlayerDAO();

            $errors = array();			

            // Username
            if(strlen($username) == 0) {

                    $errors[] = "Username is empty";

            }			
            elseif (preg_match("/^[0-9A-Za-z_]+$/", $username) == 0) {

                    $errors[] = "Username can only contain ASCII letters a-z (smalls and caps), numbers and the underscore";

            }			
            elseif($pdao->existsUsername($app, $username)) {

                    $errors[] = "Username already exists";

            }

            // Password
            if(strlen($pwd) == 0) {

                    $errors[] = "Password is empty";

            }			
            if(strcmp($pwd, $pwd_repeat) != 0) {

                    $errors[] = "Passwords do not match";

            }

            // User creation / error display
            if(count($errors) > 0) {

                    $data = array(
                            'username' => $username
                    );

                    return $app['twig']->render('signup.twig', array('errors' => $errors, 'data' => $data));

            }
            else {

                    $pdao->createAdmin($app, $username, $pwd);
                    return $app->redirect($app['url_generator']->generate('index'));

            }

        }
        
        /* Get data for every image running RANSAC five times for each image
         * with the given percentage of data points' number for each image.
         */
        public function getPercentageCSV(Application $app) {
            $imDAO = new ImageDAO();
            $images = $imDAO->getAllImages($app);

            $filter = 1; // Percentage of points to get    
            $array_len = 0;    
            $fp = fopen('/params.csv','w');
            
            // For every image
            foreach($images as $image) {   
                $pointsraw_unfiltered = $imDAO->getAllClicksImage($app, $image['id']);
                $pointsraw_tmp = array_unique($pointsraw_unfiltered,SORT_REGULAR);
                $pointsraw = array_values($pointsraw_tmp);
                shuffle($pointsraw);    

                $i = 0;
                // Run RANSAC five times
                while ($i < 5) {
                    $ransac = new Ransac;
                    $points = $ransac->ransacAlg($pointsraw);

                    $centroid = array('x' => 0, 'y' => 0);
                    Ellipse::setfrompoints($points);
                    $centroid = Ellipse::getCenter();	
                    
                    if (($centroid['x'] >= 0) && ($centroid['y'] >= 0)) {
                        $temp1 = array_merge($image,$centroid);
                        fputcsv($fp, $temp1);
                        $i = $i + 1;
                    }
                }
                    
                    
            }
            fclose($fp);
             
            return $app['twig']->render('csv.twig', array(
                    
            ));
        }
        
        /*
        public function getNClicksFromImg(Application $app) {
            $imDAO = new ImageDAO();
            // For the test, get img with id 168
            $image = $imDAO->getImageById($app, 168);
            $header = array('id', 'name', 'clicks');
            // Output CSV file with the image params
            $fileName = "D:\params.csv";
            // Output CSV file with the image params 
            $fp = fopen('/params.csv','w');
            $j = 0;
            
            $pointsraw = $imDAO->getAllClicksImage($app, $image['id']);
            $n = count($pointsraw);
            shuffle($pointsraw);
            
            for ($x = 4; $x < $n; $x++) {
                $tmp = array();    
                $points = array();
                // FOR TESTING SMALLER PERCENTAGES OF DATA
                for ($h = 0; $h < $x; $h++) {
                    $tmp[$h]['x'] = $pointsraw[$h]['x'];
                    $tmp[$h]['y'] = $pointsraw[$h]['y'];
                    //print_r($data[$bbq]);
                }
                $i = 0;
                while ($i < 5) {

                    $i = $i + 1;

                    $ransac = new Ransac;
                    $points = $ransac->ransacAlg($tmp);

                    $centroid = array('x' => 0, 'y' => 0);

                    Ellipse::setfrompoints($points);

                    $centroid = Ellipse::getCenter();		
                    
                    $ellipse_params = Ellipse::getEllipseParams();
                    $axis = Ellipse::getAxisLength();
                    $angle = Ellipse::getAngle();
                    if (($centroid['x'] > 0) && ($centroid['y'] > 0)) {
                        $imcent = array_merge($image,$centroid);
                        array_push($imcent,$x+1);
                        //$imcent = array_merge($x,$imcent);
                        //$j = $j+1;
                        fputcsv($fp, $imcent);
                        
                    } else {
                        $i = $i -1;
                    }
                }
            }
           
            fclose($fp);
            
            return $app['twig']->render('csv2.twig', array(
                    
            ));
        }
        */
        
        /* For getting the consistency data of two equal length arrays.
        Divides the data of each image into 50% amount arrays and runs RANSAC
        five times for both arrays of every image */
        public function getConsistency(Application $app) {
            $imDAO = new ImageDAO();
            $images = $imDAO->getAllImages($app);

            $filter = 0.2; // Percentage of points from the two equal arrays to get    
            $array_len = 0;    
            $fp = fopen('/params.csv','w'); // File where to write the .csv results
            
            // For every image in the db
            foreach($images as $image) {   
                // Get raw points and Slice the array into two equal length arrays 
                // for testing consistency
                $pointsraw_unfiltered = $imDAO->getAllClicksImage($app, $image['id']);
                $pointsraw_tmp = array_unique($pointsraw_unfiltered,SORT_REGULAR);
                $pointsraw = array_values($pointsraw_tmp);
                shuffle($pointsraw);    
                $n = count($pointsraw);
                $chunked1 = array_slice($pointsraw, 0, $n / 2);
                $chunked2 = array_slice($pointsraw, $n / 2);
               
                
                $j = 0;
                $i = 0;
                $x = 0;
                $array_len = min(count($chunked1), count($chunked2));
                // At least 5 pts
                $len = ceil(max(5, $array_len*$filter));

                // the percentage of pts to get according to filter value
                // 1 as a filter divides the array into equal of two 50% arrays
                $tmp1 = array_slice($chunked1, 0, $len);
                $tmp2 = array_slice($chunked2, 0, $len);

                // Fit RANSAC five times and store results for first arr
                while ($i < 5) {
                    shuffle($tmp1); 
                    $ransac = new Ransac;
                    $points = $ransac->ransacAlg($tmp1);

                    $centroid = array('x' => 0, 'y' => 0);

                    // Calculate ellipse's params
                    Ellipse::setfrompoints($points);
                    // Get the center of the ellipse
                    $centroid = Ellipse::getCenter();		
                    
                    /* Uncomment if ellipse params are needed too
                    $ellipse_params = Ellipse::getEllipseParams();
                    $axis = Ellipse::getAxisLength(); */

                    // Pixel coordinates have to be positive
                    if (($centroid['x'] >= 0) && ($centroid['y'] >= 0)) {
                        $temp1 = array_merge($image,$centroid);
                        // store result into csv
                        fputcsv($fp, $temp1);
                        $i = $i + 1;
                    }
                }
                // Fit RANSAC five times and store results for second arr    
                while ($x < 5) {
                    shuffle($tmp2);     
                    $ransac = new Ransac;
                    $points = $ransac->ransacAlg($tmp2);

                    $centroid = array('x' => 0, 'y' => 0);
                    // Calculate ellipse's params
                    Ellipse::setfrompoints($points);
                    // Get the center of the ellipse
                    $centroid = Ellipse::getCenter();	
                    
                    /* Uncomment if ellipse params are needed too
                    $ellipse_params = Ellipse::getEllipseParams();
                    $axis = Ellipse::getAxisLength(); */

                    // Pixel coordinates have to be positive
                    if (($centroid['x'] >= 0) && ($centroid['y'] >= 0)) {
                        $temp2 = array_merge($image,$centroid);
                        // store result into csv
                        fputcsv($fp, $temp2);
                        $x = $x + 1;

                    } 
                             
                }  
            }
            fclose($fp);
             
            return $app['twig']->render('csv4.twig', array(
                    
            ));
        }
        
        
        /* For testing how the accuracy of RANSAC changes when each image is run
         * from 5 clicks to 143 clicks. If there isn't image with the current
         * loop's iteration value, it's skipped. Thats why we 
         * need the getImgCountsForImprovement function to get according amount
         * of points for each click count of images */
        public function get5ToNAll(Application $app) {
            $imDAO = new ImageDAO();
            $images = $imDAO->getAllImages($app);
 
            $array_len = 0;    
            $fp = fopen('/improvement.csv','w'); // File to store the results into
            
            /* The array length for each iteration is 138. Remember to set 
             * getImgCountsForImprovement to match the same for loop code. */
            for ($i = 5; $i <= 142; $i++) {
                // Get points of each image
                foreach($images as $image) {                  
                    $pointsraw_unfiltered = $imDAO->getAllClicksImage($app, $image['id']);
                    // Run the code if image has $i number of clicks, else set centroid
                    // to 0,0 five times instead of five iters of RANSAC
                    if (count($pointsraw_unfiltered) > $i) {
                        // Remove duplicate points
                        $pointsraw_tmp = array_unique($pointsraw_unfiltered,SORT_REGULAR);
                        $pointsraw = array_values($pointsraw_tmp);
                        shuffle($pointsraw);    
                        $n = count($pointsraw);
                        // Get $i number of points from the array
                        $chunked = array_slice($pointsraw, 0, $i);

                        $j = 0;
                        while ($j < 5) {
                            $centroid = array('x' => 0, 'y' => 0);
                            
                            // If over five points, run RANSAC
                            if ($i > 5 ) { 
                                $ransac = new Ransac;
                                $points = $ransac->ransacAlg($chunked);
                                Ellipse::setfrompoints($points);
                                $centroid = Ellipse::getCenter();
                                if (($centroid['x'] > 0) && ($centroid['y'] > 0)) {
                                    $temp1 = array_merge($image,$centroid);
                                    fputcsv($fp, $temp1);
                                    $j = $j + 1;
                                }
                            }
                            // Else get params according to five pts
                            else {
                                Ellipse::setfrompoints($chunked);
                                $centroid = Ellipse::getCenter();
                                $temp1 = array_merge($image,$centroid);
                                fputcsv($fp, $temp1);
                                $j = $j + 1;
                            }
                        }
                    } 
                    /* If less than $i click count: input 5 times 0,0 as centroid */
                    else {
                        $j = 0;
                        while ($j < 5) {
                            $centroid = array('x' => 0, 'y' => 0);
                            $temp1 = array_merge($image,$centroid);
                            fputcsv($fp, $temp1);
                            $j = $j + 1;
                        }
                    }   
                }
            }

            fclose($fp);
             
            return $app['twig']->render('csv6.twig', array(
                    
            ));
        }
        
        // The amount of imgs with the corresponding iteration count of 
        // get5ToAll function
        public function getImgCountsForImprovement(Application $app) {
            $imDAO = new ImageDAO();
            $images = $imDAO->getAllImages($app);

           // $header = array('id', 'name', 'clicks');
                // Output CSV file with the image params
            //$fileName = "D:\params.csv";
            //add BOM to fix UTF-8 in Excel
            //fputs($csv, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

            //fputcsv($fp, $header); 
 
            $array_len = 0;    
            $fp = fopen('/imgcountforclicks.csv','w');
            
            /* IMG amount count array for each iteration 142-5=137 */
            /* Filled with zeros */
            $imcounts = array_fill(0, 138, 0);
            
            for ($i = 5; $i <= 142; $i++) {
                foreach($images as $image) {   
                    // GET IMAGEs THAT HAS $y clicks
                    $pointsraw_unfiltered = $imDAO->getAllClicksImage($app, $image['id']);
                    if (count($pointsraw_unfiltered) > $i) {
                        /* Increase according image count array*/
                        $imcounts[$i-5]++;
                    }
                }
            }
            fputcsv($fp, $imcounts);
            fclose($fp);
             
            return $app['twig']->render('csv7.twig', array(
                    
            ));
        }
        
        // Get data of every click into CSV with image, player and click id 
        public function getUserClickAmount(Application $app) {
            $imDAO = new ImageDAO();
            $images = $imDAO->getAllImages($app);
            $pointsraw = $imDAO->getAllClicks($app);
            $fp = fopen('/clicks.csv','w');
            $header = array('id', 'name', 'clicks');
            //fputcsv($fp, $header); 
            
            foreach ($pointsraw as $point) {
                fputcsv($fp, $point);
            }
            fclose($fp);
            
            return $app['twig']->render('csv3.twig', array(
                    
            )); 
        }

        // Get all players to a csv file with all of their data fields
        public function getAllPlayersToCsv(Application $app) {
            $fp = fopen('/players.csv','w');
            $pDAO = new PlayerDAO();
            $players = $pDAO->getAllPlayers($app);

            foreach ($players as $player) {
                fputcsv($fp, $player);
            }
            fclose($fp);   
              
            return $app['twig']->render('csv2.twig', array(
                    
            ));
        }

        public function showMainMenu(Application $app) {

            $imDAO = new ImageDAO();
            $images = $imDAO->getAllImages($app);
            $pointsraw = $imDAO->getAllClicks($app);
            $pDAO = new PlayerDAO();
            $players = $pDAO->getAllPlayers($app);

            return $app['twig']->render('list.twig', array(
                    'images' => $images,
                    'players' => $players
            ));        

        }

        public function showPlayerAdminMenu(Application $app, $id) {

            $imDAO = new ImageDAO();
            $images = $imDAO->getAllPlayerImages($app, $id);

            $pDAO = new PlayerDAO();
            $player = $pDAO->getPlayerById($app, $id);

            $f = new RandomLib\Factory();
            $g = $f->getGenerator(new SecurityLib\Strength(SecurityLib\Strength::MEDIUM));
            $token = $g->generateString(64);
            $app['session']->set($this->TOKEN_BAN_UNBAN, $token);

            return $app['twig']->render('admin_player.twig', array(
                    'player' => $player,
                    'images' => $images,
                    'token' => $token
            ));

        }

        public function userOp(Application $app, Request $req) {
            // Get (Cross Site Request Forgery) CSRF security token
            $t = $req->get('t');
            // Session key for session
            $t_s = $app['session']->get($this->TOKEN_BAN_UNBAN);
            
            // If Session key and CSRF security token dont match return err 400
            if($t != $t_s) {
                $app->abort(400, "Invalid request");
            }

            var_dump($t);
            var_dump($t_s);
            
            // Create new PlayerDAO
            $pDAO = new PlayerDAO();
            
            // Store operational status and users id from the request
            $op = $req->get('op');
            $user_id = $req->get('id');

            // Update user's PlayerDAO according request's operational status and id
            $pDAO->userOp($app, $op, $user_id);

            return "";


        }
        
        
        /* Get the timestamps of all clicks of each player */
        public function getTimestampsCSV(Application $app) {
            $pDAO = new PlayerDAO();
            $players = $pDAO->getAllPlayers($app);
            $imDAO = new ImageDAO();
            
            // Input .csv file
            $fp = fopen('/stamps.csv','w');
            foreach ($players as $player) {
                $stamps = $imDAO->getAllStampsUser($app, $player['id']);
                foreach ($stamps as $stamp) {
                    fputcsv($fp, $stamp);
                }
            }
            fclose($fp);
            
            return $app['twig']->render('csv5.twig', array(
                    
            ));
        }
    
}

 